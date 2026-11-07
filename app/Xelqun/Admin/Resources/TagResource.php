<?php

namespace App\Xelqun\Admin\Resources;

use App\Models\Tag;
use Xelqun\Forms\Components\TextInput;
use Xelqun\Panels\Resources\Pages\ManageRecords;
use Xelqun\Panels\Resources\Resource;
use Xelqun\Tables\Columns\TextColumn;

class TagResource extends Resource
{
    protected static string $model = Tag::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationIcon = 'tag';

    protected static ?string $navigationGroup = 'Catalog';

    public static function form(): array
    {
        return [TextInput::make('name')->required()->maxLength(255)];
    }

    public static function columns(): array
    {
        return [TextColumn::make('name')->searchable()->sortable()];
    }

    public static function getPages(): array
    {
        return ['index' => ManageRecords::class];
    }
}
