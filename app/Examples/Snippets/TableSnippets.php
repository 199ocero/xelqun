<?php

namespace App\Examples\Snippets;

use App\Examples\Tables\Columns\StockColumn;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Product;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use Xelqun\Actions\Action;
use Xelqun\Actions\ActionGroup;
use Xelqun\Forms\Components\TextInput;
use Xelqun\Panels\Tables\TrashedFilter;
use Xelqun\Tables\Actions\BulkAction;
use Xelqun\Tables\Columns\CheckboxColumn;
use Xelqun\Tables\Columns\ColorColumn;
use Xelqun\Tables\Columns\IconColumn;
use Xelqun\Tables\Columns\ImageColumn;
use Xelqun\Tables\Columns\SelectColumn;
use Xelqun\Tables\Columns\TextColumn;
use Xelqun\Tables\Columns\TextInputColumn;
use Xelqun\Tables\Columns\ToggleColumn;
use Xelqun\Tables\Filters\Constraints\BooleanConstraint;
use Xelqun\Tables\Filters\Constraints\NumberConstraint;
use Xelqun\Tables\Filters\Constraints\TextConstraint;
use Xelqun\Tables\Filters\Filter;
use Xelqun\Tables\Filters\QueryBuilder;
use Xelqun\Tables\Filters\SelectFilter;
use Xelqun\Tables\Filters\TernaryFilter;
use Xelqun\Tables\Summarizers\Average;
use Xelqun\Tables\Summarizers\Count;
use Xelqun\Tables\Summarizers\Range;
use Xelqun\Tables\Summarizers\Sum;
use Xelqun\Tables\Table;

/**
 * The preview of each docs page, matching the code on that page. Cells and actions
 * work, and they don't save, because these run on the public docs. Reordering is the
 * one exception: it has no way to skip the write, and the demo's reset puts it back.
 */
