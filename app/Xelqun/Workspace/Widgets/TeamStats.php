<?php

namespace App\Xelqun\Workspace\Widgets;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Xelqun\Panels\Panels;
use Xelqun\Panels\Widgets\Stat;
use Xelqun\Panels\Widgets\StatsOverview;

/**
 * Plain queries, kept to the current team by the panel.
 */
class TeamStats extends StatsOverview
{
    protected static int $sort = 1;

    protected function getStats(): array
    {
        $open = Task::query()->where('status', '!=', 'done');

        return [
            Stat::make('Active projects', Project::query()->where('status', 'active')->count())->icon('folder-tree')->description(Project::query()->where('status', 'planning')->count().' in planning'),
            Stat::make('Open tasks', (clone $open)->count())->icon('layers')->description((clone $open)->where('status', 'doing')->count().' in progress'),
            Stat::make('Overdue', (clone $open)->whereDate('due_on', '<', today())->count())->icon('clock')->description('Past their due date'),
            Stat::make('Members', User::query()->count())->icon('users')->description('On '.app(Panels::class)->tenant()?->getAttribute('name')),
        ];
    }
}
