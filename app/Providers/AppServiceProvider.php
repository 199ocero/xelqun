<?php

namespace App\Providers;

use App\Listeners\CaptureDemoMail;
use Carbon\CarbonImmutable;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();
        $this->configureDemo();
    }

    /**
     * The panels aren't tested with SSR, and nothing behind a login needs it. A
     * file sent to a live example goes to a private disk that the reset clears.
     */
    protected function configureDemo(): void
    {
        Event::listen(MessageSending::class, CaptureDemoMail::class);

        Inertia::disableSsr(fn (): bool => request()->getHost() === config('xelqun.demo_domain'));

        config([
            'xelqun-forms.uploads.disk' => 'demo',
            'xelqun-forms.uploads.temporary_ttl' => 60,
            'xelqun-forms.uploads.max_size' => 4096,
        ]);
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }
}
