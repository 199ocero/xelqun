<?php

namespace App\Xelqun\Admin\Widgets;

use App\Models\Order;
use Xelqun\Panels\Widgets\ChartWidget;

class OrdersByType extends ChartWidget
{
    protected static int $sort = 2;

    protected static int $columnSpan = 4;

    protected static ?string $heading = 'Orders by customer';

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getData(): array
    {
        $counts = Order::query()->toBase()->selectRaw('type, count(*) as total')->groupBy('type')->pluck('total', 'type');

        return [
            'labels' => $counts->keys()->map(ucfirst(...))->all(),
            'datasets' => [['data' => $counts->values()->all()]],
        ];
    }
}
