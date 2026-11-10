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

A closure replaces the query, which suits a column that isn't a real one. Its conditions are grouped on their own, so starting with `where()` leaves the other columns' searches alone.

```php
use Illuminate\Database\Eloquent\Builder;

TextColumn::make('full_name')->searchable(query: fn (Builder $query, string $search) => $query
    ->where('first_name', 'like', "%{$search}%")
    ->orWhere('last_name', 'like', "%{$search}%"));
```

## A search box for one column

`isIndividual: true` gives a column a search box of its own, under its heading. `isGlobal: false` keeps it out of the search above the table, so it's only searched through its own box.

```php
TextColumn::make('sku')->searchable(isIndividual: true, isGlobal: false);
```

A column search and the search above narrow the rows together, and the URL keeps both.

## Sorting

```php
TextColumn::make('price')->sortable();
```

Sorting through a relation works over one `BelongsTo` or `HasOne`. Anything else needs a closure, and so does a column that isn't a real one, such as a `withCount()` alias or an accessor. A column with [`counts()` or `sum()`](/tables/columns/overview#related-totals) sorts on its own.

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

## Remembering them

The URL keeps the search, the sort and the filters. To keep them for the viewer's next visit too, even without the link, keep them in the session.

```php
protected function boot(): void
{
    $this->persistSearchInSession()
        ->persistSortInSession()
        ->persistFiltersInSession()
        ->persistColumnSearchesInSession();
}
```

Each one keeps its part for this table. A link with state of its own wins over what the session kept.
