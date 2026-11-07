<?php

namespace App\Xelqun\Admin\Resources;

use App\Models\StoreSetting;
use App\Xelqun\Admin\Clusters\Settings;
use Xelqun\Forms\Components\TextInput;
use Xelqun\Forms\Components\Toggle;
use Xelqun\Forms\Layouts\Section;
use Xelqun\Panels\Resources\Pages\ManageSingleRecord;
use Xelqun\Panels\Resources\Resource;

/**
 * One record and no list.
 */
class StoreSettingResource extends Resource
{
    protected static string $model = StoreSetting::class;

    protected static ?string $cluster = Settings::class;

    protected static ?string $slug = 'store-settings';

    protected static ?string $modelLabel = 'store';

    protected static ?string $pluralModelLabel = 'store';

    protected static ?string $navigationIcon = 'package';

    public static function form(): array
    {
        return [
            Section::make('Store')->columns(2)->schema([
                TextInput::make('store_name')->required()->maxLength(255),
                TextInput::make('support_email')->email()->required(),
                TextInput::make('currency')->required()->length(3),
                Toggle::make('accepts_orders')->label('Accepting orders')->default(true),
            ]),
        ];
    }

    public static function columns(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return ['index' => ManageSingleRecord::class];
    }
}
