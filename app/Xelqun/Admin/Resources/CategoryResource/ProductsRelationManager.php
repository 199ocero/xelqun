<?php

namespace App\Xelqun\Admin\Resources\CategoryResource;

use Xelqun\Forms\Components\Select;
use Xelqun\Forms\Components\TextInput;
use Xelqun\Panels\Resources\RelationManager;
use Xelqun\Tables\Columns\TextColumn;

class ProductsRelationManager extends RelationManager
{
    protected static string $relationship = 'products';

    public static function form(): array
    {
        return [
            TextInput::make('name')->required()->maxLength(255),
            TextInput::make('sku')->label('SKU')->required()->maxLength(255),
            Select::make('status')->required()->default('draft')->options(['draft' => 'Draft', 'active' => 'Active', 'archived' => 'Archived']),
            TextInput::make('price')->numeric()->required(),
            TextInput::make('stock')->numeric()->integer()->required(),
        ];
    }

    public static function columns(): array
    {
        return [
            TextColumn::make('name')->searchable()->sortable(),
            TextColumn::make('status')->badge(),
            TextColumn::make('price')->money('USD')->sortable(),
        ];
    }
}
