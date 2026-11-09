<?php

namespace App\Xelqun\Workspace\Widgets;

use App\Models\Task;
use App\Xelqun\Workspace\Resources\TaskResource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Xelqun\Panels\Widgets\ListWidget;

/**
 * @extends ListWidget<Task>
 */
class DueSoon extends ListWidget
{
    protected static int $sort = 3;

    protected static int $columnSpan = 8;

    protected static ?string $heading = 'Due soon';

    public function query(): Builder
    {
        return Task::query()->with(['project', 'assignee'])->where('status', '!=', 'done')->whereNotNull('due_on')->orderBy('due_on');
    }

    protected function title(Model $record): string
    {
        return $record->title;
    }

    protected function meta(Model $record): string
    {
        return implode(' · ', array_filter([$record->project->name, $record->assignee->name ?? 'Unassigned', $record->due_on?->format('M j')]));
    }

    protected function icon(Model $record): string
    {
        return 'clock';
    }

    protected function iconColor(Model $record): string
    {
        return $record->due_on?->isPast() ? 'danger' : 'gray';
    }

    protected function getSeeAllUrl(): string
    {
        return TaskResource::getUrl();
    }
}
