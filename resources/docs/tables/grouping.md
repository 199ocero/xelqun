---
title: Grouping
description: Put rows under a heading for each value of a column, and let viewers pick the grouping.
---

```php
protected function boot(): void
{
    $this->defaultGroup('status');
}
```

<!-- example: table-grouping -->

Each value gets a heading, and rows sort by it first, so a group stays together whatever the sort.

A related value, such as `category.name`, needs a column with that name, and groups the way that column sorts. Without the column, the table throws a `TableDefinitionException`.

## Letting viewers choose

`groups()` lists the groupings a viewer picks from, above the table. They can also pick no grouping. Each one is a column name or a `Group`.

```php
use Xelqun\Tables\Group;

protected function boot(): void
{
    $this->groups([
        'status',
        Group::make('category.name')->label('Category')->collapsible(),
    ]);
}
```

<!-- example: table-groups -->

`label()` names the grouping in the menu. `collapsible()` lets the viewer fold each group away. The URL keeps the choice, and `defaultGroup()` still sets the one before the viewer picks.

> [!NOTE]
> Summaries have no per-group lines yet.
