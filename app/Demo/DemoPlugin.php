<?php

namespace App\Demo;

use App\Models\User;
use Xelqun\Panels\Contracts\Plugin;
use Xelqun\Panels\Navigation\NavigationItem;
use Xelqun\Panels\Panel;
use Xelqun\Panels\View\RenderHook;

/**
 * What the demo adds around a panel, written as a plugin so the demo shows one.
 */
class DemoPlugin implements Plugin
{
    public function getId(): string
    {
        return 'demo';
    }

    public function register(Panel $panel): void
    {
        $isWorkspace = $panel->getId() === 'workspace';

        $panel
            ->renderHook(RenderHook::SidebarEnd, 'demo/sidebar-card', fn (): array => [
                'docsUrl' => $this->docsUrl(),
                'inboxUrl' => route('demo.inbox'),
                'githubUrl' => config('xelqun.github_url'),
            ])
            ->renderHook(RenderHook::ContentEnd, 'demo/appearance-switcher', fn (): array => DemoAppearance::props())
            ->renderHook(RenderHook::AuthFormAfter, 'demo/login-hint', fn (): array => [
                'email' => User::DEMO_EMAIL,
                'password' => User::DEMO_PASSWORD,
                'inboxUrl' => route('demo.inbox'),
            ])
            ->navigationItems([
                $isWorkspace
                    ? NavigationItem::make('Back to the store')
                        ->icon('shopping-cart')
                        ->group('Showcase')
                        ->sort(80)
                        ->url(fn (): string => route('xelqun-panels.admin.pages.home'))
                    : NavigationItem::make('Tenancy')
                        ->icon('users')
                        ->group('Showcase')
                        ->sort(80)
                        ->url(fn (): string => route('xelqun-panels.workspace.tenant.redirect')),
                NavigationItem::make('Documentation')
                    ->icon('file-text')
                    ->group('Showcase')
                    ->sort(90)
                    ->url(fn (): string => $this->docsUrl($isWorkspace ? '/panels/tenancy' : '/panels')),
            ]);
    }

    public function boot(Panel $panel): void {}

    private function docsUrl(string $path = '/panels'): string
    {
        return request()->getScheme().'://'.config('xelqun.site_domain').$path;
    }
}
