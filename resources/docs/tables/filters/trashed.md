---
title: Trashed
description: TrashedFilter shows or hides soft deleted records, in a panel.
---

`TrashedFilter` comes with [Xelqun Panels](/panels/resources/soft-deletes).

```php
use Xelqun\Panels\Tables\TrashedFilter;

TrashedFilter::make('trashed')->label('Deleted orders');
```

<!-- example: table-filter-trashed -->
