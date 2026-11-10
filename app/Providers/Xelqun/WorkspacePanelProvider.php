<?php

namespace App\Providers\Xelqun;

use App\Demo\DemoAppearance;
use App\Demo\DemoLoginForm;
use App\Demo\DemoPlugin;
use App\Models\Team;
use App\Xelqun\Workspace\Resources\MemberResource;
use App\Xelqun\Workspace\Resources\NoteResource;
use App\Xelqun\Workspace\Resources\ProjectResource;
use App\Xelqun\Workspace\Resources\TaskResource;
use App\Xelqun\Workspace\TeamForm;
use App\Xelqun\Workspace\Widgets\DueSoon;
use App\Xelqun\Workspace\Widgets\TasksByStatus;
use App\Xelqun\Workspace\Widgets\TeamStats;
use Xelqun\Panels\Navigation\NavigationItem;
use Xelqun\Panels\Pages\Dashboard;
use Xelqun\Panels\Panel;
use Xelqun\Panels\PanelProvider;

/**
 * The same look as the admin panel, split between teams. tenant() is the only
 * line that makes it multi-tenant.
 */
class WorkspacePanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('workspace')
            ->domain(config('xelqun.demo_domain'))
            ->path('workspace')
            ->brandName('Xelqun')
            ->brandLogo('/images/demo/logo-light.svg')
            ->darkModeBrandLogo('/images/demo/logo-dark.svg')
            ->brandLogoHeight('1.75rem')
            ->favicon('/favicon.svg')
            ->font('Inter')
            ->colors(fn (): array => DemoAppearance::colors())
            ->maxContentWidth('7xl')
            ->darkMode()
            ->defaultThemeMode('system')
            ->animations()
            ->breadcrumbs()
            ->globalSearch()
            ->login(DemoLoginForm::class)
            ->emailVerification()
            ->profile()
            ->tenant(Team::class, slugAttribute: 'slug')
            ->tenantRegistration(TeamForm::class)
            ->tenantProfile(TeamForm::class)
            ->tenantMenuItems([
                NavigationItem::make('Team members')->icon('users')->url(fn (): string => MemberResource::getUrl()),
            ])
            ->pages([Dashboard::class])
            ->resources([ProjectResource::class, TaskResource::class, MemberResource::class, NoteResource::class])
            ->widgets([TeamStats::class, TasksByStatus::class, DueSoon::class])
            ->plugin(new DemoPlugin);
    }
}
