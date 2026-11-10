---
title: Text
description: TextColumn shows text, dates, numbers, money, lists and badges.
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
| `time()`     | Formats a time. The default is `H:i:s`.                               |
| `since()`    | Shows how long ago, such as "3 days ago".                             |
| `money()`    | Formats a currency. It takes the currency, then an optional locale.   |
| `numeric()`  | Formats a number with the locale's separators.                        |
| `wrap()`     | Lets long text run onto more lines. Cells stay on one line otherwise. |

Pass a format of your own.

```php
TextColumn::make('released_at')->date('d/m/Y');
TextColumn::make('price')->money('EUR', 'de_DE');
TextColumn::make('weight')->numeric(decimalPlaces: 2);
```

## Time zones

Dates and times show in the app's time zone. `timezone()` picks another, and takes a closure too. `date()`, `dateTime()`, `time()` and `since()` also take one as their last argument.

```php
TextColumn::make('created_at')->dateTime()->timezone('Europe/Paris');
TextColumn::make('shipped_at')->time('H:i', 'America/New_York');
```

## Description and placeholder

`description()` puts smaller text under the value. Pass `'above'` as the second argument to put it over the value. A closure gets `$record` and `$state`.

`placeholder()` is what an empty cell shows, in a muted color.

```php
use App\Models\Product;

TextColumn::make('name')->description(fn (Product $record): string => $record->sku);
TextColumn::make('restock_at')->date()->placeholder('Not planned');
```

<!-- example: table-text-description -->

## Long text

`limit()` cuts the text to that many characters, and `words()` to that many words. The whole text shows on hover, unless `tooltip()` sets its own. `lineClamp()` wraps the text, but shows at most that many lines.

```php
TextColumn::make('body')->limit(40);
TextColumn::make('body')->words(8);
TextColumn::make('body')->lineClamp(2);
```

`tooltip()` sets the hover text yourself, on any column. A closure gets `$record` and `$state`.

```php
TextColumn::make('status')->tooltip('Approved comments show on the site');
```

<!-- example: table-text-limit -->

## Copying

`copyable()` adds a button that copies the value. `copyMessage()` changes what it says after a copy, and `copyMessageDuration()` sets how long, in milliseconds.

```php
TextColumn::make('sku')
    ->copyable()
    ->copyMessage('SKU copied')
    ->copyMessageDuration(1500);
```

<!-- example: table-text-copyable -->

## Lists

An array, or a relation with many records, shows as a list. By default it's one line, with commas.

| Method                    | What it does                                                                    |
| ------------------------- | ------------------------------------------------------------------------------- |
| `listWithLineBreaks()`    | Puts one value on each line.                                                    |
| `bulleted()`              | Puts one value on each line, with a bullet.                                     |
| `limitList()`             | Shows only the first values, and how many more there are.                       |
| `expandableLimitedList()` | Lets the viewer open the rest. It needs `listWithLineBreaks()` or `bulleted()`. |

```php
TextColumn::make('keywords')
    ->bulleted()
    ->limitList(2)
    ->expandableLimitedList();
```

<!-- example: table-text-list -->
