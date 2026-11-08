---
title: Bulk actions
description: Buttons for the selected rows, across pages, with a limit the server enforces.
---

```php
use Illuminate\Database\Eloquent\Collection;
use Xelqun\Tables\Actions\BulkAction;

public function bulkActions(): array
{
    return [
        BulkAction::make('delete')
            ->authorize('delete')
            ->requiresConfirmation()
            ->action(fn (Collection $records) => $records->each->delete()),
    ];
}
```

<!-- example: table-bulk-actions -->

Selected rows reach the closure as `$records`, and the selection can span pages. A `BulkAction` takes the same modals as any action. `authorize('delete')` asks the [policy](/tables/your-first-table#things-to-know) of each selected row when the action runs.

`maxSelectableRecords()` sets the most rows one action can take, and the server enforces it. The default is 1,000.

## Many rows at once

`$records` loads every selected row as a model in one request. For a big selection, take `$query` instead: the same rows, still inside the table's query, and nothing loads until you go through them.

```php
use Illuminate\Database\Eloquent\Builder;

BulkAction::make('archive')
    ->authorize('update')
    ->action(fn (Builder $query) => $query->chunkById(500, fn ($rows) => $rows->each->archive())),
```

A closure gets `$records` only when it asks for it, and a policy checks the selection one row at a time, so an action that only takes `$query` stays small however many rows are selected. Use `$query->update([...])` when a row needs no model events.
