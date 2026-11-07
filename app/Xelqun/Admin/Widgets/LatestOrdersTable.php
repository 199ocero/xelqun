<?php

namespace App\Xelqun\Admin\Widgets;

use App\Models\Order;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use Xelqun\Tables\Columns\TextColumn;
use Xelqun\Tables\Table;

class LatestOrdersTable extends Table
{
    protected function boot(): void
    {
        $this->id('latest-orders-table');
    }

    public function query(): Builder
    {
        return Order::query()->latest('id');
    }

    public function authorize(?Authenticatable $user): bool
    {
        return $user?->can('viewAny', Order::class) ?? false;
    }

    public function columns(): array
    {
        return [
            TextColumn::make('customer_name')->label('Customer')->searchable(),
            TextColumn::make('type')->badge(),
            TextColumn::make('delivery_date')->date()->sortable(),
            TextColumn::make('total')->money('USD')->sortable(),
        ];
    }
}
