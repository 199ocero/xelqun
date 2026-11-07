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

Selected rows reach the closure as `$records`, and the selection can span pages. A `BulkAction` takes the same modals as any action.

`maxSelectableRecords()` sets the most rows one action can take, and the server enforces it.
