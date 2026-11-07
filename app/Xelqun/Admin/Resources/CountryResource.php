<?php

namespace App\Xelqun\Admin\Resources;

use App\Models\Country;
use App\Xelqun\Admin\Clusters\Settings;
use App\Xelqun\Admin\Resources\CountryResource\StatesRelationManager;
use Xelqun\Forms\Components\TextInput;
use Xelqun\Forms\Layouts\Section;
use Xelqun\Panels\Infolists\TextEntry;
use Xelqun\Panels\Resources\Resource;
use Xelqun\Tables\Columns\TextColumn;

class CountryResource extends Resource
{
    protected static string $model = Country::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $modelLabel = 'country';

    protected static ?string $pluralModelLabel = 'countries';

    protected static ?string $navigationIcon = 'map-pin';

    protected static ?string $cluster = Settings::class;

    public static function form(): array
    {
        return [
            Section::make('Country')->columns(2)->schema([
                TextInput::make('name')->required()->maxLength(255),
                TextInput::make('code')->required()->length(2),
                TextInput::make('dial_code')->label('Dial code')->required(),
                TextInput::make('currency')->required()->length(3),
            ]),
        ];
    }

    public static function infolist(): array
    {
        return [
            Section::make('Country')->columns(4)->schema([
                TextEntry::make('name'),
                TextEntry::make('code')->badge(),
                TextEntry::make('dial_code')->label('Dial code'),
                TextEntry::make('currency'),
            ]),
        ];
    }

    public static function columns(): array
    {
        return [
            TextColumn::make('name')->searchable()->sortable(),
            TextColumn::make('code')->badge()->searchable(),
            TextColumn::make('dial_code')->label('Dial code'),
            TextColumn::make('currency')->sortable(),
        ];
    }

    public static function getRelations(): array
    {
        return [StatesRelationManager::class];
    }
}
