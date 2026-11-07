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
