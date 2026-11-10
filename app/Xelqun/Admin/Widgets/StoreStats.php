<?php

namespace App\Xelqun\Admin\Widgets;

use App\Models\Category;
use App\Models\Product;
use Xelqun\Panels\Widgets\Stat;
use Xelqun\Panels\Widgets\StatsOverview;

class StoreStats extends StatsOverview
{
    protected static int $sort = 1;

    protected static ?int $pollingInterval = 60;

    protected function getStats(): array
    {
        $active = Product::query()->where('status', 'active')->count();

        return [
            Stat::make('Live products', $active)->icon('package')->description('Showing in the store'),
            Stat::make('Waiting for you', Product::query()->where('status', 'draft')->count())->icon('clock')->description('Drafts to approve'),
            Stat::make('Low on stock', Product::query()->where('stock', '<', 50)->count())->icon('truck')->description('Under 50 left')->chart([12, 18, 15, 22, 19, 27]),
            Stat::make('Categories', Category::query()->count())->icon('folder-tree')->description('Across the catalog'),
        ];
    }
}
