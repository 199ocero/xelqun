---
title: Overview
description: Filters narrow a table's rows, and are real form fields that live in one panel.
---

```php
use Illuminate\Database\Eloquent\Builder;
use Xelqun\Tables\Filters\Filter;
use Xelqun\Tables\Filters\SelectFilter;

public function filters(): array
{
    return [
        SelectFilter::make('status')->options(['draft' => 'Draft', 'active' => 'Active']),
        Filter::make('in_stock')->query(fn (Builder $query) => $query->where('stock', '>', 0)),
    ];
}
```

<!-- example: table-filters -->

The URL keeps the active filters, so a shared link shows the same rows.

| Filter                                        | Use it for                       |
| --------------------------------------------- | -------------------------------- |
| [SelectFilter](/tables/filters/select)        | One or many values               |
| [TernaryFilter](/tables/filters/ternary)      | Yes, no, or either               |
| [QueryBuilder](/tables/filters/query-builder) | Rules people build themselves    |
| [Filter](/tables/filters/custom)              | Your own fields and query        |
| [TrashedFilter](/tables/filters/trashed)      | Soft deleted records, in a panel |

## Shared options

`label()` changes the name. `default()` sets the value before the viewer changes anything. `hidden()` removes the filter, and `visible()` is its opposite.

```php
SelectFilter::make('status')->options($statuses)->default('active');
```

## Chips

Each active filter shows a chip above the table, and its x removes the filter. A select chip reads like "Status: Active", a ternary one like "Featured: Yes", and any other the filter's label.

<!-- example: table-filter-chips -->

`indicateUsing()` says what the chip reads. It gets `$data`, this filter's values, and returns a string, a list of them for one chip each, or null for none. Removing any of them clears the whole filter.

```php
Filter::make('price_range')
    ->schema([TextInput::make('min')->numeric(), TextInput::make('max')->numeric()])
    ->query($query)
    ->indicateUsing(fn (array $data): array => [
        filled($data['min'] ?? null) ? "From \${$data['min']}" : null,
        filled($data['max'] ?? null) ? "Up to \${$data['max']}" : null,
    ]);
```

## Applying as they change

By default, filters wait for the Apply button. `deferFilters(false)` in `boot()` applies each one as it changes, with no button.

```php
protected function boot(): void
{
    $this->deferFilters(false);
}
```
