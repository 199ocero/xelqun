<?php

namespace App\Xelqun\Admin\Widgets;

use App\Models\Product;
use App\Xelqun\Admin\Resources\ProductResource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Xelqun\Actions\Action;
use Xelqun\Panels\Widgets\ListWidget;

class PendingProducts extends ListWidget
{
    protected static int $sort = 2;

    protected static int $columnSpan = 8;

    protected static int $limit = 3;

    protected static ?string $heading = 'Needs your approval';

    public function query(): Builder
    {
        return Product::query()->with('category')->where('status', 'draft')->orderByDesc('updated_at');
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
        return 'package';
    }

    protected function iconColor(Model $record): string
    {
        return 'primary';
    }

    public function recordActions(): array
    {
        return [
            Action::make('reject')
                ->color('gray')
                ->authorize('update')
                ->successNotificationTitle('Product archived.')
                ->action(fn (Product $record) => $record->update(['status' => 'archived'])),
            Action::make('approve')
                ->color('primary')
                ->authorize('update')
                ->successNotificationTitle('Product approved.')
                ->action(fn (Product $record) => $record->update(['status' => 'active'])),
        ];
    }

    protected function getSeeAllUrl(): string
    {
        return ProductResource::getUrl();
    }
}
