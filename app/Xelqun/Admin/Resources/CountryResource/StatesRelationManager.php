<?php

namespace App\Xelqun\Admin\Resources\CountryResource;

use Xelqun\Forms\Components\TextInput;
use Xelqun\Panels\Resources\RelationManager;
use Xelqun\Tables\Columns\TextColumn;

class StatesRelationManager extends RelationManager
{
    protected static string $relationship = 'states';

    public static function form(): array
    {
        return [TextInput::make('name')->required()->maxLength(255)];
    }

    public static function columns(): array
    {
        return [TextColumn::make('name')->searchable()->sortable()];
    }
}
