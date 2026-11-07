---
title: Select
description: SelectColumn picks a value right in the cell, and saves it as soon as it changes.
---

```php
use Xelqun\Tables\Columns\SelectColumn;

SelectColumn::make('status')
    ->authorize('update')
    ->options(['draft' => 'Draft', 'active' => 'Active', 'archived' => 'Archived']);
```

<!-- example: table-select -->

An editable column saves as you change it. It's read-only until `authorize()` says who can edit, the same as an action.

The row comes through `query()`, and a select only takes its own options.

## Saving it yourself

For a relation's column, or anything else, use `updateStateUsing()`.

```php
SelectColumn::make('author.status')
    ->authorize('update')
    ->options($statuses)
    ->updateStateUsing(fn (string $state, Post $record) => $record->author->update(['status' => $state]));
```
