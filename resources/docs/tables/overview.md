---
title: Overview
description: Xelqun Tables lets you define tables in PHP, with sorting, search, filters, actions and inline editing, and render them with Vue.
---

Xelqun Tables is a table builder for Laravel, Inertia and Vue. A table is a PHP class, and it needs no Vue written for it.

```php
use App\Models\Product;
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

## Picking records in a form

`ModalTableSelect` is a [form field](/forms/fields/select) that tables brings. It picks related records from a table in a modal, with the table's search, filters and pages, instead of a dropdown.

```php
use App\Tables\CategoriesTable;
use Xelqun\Tables\Forms\ModalTableSelect;

ModalTableSelect::make('category_id')
    ->relationship('category', 'name')
    ->tableConfiguration(CategoriesTable::class);
```

It needs `relationship()`, and the server checks each picked key against it. `multiple()` picks several, as on a select. The table's `query()` lists the records to pick from, and its `authorize()` decides who may open it. Its rows load when the modal opens, so the form costs no table query.

Give the picker table a column named after the title attribute, `name` here, so a new pick shows its label.

The field's Vue component comes with `tablesPlugin()`. The installer doesn't add it, so register it in your entry file beside the forms plugin.

```ts
import { createXelqunForms } from '@xelqun/forms';
import { tablesPlugin } from '@xelqun/tables';

app.use(createXelqunForms({ plugins: [tablesPlugin()] }));
```

> [!TIP]
> [Open the demo](demo:/products) to see every kind of column and filter in a real admin panel.

## Where to go next

- [Install the package](/tables/installation)
- [Build your first table](/tables/your-first-table)
- [Browse the columns](/tables/columns/overview)
