<?php

namespace App\Xelqun\Admin\Resources;

use App\Models\Brand;
use Illuminate\Support\Str;
use Xelqun\Forms\Components\ColorPicker;
use Xelqun\Forms\Components\MarkdownEditor;
use Xelqun\Forms\Components\TextInput;
use Xelqun\Forms\Components\Toggle;
use Xelqun\Forms\Layouts\Section;
use Xelqun\Forms\State\Set;
use Xelqun\Panels\Resources\Resource;
use Xelqun\Tables\Columns\ColorColumn;
use Xelqun\Tables\Columns\TextColumn;
use Xelqun\Tables\Columns\ToggleColumn;
use Xelqun\Tables\Filters\TernaryFilter;
use Xelqun\Tables\Table;

/**
 * A table shown as a grid of cards.
 */
class BrandResource extends Resource
{
    protected static string $model = Brand::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationIcon = 'tag';

    protected static ?string $navigationGroup = 'Catalog';

    public static function form(): array
    {
        return [
            Section::make('Brand')->columnSpanFull()->columns(2)->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug((string) $state))),
                TextInput::make('slug')->required()->maxLength(255)->unique(ignoreRecord: true),
                TextInput::make('website')->url()->prefixIcon('globe'),
                ColorPicker::make('color')->hex()->required()->default('#0071e3'),
                MarkdownEditor::make('description')->columnSpanFull(),
                Toggle::make('is_visible')->label('Visible in the store')->default(true),
            ]),
        ];
    }

    public static function columns(): array
    {
        return [
            TextColumn::make('name')->searchable()->sortable(),
            ColorColumn::make('color'),
            TextColumn::make('website')->url(fn (Brand $record): ?string => $record->website, openInNewTab: true)->toggleable(),
            ToggleColumn::make('is_visible')->label('Visible')->authorize('update'),
        ];
    }

    public static function filters(): array
    {
        return [TernaryFilter::make('is_visible')->label('Visible')];
    }

    public static function configureTable(Table $table): void
    {
        $table->defaultSort('name')->contentGrid(3);
    }
}
