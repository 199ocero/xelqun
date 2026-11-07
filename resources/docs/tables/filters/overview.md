---
title: Overview
description: Filters narrow a table's rows, and are real form fields that live in one panel.
---

```php
use Illuminate\Database\Eloquent\Builder;
use Xelqun\Tables\Filters\Filter;
use Xelqun\Tables\Filters\SelectFilter;

public function filters(): array
{
    return [
        SelectFilter::make('status')->options(['draft' => 'Draft', 'active' => 'Active']),
        Filter::make('in_stock')->query(fn (Builder $query) => $query->where('stock', '>', 0)),
    ];
}
```

<!-- example: table-filters -->

The URL keeps the active filters, so a shared link shows the same rows.

| Filter                                        | Use it for                       |
| --------------------------------------------- | -------------------------------- |
| [SelectFilter](/tables/filters/select)        | One or many values               |
| [TernaryFilter](/tables/filters/ternary)      | Yes, no, or either               |
| [QueryBuilder](/tables/filters/query-builder) | Rules people build themselves    |
| [Filter](/tables/filters/custom)              | Your own fields and query        |
| [TrashedFilter](/tables/filters/trashed)      | Soft deleted records, in a panel |

## Shared options

`label()` changes the name. `default()` sets the value before the viewer changes anything. `hidden()` removes the filter.

```php
SelectFilter::make('status')->options($statuses)->default('active');
```
