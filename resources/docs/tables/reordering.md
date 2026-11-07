---
title: Reordering
description: Let rows move up and down by a number column.
---

```php
protected function boot(): void
{
    $this->reorderable('sort', fn (): bool => auth()->user()->isAdmin());
}
```

<!-- example: table-reordering -->

This adds up and down buttons. It's off until the closure says yes, and both rows come through `query()`.

> [!NOTE]
> Rows move with buttons. Drag and drop isn't supported yet.
