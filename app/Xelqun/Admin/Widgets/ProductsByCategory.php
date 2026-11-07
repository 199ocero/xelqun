<?php

namespace App\Xelqun\Admin\Widgets;

use App\Models\Category;
use Xelqun\Panels\Widgets\ChartWidget;

class ProductsByCategory extends ChartWidget
{
    protected static int $sort = 6;

    protected static int $columnSpan = 12;

    protected static ?string $heading = 'Products by category';

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $categories = Category::query()->withCount('products')->orderBy('name')->get();

        return [
            'labels' => $categories->pluck('name')->all(),
            'datasets' => [['label' => 'Products', 'data' => $categories->pluck('products_count')->all()]],
        ];
    }
}
