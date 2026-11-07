<?php

namespace App\Xelqun\Admin\Widgets;

use App\Models\Order;
use Illuminate\Support\Number;
use Xelqun\Panels\Widgets\Stat;
use Xelqun\Panels\Widgets\StatsOverview;

class OrderStats extends StatsOverview
{
    protected static int $columnSpan = 12;

    protected function getStats(): array
    {
        return [
            Stat::make('Orders', Order::query()->count())->icon('shopping-cart')->description('All time'),
            Stat::make('Revenue', Number::currency((float) Order::query()->sum('total'), 'USD', precision: 0) ?: '$0')->icon('chart-column')->description('Before refunds'),
            Stat::make('Gifts', Order::query()->where('is_gift', true)->count())->icon('tag')->description('Wrapped and ready'),
            Stat::make('Business', Order::query()->where('type', 'business')->count())->icon('users')->description('Company accounts'),
        ];
    }
}
