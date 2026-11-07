<?php

namespace App\Xelqun\Admin\Resources;

use App\Models\Category;
use App\Xelqun\Admin\Resources\CategoryResource\ProductsRelationManager;
use Xelqun\Forms\Components\TextInput;
use Xelqun\Panels\Resources\Resource;
use Xelqun\Tables\Columns\TextColumn;
use Xelqun\Tables\Table;

class CategoryResource extends Resource
{
    protected static string $model = Category::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $modelLabel = 'category';

    protected static ?string $navigationIcon = 'folder-tree';

    protected static ?string $navigationGroup = 'Catalog';

    public static function getRelations(): array
    {
        return [ProductsRelationManager::class];
    }

    public static function form(): array
    {
        return [TextInput::make('name')->required()->maxLength(255)];
    }

    public static function columns(): array
    {
        return [TextColumn::make('name')->searchable()->sortable()];
    }

    public static function configureTable(Table $table): void
    {
        $table->defaultSort('sort')->reorderable('sort', true);
    }
}
