<?php

namespace App\Xelqun\Admin\Widgets;

use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Xelqun\Panels\Widgets\ListWidget;

class LowStock extends ListWidget
{
    private const int TARGET = 100;

    protected static int $sort = 4;

    protected static int $columnSpan = 8;

    protected static int $limit = 4;

    protected static ?string $heading = 'Restocking now';

    public function query(): Builder
    {
        return Product::query()->with('category')->where('stock', '<', 50)->orderByDesc('stock');
    }

    protected function title(Model $record): string
    {
        return $record->getAttribute('name');
    }

    protected function meta(Model $record): string
    {
        return "{$record->getAttribute('category')->name} · {$record->getAttribute('sku')}";
    }

    protected function icon(Model $record): string
    {
        return 'truck';
    }

    protected function iconColor(Model $record): string
    {
        return 'warning';
    }

    protected function progress(Model $record): float
    {
        return min($record->getAttribute('stock') / self::TARGET, 1);
    }

    protected function progressNote(Model $record): string
    {
        return "{$record->getAttribute('stock')} of ".self::TARGET;
    }
}
