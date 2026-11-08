---
title: Polling
description: Keep a table fresh by checking for changes on a timer.
---

```php
protected function boot(): void
{
    $this->poll('10s');
}
```

<!-- example: table-polling -->

The interval is seconds or minutes, as in `'10s'` or `'1m'`. Anything else throws a `TableDefinitionException`.
