<?php

use App\Demo\DemoAppearance;
use App\Docs\DocsRepository;
use App\Models\DemoMail;
use App\Models\Product;
use App\Models\User;
use App\Xelqun\Admin\Resources\UserResource;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Inertia\Testing\AssertableInertia as Assert;
use Xelqun\Forms\Support\StatePath;
use Xelqun\Panels\Support\Colors\Color;

// Not RefreshDatabase: its outer transaction stops the reset turning off SQLite's foreign key checks.
uses(DatabaseMigrations::class);

const DEMO_URL = 'http://demo.xelqun.test';

beforeEach(function (): void {
    Storage::fake('public');
    Storage::fake('demo');
    Artisan::call('demo:reset');
});

function demoUser(): User
{
    return User::query()->where('email', User::DEMO_EMAIL)->firstOrFail();
}

it('only serves the panels on the demo host', function (): void {
    $this->get(DEMO_URL.'/login')->assertOk();
    $this->get('http://xelqun.test/login')->assertNotFound();
});

it('only serves the docs on the site host', function (): void {
    $this->get('http://xelqun.test/forms')->assertOk();
    $this->get(DEMO_URL.'/forms')->assertNotFound();
    $this->get(DEMO_URL.'/sitemap.xml')->assertNotFound();
});

it('keeps the demo out of search engines, and not the docs', function (): void {
    $this->get(DEMO_URL.'/login')->assertHeader('X-Robots-Tag', 'noindex, nofollow');
    $this->get('http://xelqun.test/forms')->assertHeaderMissing('X-Robots-Tag');
});

it('fills in the login form with the demo account', function (): void {
    $this->get(DEMO_URL.'/login')->assertInertia(fn (Assert $page) => $page
        ->where('page.form.state.'.StatePath::ROOT.'.email', User::DEMO_EMAIL)
        ->where('page.form.state.'.StatePath::ROOT.'.password', User::DEMO_PASSWORD));
});

it('signs in with the prefilled credentials', function (): void {
    $this->post(DEMO_URL.'/login', ['data' => ['email' => User::DEMO_EMAIL, 'password' => User::DEMO_PASSWORD]])
        ->assertRedirect(DEMO_URL);

    $this->assertAuthenticatedAs(demoUser());
});

it('puts everything back after a reset, with the same ids', function (): void {
    $product = Product::query()->findOrFail(7);
    $name = $product->name;
    $demoId = demoUser()->id;

    Product::query()->delete();
    User::query()->whereKeyNot($demoId)->delete();

    Artisan::call('demo:reset');

    expect(Product::query()->count())->toBe(120)
        ->and(Product::query()->findOrFail(7)->name)->toBe($name)
        ->and(demoUser()->id)->toBe($demoId)
        ->and($demoId)->toBe(1);
});

it('keeps sessions through a reset', function (): void {
    DB::table('sessions')->insert(['id' => 'kept', 'payload' => '', 'last_activity' => time()]);

    Artisan::call('demo:reset');

    expect(DB::table('sessions')->where('id', 'kept')->exists())->toBeTrue();
});

it('clears what visitors uploaded', function (): void {
    Storage::disk('public')->put('demo/gallery/visitor.png', 'x');
    Storage::disk('demo')->put('visitor.txt', 'x');

    Artisan::call('demo:reset');

    Storage::disk('public')->assertMissing('demo/gallery/visitor.png');
    Storage::disk('public')->assertExists('demo/products/keyboards.svg');
    Storage::disk('demo')->assertMissing('visitor.txt');
});

it('runs the reset every ten minutes', function (): void {
    $this->artisan('schedule:list')->expectsOutputToContain('demo:reset')->assertSuccessful();

    $event = collect(app(Schedule::class)->events())
        ->first(fn ($event): bool => str_contains((string) $event->command, 'demo:reset'));

    expect($event->expression)->toBe('*/10 * * * *')
        ->and($event->withoutOverlapping)->toBeTrue();
});

it('refuses changes to the shared account', function (array $change): void {
    demoUser()->forceFill($change)->save();
})->with([
    'name' => [['name' => 'Mallory']],
    'email' => [['email' => 'mallory@example.com']],
    'password' => [['password' => 'stolen']],
    'email codes' => [['has_email_authentication' => true]],
    'an authenticator' => [['app_authentication_secret' => 'SECRET']],
])->throws(ValidationException::class, 'shared demo account');

