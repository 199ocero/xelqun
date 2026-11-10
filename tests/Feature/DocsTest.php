<?php

use App\Docs\DocsRepository;
use App\Docs\MarkdownRenderer;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Inertia\Testing\AssertableInertia as Assert;

/**
 * @return array<int, array{0: string, 1: string}>
 */
function docsPages(): array
{
    $pages = [];

    foreach (config('docs.packages') as $package => $definition) {
        foreach ($definition['navigation'] as $group) {
            foreach (array_keys($group) as $slug) {
                $pages[] = [$package, $slug];
            }
        }
    }

    return $pages;
}

/**
 * @return array<string, string> Each docs file's Markdown, by its path.
 */
function docsSources(): array
{
    return collect(File::allFiles(resource_path('docs')))
        ->mapWithKeys(fn ($file): array => [$file->getRelativePathname() => $file->getContents()])
        ->all();
}

/**
 * @return list<string> The PHP samples of one page.
 */
function phpSamples(string $markdown): array
{
    preg_match_all('/```php\n(.*?)```/s', $markdown, $matches);

    return $matches[1];
}

/**
 * Every public method in the packages, and on the Laravel classes that samples call.
 *
 * @return array<string, true>
 */
function knownMethods(): array
{
    $methods = [];

    foreach (['forms', 'tables', 'panels'] as $package) {
        foreach (File::allFiles(base_path("vendor/xelqun/{$package}")) as $file) {
            if ($file->getExtension() !== 'php' || str_contains($file->getPathname(), '/tests/')) {
                continue;
            }

            preg_match_all('/function\s+&?(\w+)\(/', $file->getContents(), $matches);

            foreach ($matches[1] as $method) {
                $methods[$method] = true;
            }
        }
    }

    return $methods;
}

it('renders every page in the navigation', function (): void {
    foreach (docsPages() as [$package, $slug]) {
        $url = app(DocsRepository::class)->url($package, $slug);

        $this->get($url)
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('docs/Show')
                ->where('document.canonical', $url)
                ->whereType('document.title', 'string')
                ->whereNot('document.title', '')
                ->whereNot('document.description', ''));
    }
});

it('shows the packages and their real page counts on the landing page', function (): void {
    $this->get('/')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Home')
            ->has('packages', 3)
            ->where('packages.0.label', 'Forms')
            ->where('packages.0.pages', count(app(DocsRepository::class)->pages('forms'))));
});

it('gives the landing page a canonical address and structured data', function (): void {
    $this->get('/')
        ->assertSee('"@type":"SoftwareSourceCode"', escape: false)
        ->assertInertia(fn (Assert $page) => $page->where('canonical', url('/')));
});

it('shows the repository star count', function (): void {
    Http::fake(['api.github.com/repos/199ocero/xelqun' => Http::response(['stargazers_count' => 1234])]);

    $this->get('/')->assertInertia(fn (Assert $page) => $page->where('links.githubStars', 1234));
});

it('hides the star count when GitHub cannot be reached', function (): void {
    Http::fake(['api.github.com/*' => Http::response(status: 500)]);

    $this->get('/')->assertInertia(fn (Assert $page) => $page->where('links.githubStars', false));
});

it('shows every section in one sidebar, with the current page marked', function (): void {
    $this->get('/tables/columns/text')->assertInertia(fn (Assert $page) => $page
        ->where('docs.url', app(DocsRepository::class)->url('tables'))
        ->where('docs.sections', fn ($sections): bool => collect($sections)->pluck('key')->all() === ['introduction', 'forms', 'tables', 'panels']
            && collect($sections)->flatMap(fn (array $section): array => collect($section['groups'])->flatMap(fn (array $group): array => $group['items'])->all())->where('active', true)->pluck('label')->all() === ['Text']));
});

it('has no docs file missing from the navigation', function (): void {
    $listed = collect(docsPages())->map(fn (array $page): string => "{$page[0]}/{$page[1]}.md");

    expect(array_keys(docsSources()))->each(fn ($path) => $path->toBeIn($listed->all()));
});

it('sends a package overview to one address', function (): void {
    $this->get('/forms/overview')->assertRedirect('/forms')->assertStatus(301);
});

it('returns a 404 for pages that do not exist', function (string $url): void {
    $this->get($url)->assertNotFound();
})->with(['/forms/nope', '/widgets', '/forms/../.env']);

it('lists every page in the sitemap', function (): void {
    $response = $this->get('/sitemap.xml')->assertOk()->assertHeader('Content-Type', 'application/xml');

    foreach (docsPages() as [$package, $slug]) {
        $response->assertSee(e(app(DocsRepository::class)->url($package, $slug)));
    }
});

it('finds pages and headings', function (): void {
    $results = collect($this->getJson('/search?q=repeater')->assertOk()->json('results'));

    expect($results)->not->toBeEmpty()
        ->and($results->first()['title'])->toBe('Repeater');
});

