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