it('shows a refusal on a field the screen has', function (string $field, Closure $change): void {
    try {
        $change(demoUser());

        $this->fail('The change should have been refused.');
    } catch (ValidationException $exception) {
        expect(array_keys($exception->errors()))->toBe(["data.{$field}"]);
    }
})->with([
    'the password form' => ['password', fn (User $user) => $user->forceFill(['password' => 'stolen'])->save()],
    'the two-factor dialog' => ['code', fn (User $user) => $user->saveAppAuthentication('SECRET', ['hash'])],
    'the email codes dialog' => ['current_password', fn (User $user) => $user->saveEmailAuthentication(true)],
    'the profile form' => ['email', fn (User $user) => $user->forceFill(['name' => 'Mallory'])->save()],
]);

it('refuses to delete the shared account', function (): void {
    demoUser()->delete();
})->throws(ValidationException::class, 'shared demo account');

it('lets a registered account change itself', function (): void {
    $user = User::factory()->create();

    $user->update(['name' => 'Someone else']);
    $user->delete();

    expect(User::query()->whereKey($user->id)->exists())->toBeFalse();
});

it('never lists another visitor\'s address', function (): void {
    $visitor = User::factory()->create(['email' => 'real.person@gmail.com']);
    $other = User::factory()->create(['email' => 'another.person@gmail.com']);

    $this->actingAs($visitor);

    $emails = UserResource::getEloquentQuery()->pluck('email');

    expect($emails)->toContain('real.person@gmail.com')
        ->and($emails)->not->toContain($other->email);
});

it('registers a visitor and asks them to verify, with the mail in their inbox', function (): void {
    $this->post(DEMO_URL.'/register', ['data' => [
        'name' => 'A Visitor',
        'email' => 'visitor@example.org',
        'password' => 'a-Long-Password-1!',
        'password_confirmation' => 'a-Long-Password-1!',
    ]])->assertRedirect();

    $visitor = User::query()->where('email', 'visitor@example.org')->sole();

    $this->assertAuthenticatedAs($visitor);
    $this->get(DEMO_URL)->assertRedirect(DEMO_URL.'/email/verify');

    expect(DemoMail::query()->sole())
        ->recipient->toBe('visitor@example.org')
        ->html->toContain('/email/verify/');
});

it('lets the demo account straight in, as it is verified', function (): void {
    $this->actingAs(demoUser())->get(DEMO_URL)->assertOk();
});

it('catches mail instead of sending it', function (): void {
    $this->post(DEMO_URL.'/forgot-password', ['data' => ['email' => User::DEMO_EMAIL]])->assertRedirect();

    $mail = DemoMail::query()->sole();

    expect($mail->recipient)->toBe(User::DEMO_EMAIL)
        ->and($mail->html)->toContain('reset-password')
        ->and(Mail::mailer()->getSymfonyTransport()->messages())->toHaveCount(0);
});

it('only shows you your own mail', function (): void {
    DemoMail::query()->create(['inbox' => 'mine', 'recipient' => 'a@example.com', 'subject' => 'Mine', 'html' => '<p>Hi</p>']);
    DemoMail::query()->create(['inbox' => 'theirs', 'recipient' => 'b@example.com', 'subject' => 'Theirs', 'html' => '<p>Hi</p>']);

    $this->withSession(['demo.inbox' => 'mine'])->get(DEMO_URL.'/inbox')->assertInertia(fn (Assert $page) => $page
        ->component('demo/Inbox')
        ->has('mails', 1)
        ->where('mails.0.subject', 'Mine'));
});

it('shows nothing in the inbox of a new visitor', function (): void {
    DemoMail::query()->create(['inbox' => 'theirs', 'recipient' => 'b@example.com', 'subject' => 'Theirs', 'html' => '<p>Hi</p>']);

    $this->get(DEMO_URL.'/inbox')->assertInertia(fn (Assert $page) => $page->has('mails', 0));
});

it('loads every preview a docs page asks for', function (): void {
    foreach (File::allFiles(resource_path('docs')) as $file) {
        preg_match_all('/<!-- example: ([\w-]+) -->/', $file->getContents(), $matches);

        foreach ($matches[1] as $name) {
            $this->getJson("http://xelqun.test/examples/{$name}")
                ->assertOk()
                ->assertJsonStructure(['kind', 'title', 'schema', 'submits', 'source']);
        }
    }
});

it('previews each field, layout, column and filter on its own page', function (): void {
    $withoutPreview = ['forms/fields/hidden.md'];
    $missing = [];

    foreach (['forms/fields', 'forms/layouts', 'forms/actions', 'tables/columns', 'tables/filters', 'tables/actions'] as $folder) {
        foreach (File::files(resource_path("docs/{$folder}")) as $file) {
            $path = "{$folder}/{$file->getFilename()}";

            if (! str_contains($file->getContents(), '<!-- example:') && ! in_array($path, $withoutPreview, true)) {
                $missing[] = $path;
            }
        }
    }

    expect($missing)->toBe([]);
});

