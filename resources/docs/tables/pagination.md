---
title: Pagination
description: Choose the page sizes a table offers.
---

`paginationPageOptions()` in `boot()` sets the sizes viewers can choose.

```php
protected function boot(): void
{
    $this->paginationPageOptions([10, 25, 50]);
}
```

<!-- example: table-pagination -->

The URL keeps the page, so a refresh or a shared link stays where it was.