it('suggests pages before anything is typed, the current package first', function (): void {
    $results = collect($this->getJson('/search?package=tables')->assertOk()->json('results'));

    expect($results)->not->toBeEmpty()
        ->and($results->first()['package'])->toBe('Tables · Getting started')
        ->and($results->pluck('url')->map(fn (string $url): string => explode('/', $url)[3])->unique()->values()->all())->toBe(['tables', 'introduction', 'forms', 'panels']);
});

it('ignores searches that are too short', function (): void {
    $this->getJson('/search?q=a')->assertOk()->assertExactJson(['results' => []]);
});

it('only imports classes that exist', function (): void {
    $missing = [];

    foreach (docsSources() as $path => $markdown) {
        foreach (phpSamples($markdown) as $sample) {
            preg_match_all('/^use (Xelqun\\\\[\w\\\\]+);/m', $sample, $matches);

            foreach ($matches[1] as $class) {
                if (! class_exists($class) && ! interface_exists($class) && ! enum_exists($class) && ! trait_exists($class)) {
                    $missing[] = "{$path}: {$class}";
                }
            }
        }
    }

    expect($missing)->toBe([]);
});

it('only calls methods that exist', function (): void {
    $known = knownMethods();
    $laravel = ['where', 'whereNotNull', 'orWhere', 'whereDate', 'whereKey', 'whereBelongsTo', 'when', 'orderBy', 'with', 'count', 'pluck', 'all', 'update', 'create', 'fresh', 'send', 'to', 'id', 'user', 'can', 'addYear', 'hourly', 'decimal', 'timestamps', 'is', 'assertOk', 'throws', 'toBe', 'isManager', 'isAdmin', 'ship', 'teams', 'users', 'assertJsonPath', 'assertRedirect', 'actingAs', 'post', 'toBeTrue', 'toBeNull', 'belongsTo', 'belongsToMany', 'attach', 'name', 'foreignId', 'constrained', 'cascadeOnDelete', 'primary', 'chunkById', 'archive', 'singletonIf', 'defaults', 'route', 'parameter', 'firstOrFail', 'toBase', 'getCountForPagination', 'paginate', 'getCollection', 'map', 'mapWithKeys', 'currentPage', 'total', 'keyBy'];
    $unknown = [];

    foreach (docsSources() as $path => $markdown) {
        foreach (phpSamples($markdown) as $sample) {
            preg_match_all('/->(\w+)\(/', $sample, $matches);

            foreach (array_unique($matches[1]) as $method) {
                if (! isset($known[$method]) && ! in_array($method, $laravel, true)) {
                    $unknown[] = "{$path}: {$method}()";
                }
            }
        }
    }

    expect($unknown)->toBe([]);
});

it('lists the installed package versions on the version support page', function (): void {
    $markdown = File::get(resource_path('docs/introduction/version-support.md'));

    foreach (json_decode(File::get(base_path('composer.lock')), true)['packages'] as $package) {
        if (preg_match('#^xelqun/(\w+)$#', $package['name'], $name)) {
            $minor = implode('.', array_slice(explode('.', ltrim($package['version'], 'v')), 0, 2));

            expect($markdown)->toMatch('#\['.ucfirst($name[1]).'\]\([^)]+\)\s*\|\s*'.preg_quote($minor).'\s*\|#');
        }
    }
});

it('only links to pages that exist', function (): void {
    $pages = collect(docsPages())->map(fn (array $page): string => app(DocsRepository::class)->url($page[0], $page[1]));
    $broken = [];

    foreach (docsSources() as $path => $markdown) {
        preg_match_all('/\]\((\/(?:forms|tables|panels)[^)#]*)/', $markdown, $matches);

        foreach ($matches[1] as $link) {
            if (! $pages->contains(url($link))) {
                $broken[] = "{$path}: {$link}";
            }
        }
    }

    expect($broken)->toBe([]);
});

it('puts animated diagrams between the HTML of a page', function (): void {
    $blocks = app(MarkdownRenderer::class)->render("Before\n\n<!-- flow: overview -->\n\nBetween\n\n<!-- example: products -->\n\nAfter")['blocks'];

    expect(array_column($blocks, 'type'))->toBe(['html', 'flow', 'html', 'example', 'html'])
        ->and($blocks[1]['name'])->toBe('overview');
});

it('only shows animated diagrams that exist', function (): void {
    preg_match_all("/^    '?([\w-]+)'?: \{$/m", File::get(resource_path('js/components/docs/flows.ts')), $defined);
    $missing = [];

    foreach (docsSources() as $path => $markdown) {
        preg_match_all('/<!--\s*flow:\s*([\w\/-]+)\s*-->/', $markdown, $used);

        foreach (array_diff($used[1], $defined[1]) as $name) {
            $missing[] = "{$path}: {$name}";
        }
    }

    expect($defined[1])->not->toBeEmpty()
        ->and($missing)->toBe([]);
});
