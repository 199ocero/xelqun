---
title: Toggle
description: ToggleColumn switches a true or false value right in the cell.
---

```php
use Xelqun\Tables\Columns\ToggleColumn;

ToggleColumn::make('is_featured')->label('Featured')->authorize('update');
```

<!-- example: table-toggle -->

It's read-only until `authorize()` says who can edit. `updateStateUsing()` saves it your own way, and `rules()` adds validation.
