<?php

namespace App\Xelqun\Admin\Resources;

use App\Examples\Tables\Columns\StockColumn;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Xelqun\Forms\Components\ColorPicker;
use Xelqun\Forms\Components\DatePicker;
use Xelqun\Forms\Components\DateTimePicker;
use Xelqun\Forms\Components\FileUpload;
use Xelqun\Forms\Components\KeyValue;
use Xelqun\Forms\Components\RichEditor;
use Xelqun\Forms\Components\Select;
use Xelqun\Forms\Components\Slider;
use Xelqun\Forms\Components\TagsInput;
use Xelqun\Forms\Components\TextInput;
use Xelqun\Forms\Components\TimePicker;
use Xelqun\Forms\Components\Toggle;
use Xelqun\Forms\Components\ToggleButtons;
use Xelqun\Forms\Layouts\Section;
use Xelqun\Forms\Layouts\Tab;
use Xelqun\Forms\Layouts\Tabs;
use Xelqun\Panels\Actions\ExportAction;
use Xelqun\Panels\Actions\ImportAction;
use Xelqun\Panels\Infolists\ColorEntry;
use Xelqun\Panels\Infolists\IconEntry;
use Xelqun\Panels\Infolists\ImageEntry;
use Xelqun\Panels\Infolists\KeyValueEntry;
use Xelqun\Panels\Infolists\TextEntry;
use Xelqun\Panels\Resources\Resource;
use Xelqun\Panels\Resources\Tab as ListTab;
use Xelqun\Tables\Columns\ColorColumn;
use Xelqun\Tables\Columns\ImageColumn;
use Xelqun\Tables\Columns\SelectColumn;
use Xelqun\Tables\Columns\TextColumn;
use Xelqun\Tables\Columns\TextInputColumn;
use Xelqun\Tables\Columns\ToggleColumn;
use Xelqun\Tables\Filters\Constraints\BooleanConstraint;
use Xelqun\Tables\Filters\Constraints\NumberConstraint;
use Xelqun\Tables\Filters\Constraints\TextConstraint;
use Xelqun\Tables\Filters\QueryBuilder;
use Xelqun\Tables\Filters\SelectFilter;
use Xelqun\Tables\Summarizers\Average;
use Xelqun\Tables\Table;

class ProductResource extends Resource
{
    /** No SVG: it can carry scripts, and anyone can upload here. */
    private const array IMAGE_TYPES = ['image/png', 'image/jpeg', 'image/webp'];

    protected static string $model = Product::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationIcon = 'package';

