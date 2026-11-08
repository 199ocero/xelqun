---
title: Custom columns
description: Render a cell with your own Vue component, one-off or as a reusable column class.
---

## A one-off column

`CustomColumn` renders a Vue component you've registered.

```php
use App\Models\Product;
use Xelqun\Tables\Columns\CustomColumn;

CustomColumn::make('stock')
    ->component('app/stock')
    ->props(fn (Product $record): array => ['low' => $record->stock < 20]);
```

<!-- example: table-custom -->

Register the component in your Inertia entry file.

```ts
app.use(
    createXelqunForms({
        components: {
            'app/stock': () => import('./tables/StockCell.vue'),
        },
    }),
);
```

The component gets the `column` and the `cell`. The cell's value is `cell.state`, and the data from `props()` is `cell.props`.

```vue
<script setup lang="ts">
import type { TableCell, TableColumn } from '@xelqun/tables';

defineProps<{ column: TableColumn; cell: TableCell }>();
</script>

<template>
    <span :class="{ 'text-red-600': cell.props.low }">{{ cell.state }}</span>
</template>
```

## A reusable column

```bash
php artisan make:xelqun-column StockColumn --type=app/stock
```

That creates the class, `app/Tables/Columns/StockColumn.php`, and its Vue component, `resources/js/tables/StockColumnCell.vue`, and prints the line that registers it. Like built-in columns, it can be sorted, searched and linked.

To share one between apps, see [plugins](/tables/plugins).