it('has both themes of every screenshot', function (): void {
    $missing = [];

    foreach (File::allFiles(resource_path('docs')) as $file) {
        preg_match_all('/<!-- screenshot: ([\w-]+) \| .+? -->/', $file->getContents(), $matches);

        foreach ($matches[1] as $name) {
            foreach (['light', 'dark'] as $theme) {
                if (! is_file(public_path("images/docs/panels/{$name}-{$theme}.jpg"))) {
                    $missing[] = "{$name}-{$theme}";
                }
            }
        }
    }

    expect($missing)->toBe([]);
});

it('never shows a screenshot bigger than it was captured', function (): void {
    $this->get('http://xelqun.test/panels/widgets/lists')->assertOk();

    $html = collect(app(DocsRepository::class)->find('panels', 'navigation')['blocks'])->pluck('html')->implode('');
    [$capturedWidth] = getimagesize(public_path('images/docs/panels/sidebar-light.jpg'));

    expect($html)->toContain('width="'.intdiv($capturedWidth, 2).'"');
});

it('refuses a preview that does not exist', function (string $name): void {
    $this->getJson("http://xelqun.test/examples/{$name}")->assertNotFound();
})->with(['form-nope', 'table-nope', 'actions-nope', 'nope']);

it('checks a live example without saving it', function (): void {
    $this->post('http://xelqun.test/examples/basic-fields', ['data' => []])->assertSessionHasErrors();
    $this->post('http://xelqun.test/examples/nope', [])->assertNotFound();
});

it('opens every screen the docs link to', function (): void {
    $paths = collect();

    foreach (File::allFiles(resource_path('docs')) as $file) {
        preg_match_all('/\]\(demo:([^)#]*)\)/', $file->getContents(), $matches);
        $paths = $paths->merge($matches[1]);
    }

    $this->actingAs(demoUser());

    foreach ($paths->unique() as $path) {
        $response = $this->get(DEMO_URL.$path);

        expect($response->status())->toBeIn([200, 302], "{$path} returned {$response->status()}");
    }
});

it('documents every package class', function (): void {
    $docs = collect(File::allFiles(resource_path('docs')))->map->getContents()->implode("\n");
    $missing = [];

    $folders = [
        'forms/src/Components' => ['Component', 'Field', 'Toggleable'],
        'forms/src/Layouts' => ['Layout'],
        'tables/src/Columns' => ['Column'],
        'tables/src/Filters' => ['BaseFilter', 'TableFiltersForm'],
        'tables/src/Filters/Constraints' => ['Constraint'],
        'tables/src/Summarizers' => ['Summarizer'],
        'panels/src/Actions' => ['ChoosesRelatedRecord'],
        'panels/src/Infolists' => ['Entry'],
        'panels/src/Widgets' => ['Stat', 'Tile', 'Widget', 'PanelWidget', 'HasWidgetSettings'],
    ];

    foreach ($folders as $folder => $abstract) {
        foreach (File::files(base_path("vendor/xelqun/{$folder}")) as $file) {
            $class = $file->getFilenameWithoutExtension();

            if (! in_array($class, $abstract, true) && ! str_contains($docs, $class)) {
                $missing[] = "{$folder}/{$class}";
            }
        }
    }

    expect($missing)->toBe([]);
});

it('keeps a choice of colors for the visitor who made it', function (): void {
    $this->post(DEMO_URL.'/_demo/appearance', ['primary' => 'Rose', 'gray' => 'Stone'])->assertRedirect();

    expect(session('demo.primary'))->toBe('Rose')
        ->and(session('demo.gray'))->toBe('Stone')
        ->and(DemoAppearance::colors())->toBe(['primary' => Color::Rose, 'gray' => Color::Stone]);
});

it('goes back to the default primary', function (): void {
    $this->withSession(['demo.primary' => 'Rose'])->post(DEMO_URL.'/_demo/appearance', ['primary' => null, 'gray' => 'Zinc']);

    expect(DemoAppearance::colors()['primary'])->toBe('#0071e3');
});

it('only takes a neutral as the gray, and a color as the primary', function (array $choice, string $field): void {
    $this->post(DEMO_URL.'/_demo/appearance', $choice)->assertSessionHasErrors($field);
})->with([
    'a color as the gray' => [['primary' => 'Rose', 'gray' => 'Rose'], 'gray'],
    'a gray as the primary' => [['primary' => 'Zinc', 'gray' => 'Zinc'], 'primary'],
    'something else' => [['gray' => 'red;}body{display:none'], 'gray'],
]);

it('has no checklist or appearance page any more', function (string $path): void {
    $this->actingAs(demoUser())->get(DEMO_URL.$path)->assertNotFound();
})->with(['/feature-checklist', '/appearance']);
