---
title: Overview
description: Send a toast from an action, a hook or a controller, and it reaches the page by itself.
---

```php
use Xelqun\Notifications\Notification;

Notification::make()
    ->title('Saved')
    ->body('Your changes are live.')
    ->success()
    ->send();
```

<!-- example: notifications -->

The toaster needs no setup. Only five show at a time, and hovering pauses their timers.

## Options

| Method                                         | What it does                           |
| ---------------------------------------------- | -------------------------------------- |
| `title()`, `body()`                            | The text.                              |
| `success()`, `info()`, `warning()`, `danger()` | The status, which brings its own icon. |
| `icon()`                                       | Replaces the status's icon.            |
| `duration()`                                   | How long it stays, in milliseconds.    |
| `persistent()`                                 | Keeps it on screen until it's closed.  |

## From an action

`successNotificationTitle()` is the short way, once `action()` has returned.

```php
Action::make('save')
    ->authorize(true)
    ->successNotificationTitle('Saved')
    ->action(fn () => null);
```

## Buttons

`actions()` adds buttons under the text. Add `persistent()` so people have time to use them.

```php
use Xelqun\Notifications\NotificationAction;

Notification::make()
    ->title('Order shipped')
    ->persistent()
    ->actions([
        NotificationAction::make('view')->label('View')->url(route('orders.show', $order)),
    ])
    ->send();
```

`run()` runs an action of an [ActionSet](/forms/actions/standalone), then closes the notification. `close()` closes it when the button is used.

A panel can also keep notifications in the database. See [database notifications](/panels/notifications/database).
