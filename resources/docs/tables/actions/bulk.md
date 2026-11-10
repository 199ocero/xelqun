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

## Rows that can't be selected

`checkIfRecordIsSelectableUsing()` in `boot()` decides which rows have a checkbox. It gets `$record`. The server checks it too, so a row it refuses never reaches a bulk action or select all.

```php
use App\Models\Product;

protected function boot(): void
{
    $this->checkIfRecordIsSelectableUsing(fn (Product $record): bool => $record->status !== 'archived');
}
```

Select all then checks the rows one at a time, in PHP, until it has enough. On a table where most rows can't be selected, that gets slow.

## Allowing some of the selection

`authorize('archive')` refuses the whole action if any selected row fails the policy. `authorizeIndividualRecords('archive')` runs it on the rows the policy allows instead, and leaves the others out of `$records` and `$query`. The button shows while any of the selection is allowed.

The action still needs its own `authorize()`, so pair it with `authorize(true)`.

```php
BulkAction::make('archive')
    ->authorize(true)
    ->authorizeIndividualRecords('archive')
    ->action(fn (Collection $records) => $records->each->archive());
```

## Keeping the selection

`$action->halt()` stops the action and keeps its modal open, and the selection stays as it was. See [record actions](/tables/actions/record#keeping-the-modal-open).
