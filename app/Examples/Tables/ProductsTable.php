<?php

namespace App\Examples\Tables;

use App\Examples\Tables\Columns\StockColumn;
use App\Models\Category;
use App\Models\Product;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Xelqun\Actions\Action;
use Xelqun\Actions\ActionGroup;
use Xelqun\Forms\Components\Component;
use Xelqun\Forms\Components\FileUpload;
use Xelqun\Forms\Components\Select;
use Xelqun\Forms\Components\TextInput;
use Xelqun\Forms\State\Get;
use Xelqun\Tables\Actions\BulkAction;
use Xelqun\Tables\Columns\IconColumn;
use Xelqun\Tables\Columns\ImageColumn;
use Xelqun\Tables\Columns\TextColumn;
use Xelqun\Tables\Filters\Filter;
use Xelqun\Tables\Filters\SelectFilter;
use Xelqun\Tables\Filters\TernaryFilter;
use Xelqun\Tables\Table;

/**
 * A table is a class, like a form. Rows only carry what its columns declare.
 */
class ProductsTable extends Table
{
    protected function boot(): void
    {
        $this->defaultSort('name')->paginationPageOptions([10, 25, 50]);
    }

    public function query(): Builder
    {
        return Product::query();
    }

    public function filters(): array
    {
        return [
            SelectFilter::make('status')->multiple()->options([
                'draft' => 'Draft',
                'active' => 'Active',
                'archived' => 'Archived',
            ]),
            SelectFilter::make('category')->relationship('category', 'name')->searchable(),
            TernaryFilter::make('is_featured')->label('Featured'),
            Filter::make('in_stock')->query(fn (Builder $query) => $query->where('stock', '>', 0)),
            Filter::make('released')
                ->schema([
                    Select::make('released_year')
                        ->label('Released in')
                        ->live()
                        ->options(fn (): array => $this->releaseYears()),
                    Select::make('released_month')
                        ->label('Month')
                        ->options(fn (Get $get): array => $this->releaseMonths($get('released_year'))),
                ])
                ->query(fn (Builder $query, array $data) => $query
                    ->when($data['released_year'] ?? null, fn (Builder $query, string $year) => $query->whereYear('released_at', $year))
                    ->when($data['released_month'] ?? null, fn (Builder $query, string $month) => $query->whereMonth('released_at', $month))),
        ];
    }

    public function columns(): array
    {
        return [
            ImageColumn::make('photo')
                ->alt(fn (Product $record): string => $record->name)
                ->state(fn (Product $record): string => $record->image === null ? '/favicon.svg' : Storage::disk('public')->url($record->image))
                ->size(28),
            TextColumn::make('name')->searchable()->sortable(),
            TextColumn::make('category.name')
                ->label('Category')
                ->searchable()
                ->sortable()
                ->url(fn (Product $record): string => url()->current().'?'.http_build_query(['products-table' => ['search' => $record->category->name]])),
            TextColumn::make('sku')->label('SKU')->searchable(),
            TextColumn::make('status')
                ->badge()
                ->sortable()
                ->icon(fn (string $state): ?string => $state === 'active' ? 'check' : null)
                ->color(fn (string $state): string => match ($state) {
                    'active' => 'success',
                    'draft' => 'warning',
                    default => 'gray',
                }),
            TextColumn::make('price')->money('USD')->sortable(),
            StockColumn::make('stock')->full(250)->sortable(),
            IconColumn::make('is_featured')->label('Featured')->boolean(),
            TextColumn::make('released_at')->label('Released')->date()->sortable(),
        ];
    }