    protected static ?string $navigationGroup = 'Catalog';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['category', 'brand']);
    }

    public static function getHeaderActions(): array
    {
        return [ExportAction::for(static::class), ImportAction::for(static::class)];
    }

    public static function getTabs(): array
    {
        $byStatus = fn (string $status): ListTab => ListTab::make()
            ->badge(fn (): int => Product::query()->where('status', $status)->count())
            ->modifyQueryUsing(fn (Builder $query) => $query->where('status', $status));

        return ['all' => ListTab::make(), 'active' => $byStatus('active'), 'draft' => $byStatus('draft'), 'archived' => $byStatus('archived')];
    }

    public static function infolist(): array
    {
        return [
            Section::make('Product')->columns(2)->schema([
                TextEntry::make('name'),
                TextEntry::make('sku')->label('SKU'),
                TextEntry::make('category.name')->label('Category'),
                TextEntry::make('status')->badge()->color(fn (string $state): string => match ($state) {
                    'active' => 'success',
                    'draft' => 'warning',
                    default => 'gray',
                }),
            ]),
            Section::make('Stock and pricing')->columns(3)->schema([
                TextEntry::make('price')->money('USD'),
                TextEntry::make('stock'),
                IconEntry::make('is_featured')->label('Featured')->boolean(),
            ]),
            Section::make('Details')->columns(3)->schema([
                ImageEntry::make('image')->disk('public'),
                ColorEntry::make('color'),
                KeyValueEntry::make('specs'),
            ]),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'sku', 'category.name'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return ['SKU' => $record->getAttribute('sku'), 'Category' => $record->getRelation('category')?->getAttribute('name')];
    }

    public static function getNavigationBadge(): int
    {
        return static::getEloquentQuery()->count();
    }

    public static function form(): array
    {
        $statuses = ['draft' => 'Draft', 'active' => 'Active', 'archived' => 'Archived'];

        return [
            Tabs::make('Product')->persistTabInQueryString()->columnSpanFull()->tabs([
                Tab::make('General')->columns(2)->schema([
                    TextInput::make('name')->required()->maxLength(255),
                    TextInput::make('sku')->label('SKU')->required()->maxLength(255)->copyable(),
                    Select::make('category_id')->label('Category')->required()->relationship('category', 'name')->searchable()->preload(),
                    Select::make('brand_id')
                        ->label('Brand')
                        ->relationship('brand', 'name')
                        ->searchable()
                        ->preload()
                        ->createOptionForm([
                            TextInput::make('name')->required(),
                            TextInput::make('slug')->required(),
                        ]),
                    ToggleButtons::make('status')
                        ->required()
                        ->default('draft')
                        ->options($statuses)
                        ->colors(['draft' => 'warning', 'active' => 'success', 'archived' => 'gray'])
                        ->icons(['draft' => 'pencil', 'active' => 'check', 'archived' => 'folder'])
                        ->inline(),
                    Toggle::make('is_featured')->label('Featured')->description('Shown first in the store.'),
                    RichEditor::make('description')->columnSpanFull(),
                ]),
                Tab::make('Pricing')->columns(2)->schema([
                    TextInput::make('price')->numeric()->required()->prefix('$')->minValue(0),
                    TextInput::make('stock')->integer()->required()->minValue(0),
                    Slider::make('discount')->range(0, 100)->step(5)->tooltips()->default(0)->columnSpanFull(),
                ]),
                Tab::make('Media')->columns(2)->schema([
                    FileUpload::make('image')->image()->acceptedFileTypes(self::IMAGE_TYPES)->maxSize(2048)->disk('public')->directory('demo/products')->imageEditor(),
                    FileUpload::make('gallery')->image()->acceptedFileTypes(self::IMAGE_TYPES)->maxSize(2048)->disk('public')->directory('demo/gallery')->multiple()->reorderable()->maxFiles(6),
                ]),
                Tab::make('Details')->columns(2)->schema([
                    KeyValue::make('specs')->keyLabel('Spec')->valueLabel('Detail')->columnSpanFull(),
                    TagsInput::make('keywords')->suggestions(['wireless', 'usb-c', 'compact', 'studio']),
                    ColorPicker::make('color')->hex(),
                    DatePicker::make('released_at')->label('Released'),
                    DateTimePicker::make('restock_at')->label('Restock on'),
                    TimePicker::make('pickup_time')->label('Pickup from')->seconds(false),
                ]),
            ]),
        ];
    }

    public static function columns(): array
    {
        return [
            ImageColumn::make('image')->disk('public')->size(36)->alt('Product photo'),
            TextColumn::make('name')->searchable()->sortable(),
            TextColumn::make('category.name')->label('Category')->sortable(),
            TextColumn::make('brand.name')->label('Brand')->toggleable(isHiddenByDefault: true),
            TextColumn::make('sku')->label('SKU')->searchable()->toggleable(isHiddenByDefault: true),
            ColorColumn::make('color')->toggleable(isHiddenByDefault: true),
            SelectColumn::make('status')->authorize('update')->options(['draft' => 'Draft', 'active' => 'Active', 'archived' => 'Archived']),
            TextColumn::make('price')->money('USD')->sortable()->summarize(Average::make()->label('Average')),
            TextInputColumn::make('stock')->authorize('update')->rules(['required', 'integer', 'min:0']),
            StockColumn::make('stock_level')->label('Level')->state(fn (Product $record): int => $record->stock)->toggleable(),
            ToggleColumn::make('is_featured')->label('Featured')->authorize('update')->toggleable(),
        ];
    }

    public static function filters(): array
    {
        return [
            SelectFilter::make('status')->options(['draft' => 'Draft', 'active' => 'Active', 'archived' => 'Archived']),
            QueryBuilder::make('rules')->label('Custom rules')->constraints([
                TextConstraint::make('name'),
                TextConstraint::make('category.name')->label('Category'),
                NumberConstraint::make('price'),
                NumberConstraint::make('stock'),
                BooleanConstraint::make('is_featured')->label('Featured'),
            ]),
        ];
    }

    public static function configureTable(Table $table): void
    {
        $table->defaultSort('name')->paginationPageOptions([10, 25, 50]);
    }
}
