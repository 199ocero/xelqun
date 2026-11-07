---
title: Your first table
description: Build a table in PHP, pass it to an Inertia page, and render it with XqTable.
---

## Define the table

```bash
php artisan make:xelqun-table ProductsTable
```

<!-- example: table-first-table -->

A table needs a `query()` and its `columns()`.

```php
namespace App\Tables;

use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Xelqun\Tables\Columns\TextColumn;
use Xelqun\Tables\Table;

class ProductsTable extends Table
{
    protected function boot(): void
    {
        $this->defaultSort('name')->paginationPageOptions([10, 25, 50]);
    }

    public function query(): Builder
    {
        return Product::query()->where('team_id', $this->context['team']);
    }

    public function columns(): array
    {
        return [
            TextColumn::make('name')->searchable()->sortable(),
            TextColumn::make('price')->money('USD')->sortable(),
        ];
    }
}
```

## Show it

```php
use App\Tables\ProductsTable;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductController
{
    public function index(Request $request)
    {
        return Inertia::render('Products/Index', [
            'table' => ProductsTable::make(['team' => $request->user()->team_id])->toSchema(),
        ]);
    }
}
```

```vue
<script setup lang="ts">
import { XqTable, type TableSchema } from '@xelqun/tables';

defineProps<{ table: TableSchema }>();
</script>

<template>
    <XqTable :table="table" />
</template>
```

## Passing context

`query()` also runs on the table's own requests, where the page's route parameters are gone. Pass what it needs through `make()`, as above. The context travels in an encrypted token, so keep it to scalar values.

## Things to know

- **`authorize()` runs on every request.** It allows everything until you say otherwise.
- **A missing `authorize()` on an action hides its button.**
- **Closures get values by parameter name.** Write `$record`, `$records`, `$state`, `$query`, `$data` or `$direction`. Another name gets a value from the container, not your row.
- **`$set` and `$get` do nothing in a table action,** because the table has no form state. Use the record.
- **Don't use `Action::record()` in a table.** The table already finds the row.
- **Hidden columns are left out entirely.** They aren't sent, searched or sorted.

## Two tables on a page

Each table keeps its state in the URL under its own name, so paging one leaves the other alone. `id()` names a table when a page shows the same class twice.

<!-- example: two-tables -->
