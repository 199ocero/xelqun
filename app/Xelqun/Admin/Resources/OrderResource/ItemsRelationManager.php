<?php

namespace App\Xelqun\Admin\Resources\OrderResource;

use App\Xelqun\Admin\Resources\OrderItemResource;
use Xelqun\Forms\Components\TextInput;
use Xelqun\Panels\Resources\RelationManager;
use Xelqun\Tables\Columns\TextColumn;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    protected static ?string $title = 'Line items';

    protected static ?string $relatedResource = OrderItemResource::class;

    public static function form(): array
    {
        return [
            TextInput::make('product')->required()->maxLength(255),
            TextInput::make('qty')->label('Quantity')->numeric()->minValue(1)->default(1)->required(),
            TextInput::make('price')->numeric()->prefix('$')->minValue(0.01)->required(),
            TextInput::make('subtotal')->numeric()->prefix('$')->computed('round(qty * price, 2)')->readOnly(),
        ];
    }

    public static function columns(): array
    {
        return [
            TextColumn::make('product')->searchable()->sortable(),
            TextColumn::make('qty')->label('Quantity')->sortable(),
            TextColumn::make('price')->money('USD')->sortable(),
            TextColumn::make('subtotal')->money('USD')->sortable(),
        ];
    }
}
