<?php

namespace App\Xelqun\Workspace\Widgets;

use App\Models\Task;
use Xelqun\Panels\Widgets\ChartWidget;

class TasksByStatus extends ChartWidget
{
    protected static int $sort = 2;

    protected static int $columnSpan = 4;

    protected static ?string $heading = 'Tasks by status';

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getData(): array
    {
        $counts = Task::query()->toBase()->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status');

        return [
            'labels' => $counts->keys()->map(fn (string $status): string => Task::STATUSES[$status] ?? $status)->all(),
            'datasets' => [['data' => $counts->values()->all()]],
        ];
    }
}
