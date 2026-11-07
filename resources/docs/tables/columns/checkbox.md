---
title: Checkbox
description: CheckboxColumn ticks a true or false value right in the cell.
---

```php
use Xelqun\Tables\Columns\CheckboxColumn;

CheckboxColumn::make('is_approved')->authorize('update');
```

<!-- example: table-checkbox -->

It works as a [ToggleColumn](/tables/columns/toggle) does.
