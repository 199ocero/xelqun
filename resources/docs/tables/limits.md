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
- A column that isn't a real column, such as a `withCount()` alias or an accessor, needs a closure to sort or search. `counts()` and `sum()` sort on their own.
- Cursor pagination can't follow a sort through a relation, or on `counts()` or `sum()`. Use the default pagination for those tables.

## Filters

- The query builder's rules match all or any, with no nested groups.
- Filters open in one panel. There's no layout above the table.

## Selection

- With `checkIfRecordIsSelectableUsing()`, select all checks the rows one at a time in PHP. On a table where most rows can't be selected, it gets slow.
- `authorizeIndividualRecords()` asks the policy about each selected row in PHP, and puts the allowed keys back into the query as a list. A very big selection makes a long query.

## Display

- Summaries have sum, average, count and range, with no custom scopes or per-group lines.
- Rows move with buttons, not drag and drop.
- A table needs an Eloquent query, so custom data isn't supported.
