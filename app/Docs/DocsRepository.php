<?php

namespace App\Docs;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

/**
 * Finds docs pages and how they link together. `config/docs.php` holds the order,
 * so a file that's not listed there is not a page.
 */
final class DocsRepository
{
    public const string OVERVIEW = 'overview';

    public const string INTRODUCTION = 'introduction';

    private const int MAX_SEARCH_RESULTS = 12;

    public function __construct(private readonly MarkdownRenderer $renderer) {}

    /**
     * @return array<string, array{label: string, tagline: string, icon: string, popular: list<string>, navigation: array<string, array<string, string>>}>
     */
    public function packages(): array
    {
        return config('docs.packages');
    }

    /**
     * @return array<string, string> Each page's sidebar label, by slug, in order.
     */
    public function pages(string $package): array
    {
        return array_merge(...array_values($this->packages()[$package]['navigation']));
    }

    public function url(string $package, string $slug = self::OVERVIEW): string
    {
        return route('docs.show', ['package' => $package, 'page' => $slug === self::OVERVIEW ? null : $slug]);
    }

    public function path(string $package, string $slug): string
    {
        return resource_path("docs/{$package}/{$slug}.md");
    }

    /**
     * @return array{title: string, description: string, blocks: list<array<string, string>>, headings: list<array{id: string, text: string, level: int}>, text: string}|null
     */
    public function find(string $package, string $slug): ?array
    {
        $path = $this->path($package, $slug);

        if (! isset($this->pages($package)[$slug]) || ! is_file($path)) {
            return null;
        }

        // The renderer is part of the key, because a change there changes every page.
        return Cache::rememberForever(
            "docs.{$package}.{$slug}.".filemtime($path).'.'.filemtime(app_path('Docs/MarkdownRenderer.php')),
            fn (): array => $this->renderer->render((string) file_get_contents($path)),
        );
    }

    /**
     * @return list<array{label: string, items: list<array{label: string, url: string, active: bool}>}>
     */
    public function navigation(string $package, string $activeSlug): array
    {
        $groups = [];

        foreach ($this->packages()[$package]['navigation'] as $label => $pages) {
            $items = [];

            foreach ($pages as $slug => $pageLabel) {
                $items[] = ['label' => $pageLabel, 'url' => $this->url($package, $slug), 'active' => $slug === $activeSlug];
            }

            $groups[] = ['label' => $label, 'items' => $items];
        }

        return $groups;
    }

    /**
     * The whole sidebar: every section, with the page being read marked.
     *
     * @return list<array{key: string, label: string, icon: string, groups: list<array{label: string, items: list<array{label: string, url: string, active: bool}>}>}>
     */
    public function sections(string $activePackage, string $activeSlug): array
    {
        $sections = [];

        foreach ($this->packages() as $package => $definition) {
            $sections[] = [
                'key' => $package,
                'label' => $definition['label'],
                'icon' => $definition['icon'],
                'groups' => $this->navigation($package, $package === $activePackage ? $activeSlug : ''),
            ];
        }

        return $sections;
    }

    /**
     * @return array{previous: array{label: string, url: string}|null, next: array{label: string, url: string}|null}
     */
    public function neighbours(string $package, string $slug): array
    {
        $pages = $this->pages($package);
        $slugs = array_keys($pages);
        $position = (int) array_search($slug, $slugs, true);

        $link = fn (?string $neighbour): ?array => $neighbour === null ? null : ['label' => $pages[$neighbour], 'url' => $this->url($package, $neighbour)];

        return [
            'previous' => $link($slugs[$position - 1] ?? null),
            'next' => $link($slugs[$position + 1] ?? null),
        ];
    }

    /**
     * Whichever group of the sidebar a page sits in, for its breadcrumbs.
     */
    public function group(string $package, string $slug): string
    {
        foreach ($this->packages()[$package]['navigation'] as $label => $pages) {
            if (isset($pages[$slug])) {
                return $label;
            }
        }

        return '';
    }

    /**
     * A page's title counts most, then a heading, then its text.
     *
     * @return list<array{title: string, section: string, package: string, url: string}>
     */
    public function search(string $query): array
    {
        $needle = Str::lower(trim($query));

        if (mb_strlen($needle) < 2) {
            return [];
        }

        $matches = [];

        foreach ($this->index() as $entry) {
            $score = match (true) {
                Str::lower($entry['title']) === $needle => 4,
                Str::contains(Str::lower($entry['title']), $needle) => 3,
                Str::contains(Str::lower($entry['section']), $needle) => 2,
                Str::contains($entry['text'], $needle) => 1,
                default => 0,
            };

            if ($score > 0) {
                $matches[] = [...$entry, 'score' => $score];
            }
        }

        usort($matches, fn (array $first, array $second): int => $second['score'] <=> $first['score']);

        return array_map(
            fn (array $match): array => ['title' => $match['title'], 'section' => $match['section'], 'package' => $match['package'], 'url' => $match['url']],
            array_slice($matches, 0, self::MAX_SEARCH_RESULTS),
        );
    }

    /**
     * What to show before anything is typed: the package being read comes first.
     *
     * @return list<array{title: string, section: string, package: string, url: string}>
     */
    public function suggestions(?string $currentPackage = null): array
    {
        $packages = $this->packages();
        $packages = array_intersect_key($packages, [(string) $currentPackage => true]) + $packages;

        $suggestions = [];

        foreach ($packages as $package => $definition) {
            foreach ($definition['popular'] as $slug) {
                $suggestions[] = [
                    'title' => $this->pages($package)[$slug],
                    'section' => '',
                    'package' => "{$definition['label']} · {$this->group($package, $slug)}",
                    'url' => $this->url($package, $slug),
                ];
            }
        }

        return $suggestions;
    }

    /**
     * One entry for each page and each of its headings.
     *
     * @return list<array{title: string, section: string, package: string, url: string, text: string}>
     */
    private function index(): array
    {
        $entries = [];

        foreach ($this->packages() as $package => $definition) {
            foreach (array_keys($this->pages($package)) as $slug) {
                $page = $this->find($package, $slug);

                if ($page === null) {
                    continue;
                }

                $url = $this->url($package, $slug);
                $entries[] = ['title' => $page['title'], 'section' => '', 'package' => $definition['label'], 'url' => $url, 'text' => Str::lower($page['text'])];

                foreach ($page['headings'] as $heading) {
                    $entries[] = ['title' => $page['title'], 'section' => $heading['text'], 'package' => $definition['label'], 'url' => "{$url}#{$heading['id']}", 'text' => ''];
                }
            }
        }

        return $entries;
    }
}
