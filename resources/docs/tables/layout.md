---
title: Content grid and stripes
description: Show rows as cards, shade every other row, and put a heading above the table.
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

Cards have no column headers, so a Sort by menu above them lists the sortable columns.

## Stripes

```php
protected function boot(): void
{
    $this->striped();
}
```

<!-- example: table-striped -->

## Heading

`heading()` puts a title above the table, as on a dashboard, and `description()` puts smaller text under it.

```php
protected function boot(): void
{
    $this->heading('Low stock')->description('Products with fewer than ten left.');
}
```
