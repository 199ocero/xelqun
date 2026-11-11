---
title: Import and export
description: Download a list as CSV, and read one back in through the resource's form.
---

```php
use Xelqun\Panels\Actions\ExportAction;
use Xelqun\Panels\Actions\ImportAction;

public static function getHeaderActions(): array
{
    return [ExportAction::for(static::class), ImportAction::for(static::class)];
}
```

<!-- screenshot: import-action | The import modal, with export beside it -->

## Export

Export downloads the list as CSV, inside `getEloquentQuery()` and the current tab, using the table's columns. Override `getExportColumns()` to change them. A column the table shows is written as the table formats it, so `money()`, `date()` and `formatStateUsing()` apply. The download asks the same as the export button: the action's `authorize()`, whether it's shown and enabled, and `viewAny`. Any cell that a spreadsheet would run as a formula is kept as text.

## Import

Import reads a CSV whose first row names the columns. Each row goes through the resource's form, so it gets the same validation, hooks and tenant as a record typed in, and bad rows are counted and skipped. Only the columns from `getImportColumns()` are read, which by default are the form's fields.

A header matches a column by its name or label, ignoring case, spaces, dashes and underscores, so `Product Name` fills `product_name`. For other headers, rules or a column the file must have, return `ImportColumn`s:

```php
use Xelqun\Panels\Actions\ImportColumn;

public static function getImportColumns(): array
{
    return [
        ImportColumn::make('sku')->label('SKU')->guesses(['item code'])->requiredMapping(),
        ImportColumn::make('price')->rules(['numeric', 'min:0']),
        'name',
    ];
}
```

| Method              | Does                                                               |
| ------------------- | ------------------------------------------------------------------ |
| `label()`           | The name in the modal, and another header that matches             |
| `guesses()`         | More headers that match                                            |
| `rules()`           | Rules each value must pass, on top of the form's                   |
| `requiredMapping()` | Refuse a file with no header for this column, before a row is read |

### Big files

In the request, an import stops at 1,000 rows and tells the viewer how many it skipped. `queue()` reads the whole file in a queued job instead:

```php
ImportAction::for(static::class)->queue()
```

The job runs as the user who started it, in their panel, tenant and parent record. It checks again that they can still reach them and create records, then tells them the result in their bell, so the panel needs [database notifications](/panels/notifications/database). It runs once and never retries, so a crash can't import rows twice. Pass a queue's name as the second argument: `queue(queue: 'imports')`.

> [!NOTE]
> Headers are matched for you, with no step to pick them by hand, and there's no file of the rows that failed. Export is CSV only, and ignores table filters.

[See them in the demo](demo:/products).
