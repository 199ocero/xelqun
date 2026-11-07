---
title: Grouping actions
description: ActionGroup puts several actions in one menu.
---

```php
use Xelqun\Actions\Action;
use Xelqun\Actions\ActionGroup;

ActionGroup::make([
    Action::make('edit')->authorize('update')->icon('pencil')->url(fn (): string => route('orders.edit')),
    Action::make('archive')->authorize('update')->icon('folder')->action(fn () => null),
])
    ->label('More')
    ->icon('ellipsis-vertical')
    ->color('gray')
    ->tooltip('More actions');
```

<!-- example: actions-grouping -->

An action in a group takes every option an action does, including modals.
