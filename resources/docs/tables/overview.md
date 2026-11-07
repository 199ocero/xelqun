---
title: Overview
description: Xelqun Tables lets you define tables in PHP, with sorting, search, filters, actions and inline editing, and render them with Vue.
---

Xelqun Tables is a table builder for Laravel, Inertia and Vue. A table is a PHP class, and it needs no Vue written for it.

```php
use Illuminate\Database\Eloquent\Builder;
use Xelqun\Tables\Columns\TextColumn;
use Xelqun\Tables\Filters\SelectFilter;
use Xelqun\Tables\Table;

class ProductsTable extends Table
{
    public function query(): Builder
    {
        return Product::query();
    }

    public function columns(): array
    {
        return [
            TextColumn::make('name')->searchable()->sortable(),
            TextColumn::make('category.name')->searchable()->sortable(),
            TextColumn::make('status')->badge(),
            TextColumn::make('price')->money('USD')->sortable(),
        ];
    }

    public function filters(): array
    {
        return [SelectFilter::make('category')->relationship('category', 'name')];
    }
}
```

<!-- example: products -->

This is a table like that one, running. Search, sort or change the page.

## How it works

The browser sends only the table's state, such as the search, the sort and the page. The server rebuilds the table from an encrypted token, runs the query, and returns the rows. The URL keeps up, so a refresh or a shared link shows the same records.

Rows only carry what the columns declare, so nothing else on the model reaches the browser.

## What you get

- **Columns.** Text, icons, images, colors, and cells you edit in place.
- **Search and sorting.** Through relations too.
- **Filters.** Select, ternary, custom forms and a query builder.
- **Actions.** Buttons on rows, above the table, and on selected rows.
- **Summaries and groups.** Totals over every row, and a heading for each group.
- **Layouts.** A grid of cards, stripes, polling and column toggles.

> [!TIP]
> [Open the demo](demo:/products) to see every kind of column and filter in a real admin panel.

## Where to go next

- [Install the package](/tables/installation)
- [Build your first table](/tables/your-first-table)
- [Browse the columns](/tables/columns/overview)
