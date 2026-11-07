<?php

namespace App\Xelqun\Admin\Widgets;

use App\Models\Category;
use App\Xelqun\Admin\Resources\CategoryResource;
use Xelqun\Panels\Widgets\Tile;
use Xelqun\Panels\Widgets\TileWidget;

class CategoryTiles extends TileWidget
{
    protected static int $sort = 5;

    protected static int $columnSpan = 4;

    protected static ?string $heading = 'Your categories';

    protected function getTiles(): array
    {
        return Category::query()->withCount('products')->orderBy('name')->get()
            ->map(fn (Category $category): Tile => Tile::make($category->name)
                ->icon('folder-tree')
                ->status("{$category->products_count} items", $category->products_count > 0 ? 'primary' : 'gray')
                ->url(CategoryResource::getUrl('edit', ['record' => $category])))
            ->all();
    }
}
