<?php

namespace App\Xelqun\Admin\Widgets;

use App\Models\Product;
use App\Xelqun\Admin\Resources\ProductResource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Xelqun\Panels\Widgets\ListWidget;

class RecentProducts extends ListWidget
{
    protected static int $sort = 3;

    protected static int $columnSpan = 4;

    protected static int $limit = 3;

    protected static ?string $heading = 'Recent products';

    public function query(): Builder
    {
        return Product::query()->with('category')->latest('id');
    }

    protected function title(Model $record): string
    {
        return $record->getAttribute('name');
    }

    protected function meta(Model $record): string
    {
        return $record->getAttribute('category')->name;
    }

    protected function icon(Model $record): string
    {
        return 'tag';
    }

    protected function iconColor(Model $record): string
    {
        return 'success';
    }

    public function recordUrl(Model $record): string
    {
        return ProductResource::getUrl('edit', ['record' => $record]);
    }

    protected function getSeeAllUrl(): string
    {
        return ProductResource::getUrl();
    }
}
