---
title: Content grid and stripes
description: Show rows as cards, or shade every other row.
---

## Cards

`contentGrid()` shows rows as cards, this many across on a wide screen.

```php
protected function boot(): void
{
    $this->contentGrid(3);
}
```

<!-- example: table-grid -->

## Stripes

```php
protected function boot(): void
{
    $this->striped();
}
```

<!-- example: table-striped -->
