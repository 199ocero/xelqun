---
title: Grouping actions
description: ActionGroup puts several actions in one menu.
---

```php
use App\Models\Order;
use Xelqun\Actions\Action;
use Xelqun\Actions\ActionGroup;

ActionGroup::make([
    Action::make('edit')->authorize('update')->icon('pencil')->url(fn (Order $record): string => route('orders.edit', $record)),
    Action::make('archive')->authorize('update')->icon('folder')->action(fn () => null),
])
    ->label('More')
    ->icon('ellipsis-vertical')
    ->color('gray')
    ->tooltip('More actions');
```

<!-- example: actions-grouping -->

An action in a group takes every option an action does, including modals. `authorize('update')` needs a policy for the record's model, or the action is hidden, and a group with no visible actions is hidden too.

A group takes `badge()`, `badgeColor()` and `size()` too, as an action does.
