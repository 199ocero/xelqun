<?php

namespace App\Examples\Tables;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Collection;
use Xelqun\Actions\Action;
use Xelqun\Notifications\Notification;
use Xelqun\Tables\ArrayRecord;
use Xelqun\Tables\Columns\TextColumn;
use Xelqun\Tables\Filters\SelectFilter;
use Xelqun\Tables\Table;

/**
 * Rows from an array instead of the database, as an API's would be. records()
 * does the searching, sorting and filtering, and the table pages the rest.
 */
class CarriersTable extends Table
{
    /** @var array<string, array{name: string, region: string, days: int, on_time: int}> */
    private const array CARRIERS = [
        'dhl' => ['name' => 'DHL Express', 'region' => 'Worldwide', 'days' => 2, 'on_time' => 96],
        'fedex' => ['name' => 'FedEx International', 'region' => 'Worldwide', 'days' => 3, 'on_time' => 94],
        'ups' => ['name' => 'UPS Worldwide', 'region' => 'Worldwide', 'days' => 3, 'on_time' => 93],
        'usps' => ['name' => 'USPS Priority', 'region' => 'Americas', 'days' => 3, 'on_time' => 89],
        'canada-post' => ['name' => 'Canada Post', 'region' => 'Americas', 'days' => 4, 'on_time' => 88],
        'correios' => ['name' => 'Correios', 'region' => 'Americas', 'days' => 7, 'on_time' => 81],
        'royal-mail' => ['name' => 'Royal Mail', 'region' => 'Europe', 'days' => 2, 'on_time' => 91],
        'dpd' => ['name' => 'DPD', 'region' => 'Europe', 'days' => 2, 'on_time' => 95],
        'postnl' => ['name' => 'PostNL', 'region' => 'Europe', 'days' => 3, 'on_time' => 92],
        'colissimo' => ['name' => 'Colissimo', 'region' => 'Europe', 'days' => 4, 'on_time' => 87],
        'japan-post' => ['name' => 'Japan Post EMS', 'region' => 'Asia Pacific', 'days' => 4, 'on_time' => 97],
        'sf-express' => ['name' => 'SF Express', 'region' => 'Asia Pacific', 'days' => 3, 'on_time' => 93],
        'australia-post' => ['name' => 'Australia Post', 'region' => 'Asia Pacific', 'days' => 5, 'on_time' => 86],
        'lbc' => ['name' => 'LBC Express', 'region' => 'Asia Pacific', 'days' => 5, 'on_time' => 84],
    ];

    protected function boot(): void
    {
        $this->paginationPageOptions([5, 10, 'all'])->records(
            fn (?string $search, array $filters, ?string $sortColumn, ?string $sortDirection): array => collect(self::CARRIERS)
                ->when($search !== null, fn (Collection $rows) => $rows->filter(fn (array $row): bool => str_contains(strtolower($row['name']), strtolower((string) $search))))
                ->when(isset($filters['region']['region']), fn (Collection $rows) => $rows->where('region', $filters['region']['region']))
                ->when($sortColumn !== null, fn (Collection $rows) => $rows->sortBy($sortColumn, descending: $sortDirection === 'desc'))
                ->all(),
        );
    }

    public function authorize(?Authenticatable $user): bool
    {
        return $user !== null;
    }

    public function columns(): array
    {
        return [
            TextColumn::make('name')->searchable()->sortable(),
            TextColumn::make('region')->badge(),
            TextColumn::make('days')->label('Delivery')->formatStateUsing(fn (int $state): string => "{$state} days")->sortable(),
            TextColumn::make('on_time')->label('On time')->formatStateUsing(fn (int $state): string => "{$state}%")->sortable(),
        ];
    }

    public function filters(): array
    {
        return [SelectFilter::make('region')->options(['Worldwide' => 'Worldwide', 'Americas' => 'Americas', 'Europe' => 'Europe', 'Asia Pacific' => 'Asia Pacific'])];
    }

    public function recordActions(): array
    {
        return [
            Action::make('ping')
                ->label('Check status')
                ->icon('zap')
                ->authorize(true)
                ->action(fn (ArrayRecord $record) => Notification::make()->title("{$record['name']} is up")->body("Answered in {$record['days']}0 ms.")->success()->send()),
        ];
    }
}
