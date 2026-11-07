---
title: Grouping
description: Put rows under a heading for each value of a column.
---

```php
protected function boot(): void
{
    $this->defaultGroup('status');
}
```

<!-- example: table-grouping -->

Each value gets a heading, and rows sort by it first, so a group stays together whatever the sort.

> [!NOTE]
> Grouping is set in code. Viewers can't choose a group yet, and summaries have no per-group lines.
