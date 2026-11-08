---
title: Current limits
description: What Xelqun Tables doesn't do yet.
---

Xelqun is alpha, and a minor version can break existing code until 1.0. These are the known limits today.

## Cells

- A list in a cell has one color and icon, and its `color()` closure receives the array.

## Search and sorting

- `%` and `_` in a search act as wildcards.
- Sorting through a relation works over one `BelongsTo` or `HasOne`. Anything else needs a closure.
- A column that isn't a real column, such as a `withCount()` alias or an accessor, needs a closure to sort or search.

## Filters

- The query builder's rules match all or any, with no nested groups.
- Filters open in one panel. There's no layout above the table.

## Display

- Summaries have sum, average, count and range, with no custom scopes or per-group lines.
- Grouping is set in code, and viewers can't choose a group.
- Rows move with buttons, not drag and drop.
- A table needs an Eloquent query, so custom data isn't supported.
