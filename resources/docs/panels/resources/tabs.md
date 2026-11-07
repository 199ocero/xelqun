---
title: List tabs
description: Split a resource's list into tabs, with counts.
---

```php
use Illuminate\Database\Eloquent\Builder;
use Xelqun\Panels\Resources\Tab;

public static function getTabs(): array
{
    return [
        'all' => Tab::make(),
        'active' => Tab::make()
            ->badge(fn (): int => Product::where('status', 'active')->count())
            ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'active')),
    ];
}
```

<!-- screenshot: resource-list | Status tabs with counts above a list -->

The first tab is the default. With no label, the tab's key is used. Use a closure in `badge()`, so a count only runs for the page that shows it.

A tab always stays inside `getEloquentQuery()`, even with an `orWhere`, and so does everything on that table.

[See it in the demo](demo:/products).
