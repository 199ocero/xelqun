---
title: Search and sorting
description: Let viewers search and sort, through relations too, and set the default order.
---

## Search

```php
TextColumn::make('name')->searchable();
TextColumn::make('category.name')->searchable();
```

<!-- example: table-search-and-sorting -->

A closure replaces the query, which suits a column that isn't a real one.

```php
use Illuminate\Database\Eloquent\Builder;

TextColumn::make('full_name')->searchable(query: fn (Builder $query, string $search) => $query
    ->where('first_name', 'like', "%{$search}%")
    ->orWhere('last_name', 'like', "%{$search}%"));
```

## Sorting

```php
TextColumn::make('price')->sortable();
```

Sorting through a relation works over one `BelongsTo` or `HasOne`. Anything else needs a closure, and so does a column that isn't a real one, such as a `withCount()` alias or an accessor.

```php
TextColumn::make('products_count')->sortable(query: fn (Builder $query, string $direction) => $query->orderBy('products_count', $direction));
```

## The default order

`defaultSort()` in `boot()` sets the order before the viewer picks one. The column doesn't have to be sortable.

```php
protected function boot(): void
{
    $this->defaultSort('created_at', 'desc');
}
```