    public function recordActions(): array
    {
        return [
            Action::make('edit')
                ->authorize(fn (): bool => $this->canChangeProducts())
                ->iconButton()
                ->icon('pencil')
                ->modalHeading('Edit product')
                ->schema($this->productFields())
                ->successNotificationTitle('Product saved')
                ->action(fn (array $data, Product $record) => $record->update($data)),
            Action::make('delete')
                ->authorize(fn (): bool => $this->canChangeProducts())
                ->iconButton()
                ->icon('trash')
                ->color('danger')
                ->requiresConfirmation()
                ->modalDescription('This removes the product for good.')
                ->successNotificationTitle('Product deleted')
                ->action(fn (Product $record) => $record->delete()),
            ActionGroup::make([
                Action::make('activate')
                    ->authorize(fn (): bool => $this->canChangeProducts())
                    ->label('Mark as active')
                    ->icon('check')
                    ->successNotificationTitle(fn (Product $record): string => "{$record->name} is active")
                    ->action(fn (Product $record) => $record->update(['status' => 'active'])),
                Action::make('rename')
                    ->authorize(fn (): bool => $this->canChangeProducts())
                    ->label('Rename')
                    ->icon('pencil')
                    ->fillForm(fn (Product $record): array => ['name' => $record->name])
                    ->modalSubmitActionLabel('Rename')
                    ->schema([TextInput::make('name')->required()])
                    ->successNotificationTitle('Renamed')
                    ->action(fn (array $data, Product $record) => $record->update($data)),
                Action::make('archive')
                    ->authorize(fn (): bool => $this->canChangeProducts())
                    ->label('Archive')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalDescription('It stays in the table with the status archived.')
                    ->successNotificationTitle('Archived')
                    ->action(fn (Product $record) => $record->update(['status' => 'archived'])),
            ]),
        ];
    }

    public function headerActions(): array
    {
        return [
            Action::make('create')
                ->authorize(fn (): bool => $this->canChangeProducts())
                ->label('New product')
                ->color('primary')
                ->icon('plus')
                ->modalSubmitActionLabel('Create')
                ->schema($this->productFields())
                ->successNotificationTitle('Product created')
                ->action(fn (array $data) => Product::query()->create([...$data, 'sku' => Str::upper(Str::random(8))])),
        ];
    }

    public function bulkActions(): array
    {
        return [
            BulkAction::make('set_status')
                ->authorize(fn (): bool => $this->canChangeProducts())
                ->label('Set status')
                ->modalHeading('Set status')
                ->schema([
                    Select::make('status')->required()->options([
                        'draft' => 'Draft',
                        'active' => 'Active',
                        'archived' => 'Archived',
                    ]),
                ])
                ->action(fn (array $data, Collection $records) => $records->toQuery()->update(['status' => $data['status']])),
            BulkAction::make('delete')
                ->authorize(fn (): bool => $this->canChangeProducts())
                ->label('Delete')
                ->color('danger')
                ->requiresConfirmation()
                ->modalDescription('This removes the selected products for good.')
                ->action(fn (Collection $records) => $records->toQuery()->delete()),
        ];
    }

    /**
     * @return array<int, Component>
     */
    private function productFields(): array
    {
        return [
            TextInput::make('name')->required()->maxLength(255),
            Select::make('category_id')
                ->label('Category')
                ->required()
                ->options(fn (): SupportCollection => Category::query()->orderBy('name')->pluck('name', 'id')),
            Select::make('status')->required()->default('draft')->options([
                'draft' => 'Draft',
                'active' => 'Active',
                'archived' => 'Archived',
            ]),
            TextInput::make('price')->numeric()->required()->minValue(0),
            FileUpload::make('image')->image()->disk('public')->directory('products'),
        ];
    }

    /**
     * The public demo shows the table, and nobody changes its data.
     */
    private function canChangeProducts(): bool
    {
        return request()->getHost() === config('xelqun.demo_domain');
    }

    /**
     * @return array<int|string, string>
     */
    private function releaseYears(): array
    {
        return Product::query()
            ->whereNotNull('released_at')
            ->pluck('released_at')
            ->map(fn (CarbonInterface $date): string => $date->format('Y'))
            ->unique()
            ->sortDesc()
            ->mapWithKeys(fn (string $year): array => [$year => $year])
            ->all();
    }

    /**
     * Only the months that year has products in, which is what makes it depend on the year.
     *
     * @return array<int, string>
     */
    private function releaseMonths(?string $year): array
    {
        if ($year === null) {
            return [];
        }

        return Product::query()
            ->whereYear('released_at', $year)
            ->pluck('released_at')
            ->sort()
            ->mapWithKeys(fn (CarbonInterface $date): array => [$date->format('n') => $date->format('F')])
            ->all();
    }
}
