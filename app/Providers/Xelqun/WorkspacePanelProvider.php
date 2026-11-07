<?php

namespace App\Providers\Xelqun;

use App\Demo\DemoLoginForm;
use App\Models\Team;
use App\Xelqun\Workspace\Resources\NoteResource;
use App\Xelqun\Workspace\TeamForm;
use Xelqun\Panels\Pages\Dashboard;
use Xelqun\Panels\Panel;
use Xelqun\Panels\PanelProvider;
use Xelqun\Panels\Support\Colors\Color;

/**
 * A second panel, with top navigation and a tenant for each team.
 */
class WorkspacePanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('workspace')
            ->domain(config('xelqun.demo_domain'))
            ->path('workspace')
            ->brandName('Workspace')
            ->colors(['primary' => Color::Emerald])
            ->login(DemoLoginForm::class)
            ->emailVerification()
            ->topNavigation()
            ->tenant(Team::class, slugAttribute: 'slug')
            ->tenantRegistration(TeamForm::class)
            ->tenantProfile(TeamForm::class)
            ->pages([Dashboard::class])
            ->resources([NoteResource::class]);
    }
}
