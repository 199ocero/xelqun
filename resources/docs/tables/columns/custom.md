---
title: Custom columns
description: Render a cell with your own Vue component, one-off or as a reusable column class.
---

## A one-off column

`CustomColumn` renders a Vue component you've registered.

```php
use Xelqun\Tables\Columns\CustomColumn;

CustomColumn::make('stock')
    ->component('app/stock')
    ->props(fn (Product $record): array => ['low' => $record->stock < 20]);
```

<!-- example: table-custom -->

The data is available as `cell.props`.

```ts
app.use(
    createXelqunForms({
        components: {
            'app/stock': () => import('./components/columns/StockCell.vue'),
        },
    }),
);
```

## A reusable column

```bash
php artisan make:xelqun-column StockColumn --type=app/stock
```

That creates the class and its Vue component. Like built-in columns, it can be sorted, searched and linked.

To share one between apps, see [plugins](/tables/plugins).
