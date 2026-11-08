---
title: Empty state
description: Show your own message when a table has no rows.
---

```php
protected function boot(): void
{
    $this->emptyState('No comments yet', 'New comments show up here.');
}
```

<!-- example: table-empty-state -->

This replaces the default, "No records found.".
