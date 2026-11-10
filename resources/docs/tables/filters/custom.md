---
title: Custom filters
description: Filter takes your own query, and your own form fields.
---

## A simple switch

```php
use Illuminate\Database\Eloquent\Builder;
use Xelqun\Tables\Filters\Filter;

Filter::make('in_stock')->query(fn (Builder $query) => $query->where('stock', '>', 0));
```

<!-- example: table-filter-custom -->

## With fields

`schema()` takes any form fields, and their values reach the closure as `$data`.

```php
use Xelqun\Forms\Components\DatePicker;

Filter::make('delivery')
    ->schema([
        DatePicker::make('delivery_from'),
        DatePicker::make('delivery_until'),
    ])
    ->query(fn (Builder $query, array $data) => $query
        ->when($data['delivery_from'] ?? null, fn (Builder $query, string $date) => $query->whereDate('delivery_date', '>=', $date))
        ->when($data['delivery_until'] ?? null, fn (Builder $query, string $date) => $query->whereDate('delivery_date', '<=', $date)));
```

Because they're real fields, a filter can depend on another. Pick a year, and the months follow from the server.

## While the filter is off

`apply()` only runs while a filter is active. A filter class of your own can override `applyWhenInactive()` to narrow the query while it's off, such as hiding archived rows until the viewer asks for them.

```php
use Illuminate\Database\Eloquent\Builder;
use Xelqun\Tables\Filters\Filter;

class ArchivedFilter extends Filter
{
    public function apply(Builder $query, array $data): void
    {
        $query->where('status', 'archived');
    }

    public function applyWhenInactive(Builder $query): void
    {
        $query->where('status', '!=', 'archived');
    }
}
```

`ArchivedFilter::make('archived')` then goes in `filters()` like any other. A [ternary filter](/tables/filters/ternary#your-own-queries) does the same with `queries(blank:)`.
