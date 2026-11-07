<?php

namespace App\Examples\Tables;

use App\Models\Category;
use Illuminate\Database\Eloquent\Builder;
use Xelqun\Tables\Columns\TextColumn;
use Xelqun\Tables\Table;

/**
 * The second table on the page. Each keeps its own state in the URL.
 */
class CategoriesTable extends Table
{
    protected function boot(): void
    {
        $this->defaultSort('name')->paginationPageOptions([3, 6]);
    }

    public function query(): Builder
    {
        return Category::query()->withCount('products');
    }

    public function columns(): array
    {
        return [
            TextColumn::make('name')->searchable()->sortable(),
            TextColumn::make('products_count')
                ->label('Products')
                ->sortable(query: fn (Builder $query, string $direction) => $query->orderBy('products_count', $direction === 'desc' ? 'desc' : 'asc')),
        ];
    }
}
