---
title: Pagination
description: Choose the page sizes a table offers, how it pages, and when its rows load.
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

## Every row

`'all'` can be one of the sizes, and puts every row on one page. `paginated(false)` shows every row with no pager at all, and `paginated([...])` sets the sizes like `paginationPageOptions()`.

```php
$this->paginationPageOptions([25, 50, 'all']);

$this->paginated(false);
```

## Without counting rows

The default pager counts the rows for its page numbers. On a big table that count is slow, so `paginationMode()` can skip it.

| Mode      | What it does                                                                                       |
| --------- | -------------------------------------------------------------------------------------------------- |
| `default` | Counts the rows, and shows page numbers.                                                           |
| `simple`  | Skips the count, and offers previous and next.                                                     |
| `cursor`  | Skips the count too, and pages from where the last page ended, so it stays fast deep into a table. |

```php
protected function boot(): void
{
    $this->paginationMode('cursor');
}
```

Neither one counts the rows, so neither offers select all across pages.

<!-- example: table-cursor-pagination -->

> [!WARNING]
> Cursor pagination reads plain columns. A sort through a relation, or on `counts()` or `sum()`, fails with it. Use the default mode for a table that sorts that way.

## Loading the rows later

`deferLoading()` renders the page without rows, and loads them right after. It suits a slow query on a page that shows more than the table.

```php
protected function boot(): void
{
    $this->deferLoading();
}
```

<!-- example: table-deferred-loading -->