final class TableSnippets
{
    /**
     * @return array<string, Closure(): array<string, mixed>>
     */
    public static function all(): array
    {
        $products = fn (): Builder => Product::query()->with('category');
        $small = fn (Table $table): Table => $table->defaultSort('name')->paginationPageOptions([5, 10, 25]);
        $name = fn (): TextColumn => TextColumn::make('name')->searchable()->sortable();
        $preview = fn (): Closure => fn (): null => null;

        $product = fn (array $columns, array $extra = []): Closure => fn (): array => [
            'query' => $products,
            'configure' => $small,
            'columns' => [$name(), ...$columns],
            ...$extra,
        ];

        $photo = fn (Product $record): string => Storage::disk('public')->url((string) $record->image);

        return [
            'first-table' => $product([TextColumn::make('price')->money('USD')->sortable()]),
            'column-options' => $product([
                TextColumn::make('category.name')->label('Category'),
                TextColumn::make('status')->badge()->color(fn (string $state): string => $state === 'active' ? 'success' : 'gray'),
            ]),
            'text' => $product([
                TextColumn::make('status')->badge(),
                TextColumn::make('price')->money('USD'),
                TextColumn::make('released_at')->date(),
            ]),
            'icon' => $product([
                IconColumn::make('is_featured')->label('Featured')->boolean(),
                IconColumn::make('status')
                    ->icon(fn (string $state): string => $state === 'active' ? 'circle-check' : 'circle-alert')
                    ->color(fn (string $state): string => $state === 'active' ? 'success' : 'warning'),
            ]),
            'image' => $product([
                ImageColumn::make('photo')->state($photo)->size(40)->alt('Product photo'),
                ImageColumn::make('round')->label('Circular')->state($photo)->size(40)->circular()->alt('Product photo'),
            ]),
            'color' => fn (): array => [
                'query' => fn (): Builder => Brand::query(),
                'configure' => fn (Table $table): Table => $table->defaultSort('name'),
                'columns' => [TextColumn::make('name'), ColorColumn::make('color')],
            ],
            'select' => $product([
                SelectColumn::make('status')->authorize(true)->options(['draft' => 'Draft', 'active' => 'Active', 'archived' => 'Archived'])->updateStateUsing($preview()),
            ]),
            'toggle' => $product([
                ToggleColumn::make('is_featured')->label('Featured')->authorize(true)->updateStateUsing($preview()),
            ]),
            'text-input' => $product([
                TextInputColumn::make('stock')->authorize(true)->rules(['required', 'integer', 'min:0'])->updateStateUsing($preview()),
            ]),
            'checkbox' => fn (): array => [
                'query' => fn (): Builder => Comment::query(),
                'columns' => [TextColumn::make('name'), CheckboxColumn::make('is_approved')->label('Approved')->authorize(true)->updateStateUsing($preview())],
            ],
            'custom' => $product([StockColumn::make('stock')->full(250)]),
            'filters' => $product([TextColumn::make('status')->badge()], ['filters' => [
                SelectFilter::make('status')->options(['draft' => 'Draft', 'active' => 'Active', 'archived' => 'Archived']),
                Filter::make('in_stock')->query(fn (Builder $query) => $query->where('stock', '>', 0)),
            ]]),
            'filter-select' => $product([TextColumn::make('category.name')->label('Category')], ['filters' => [
                SelectFilter::make('category')->relationship('category', 'name'),
                SelectFilter::make('state')->attribute('status')->multiple()->options(['draft' => 'Draft', 'active' => 'Active']),
            ]]),
            'filter-ternary' => $product([IconColumn::make('is_featured')->label('Featured')->boolean()], ['filters' => [
                TernaryFilter::make('is_featured')->label('Featured'),
            ]]),
            'filter-query-builder' => $product([TextColumn::make('price')->money('USD')], ['filters' => [
                QueryBuilder::make('rules')->label('Custom rules')->constraints([
                    TextConstraint::make('name'),
                    NumberConstraint::make('price'),
                    BooleanConstraint::make('is_featured'),
                ]),
            ]]),
            'filter-custom' => $product([TextColumn::make('price')->money('USD')], ['filters' => [
                Filter::make('price_range')
                    ->schema([TextInput::make('min')->numeric(), TextInput::make('max')->numeric()])
                    ->query(fn (Builder $query, array $data) => $query
                        ->when($data['min'] ?? null, fn (Builder $query, string $min) => $query->where('price', '>=', $min))
                        ->when($data['max'] ?? null, fn (Builder $query, string $max) => $query->where('price', '<=', $max))),
            ]]),
            'filter-trashed' => fn (): array => [
                'query' => fn (): Builder => Post::query()->withTrashed(),
                'columns' => [TextColumn::make('title')],
                'filters' => [TrashedFilter::make('trashed')->label('Deleted posts')],
            ],
            'record-actions' => $product([], ['recordActions' => [
                Action::make('edit')->authorize(true)->icon('pencil')->fillForm(fn (Product $record): array => ['name' => $record->name])
                    ->schema([TextInput::make('name')->required()])
                    ->successNotificationTitle('Nothing was saved, this is a preview.')
                    ->action($preview()),
                ActionGroup::make([
                    Action::make('archive')->authorize(true)->requiresConfirmation()->successNotificationTitle('Nothing was saved, this is a preview.')->action($preview()),
                ]),
            ]]),
            'header-actions' => $product([], ['headerActions' => [
                Action::make('create')->authorize(true)->schema([TextInput::make('name')->required()])
                    ->successNotificationTitle('Nothing was saved, this is a preview.')
                    ->action($preview()),
            ]]),
            'bulk-actions' => $product([], ['bulkActions' => [
                BulkAction::make('delete')->authorize(true)->requiresConfirmation()
                    ->successNotificationTitle('Nothing was deleted, this is a preview.')
                    ->action($preview()),
            ]]),
            'search-and-sorting' => $product([
                TextColumn::make('category.name')->label('Category')->searchable()->sortable(),
                TextColumn::make('price')->money('USD')->sortable(),
            ]),
            'pagination' => $product([TextColumn::make('price')->money('USD')]),
            'summaries' => $product([
                TextColumn::make('price')->money('USD')->summarize([Sum::make()->label('Total'), Average::make()->label('Average'), Range::make()->label('Range'), Count::make()->label('Rows')]),
            ]),
            'grouping' => fn (): array => [
                'query' => fn (): Builder => Comment::query(),
                'configure' => fn (Table $table): Table => $table->defaultGroup('status'),
                'columns' => [TextColumn::make('name'), TextColumn::make('status')->badge()],
            ],
            'reordering' => fn (): array => [
                'query' => fn (): Builder => Category::query(),
                'configure' => fn (Table $table): Table => $table->defaultSort('sort')->reorderable('sort', true),
                'columns' => [TextColumn::make('name')],
            ],
            'grid' => fn (): array => [
                'query' => fn (): Builder => Brand::query(),
                'configure' => fn (Table $table): Table => $table->defaultSort('name')->contentGrid(3),
                'columns' => [TextColumn::make('name'), ColorColumn::make('color')],
            ],
            'striped' => $product([TextColumn::make('price')->money('USD')], ['configure' => fn (Table $table): Table => $small($table)->striped()]),
            'empty-state' => fn (): array => [
                'query' => fn (): Builder => Comment::query()->whereRaw('1 = 0'),
                'configure' => fn (Table $table): Table => $table->emptyState('No comments yet', 'New comments show up here.'),
                'columns' => [TextColumn::make('name')],
            ],
            'polling' => $product([TextColumn::make('stock')], ['configure' => fn (Table $table): Table => $small($table)->poll('10s')]),
            'column-toggles' => $product([
                TextColumn::make('sku')->label('SKU')->toggleable(),
                TextColumn::make('price')->money('USD')->toggleable(),
                TextColumn::make('created_at')->date()->toggleable(isHiddenByDefault: true),
            ]),
        ];
    }
}
