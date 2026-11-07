<?php

namespace App\Xelqun\Admin\Widgets;

use App\Models\Order;
use Xelqun\Panels\Widgets\ChartWidget;

class OrdersChart extends ChartWidget
{
    protected static int $sort = 2;

    protected static int $columnSpan = 8;

    protected static ?string $heading = 'Order value by delivery week';

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        $weeks = collect(range(0, 5))->map(fn (int $week) => now()->addWeeks($week)->startOfWeek());

        $totalFor = fn (string $type) => $weeks->map(fn ($week): float => (float) Order::query()
            ->where('type', $type)
            ->whereBetween('delivery_date', [$week, $week->copy()->endOfWeek()])
            ->sum('total'))->all();

        return [
            'labels' => $weeks->map(fn ($week): string => $week->format('M j'))->all(),
            'datasets' => [
                ['label' => 'Individual', 'data' => $totalFor('individual')],
                ['label' => 'Business', 'data' => $totalFor('business')],
            ],
        ];
    }
}
