---
title: Text
description: TextColumn shows text, dates, money and badges.
---

```php
use Xelqun\Tables\Columns\TextColumn;

TextColumn::make('name');
TextColumn::make('status')->badge();
TextColumn::make('price')->money('USD');
TextColumn::make('released_at')->date();
TextColumn::make('created_at')->dateTime();
```

<!-- example: table-text -->

## Options

| Method       | What it does                                                          |
| ------------ | --------------------------------------------------------------------- |
| `badge()`    | Shows the value in a pill. A list shows one pill for each value.      |
| `date()`     | Formats a date. The default is `M j, Y`.                              |
| `dateTime()` | Formats a date and time. The default is `M j, Y H:i`.                 |
| `money()`    | Formats a currency. It takes the currency, then an optional locale.   |
| `wrap()`     | Lets long text run onto more lines. Cells stay on one line otherwise. |

Pass a format of your own.

```php
TextColumn::make('released_at')->date('d/m/Y');
TextColumn::make('price')->money('EUR', 'de_DE');
```
