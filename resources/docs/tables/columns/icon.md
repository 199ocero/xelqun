---
title: Icon
description: IconColumn shows an icon, or a check and a cross for a true or false value.
---

```php
use Xelqun\Tables\Columns\IconColumn;

IconColumn::make('is_gift')->label('Gift')->boolean();
```

<!-- example: table-icon -->

`boolean()` shows a check for a true value and a cross for a false one.

Without it, `icon()` and `color()` choose what to show.

```php
IconColumn::make('status')
    ->icon(fn (string $state): string => $state === 'active' ? 'circle-check' : 'circle-alert')
    ->color(fn (string $state): string => $state === 'active' ? 'success' : 'warning');
```
