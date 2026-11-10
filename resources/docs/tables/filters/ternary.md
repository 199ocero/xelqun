---
title: Ternary
description: TernaryFilter narrows rows to yes, no, or either.
---

```php
use Xelqun\Tables\Filters\TernaryFilter;

TernaryFilter::make('is_featured')->label('Featured');
```

<!-- example: table-filter-ternary -->

`attribute()` sets the column, which defaults to the filter's name.

## Labels

`trueLabel()` and `falseLabel()` rename the two choices, which read Yes and No otherwise. `placeholder()` is what the select says while neither is picked.

```php
TernaryFilter::make('is_featured')
    ->label('Featured')
    ->trueLabel('Featured only')
    ->falseLabel('Not featured')
    ->placeholder('All products');
```

## A nullable column

`nullable()` makes yes mean the column has a value, and no mean it's null. It suits a timestamp such as `email_verified_at`.

```php
TernaryFilter::make('email_verified_at')->label('Verified')->nullable();
```

## Your own queries

`queries()` takes a query for yes, for no, and for neither picked. Each closure gets `$query`. The `blank` one runs while the filter is off.

```php
use Illuminate\Database\Eloquent\Builder;

TernaryFilter::make('archived')
    ->queries(
        true: fn (Builder $query) => $query->where('status', 'archived'),
        false: fn (Builder $query) => $query->where('status', '!=', 'archived'),
        blank: fn (Builder $query) => $query->where('status', '!=', 'archived'),
    );
```

Here archived rows stay hidden until the viewer asks for them.
