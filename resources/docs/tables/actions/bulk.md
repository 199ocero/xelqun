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

`maxSelectableRecords()` sets the most rows one action can take, and the server enforces it. The default is 1,000. The action loads every selected row as a model in one request, so raising it far needs the memory for that many models.
