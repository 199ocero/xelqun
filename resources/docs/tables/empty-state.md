---
title: Empty state
description: Show your own message, icon and buttons when a table has no rows.
---

```php
protected function boot(): void
{
    $this->emptyState('No comments yet', 'New comments show up here.');
}
```

<!-- example: table-empty-state -->

This replaces the default, "No records found.". A [content grid](/tables/layout) shows it too.

## Icon and buttons

The third argument is an icon. `emptyStateActions()` puts buttons under the message, such as one that creates the first record. They're the same [actions](/tables/actions/header) as the ones above the table, with no record.

```php
use App\Models\Comment;
use Xelqun\Actions\Action;
use Xelqun\Forms\Components\TextInput;

protected function boot(): void
{
    $this->emptyState('No comments yet', 'New comments show up here.', 'inbox');
}

public function emptyStateActions(): array
{
    return [
        Action::make('create')
            ->authorize('create')
            ->schema([TextInput::make('name')->required()])
            ->action(fn (array $data) => Comment::create($data)),
    ];
}
```

<!-- example: table-empty-state-actions -->
