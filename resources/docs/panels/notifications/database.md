---
title: Database
description: A bell in the top bar that shows notifications stored in the database.
---

```php
$panel->databaseNotifications();
```

<!-- screenshot: notifications | The notifications open under the bell -->

This adds a bell to the top bar, which checks every 30 seconds. It needs Laravel's table, from `php artisan make:notifications-table`, and `Notifiable` on your user model.

```php
use Xelqun\Panels\Notifications\DatabaseNotification;

DatabaseNotification::make()
    ->title('Order paid')
    ->body('Order 1001 is ready to ship.')
    ->success()
    ->url(OrderResource::getUrl('view', ['record' => $order]))
    ->sendToDatabase($user);
```

This works from a job, an observer or a webhook too. `getUrl()` finds the panel when only one registers the resource, and takes the panel as a [third argument](/panels/resources/pages#links) when several do.

`databaseNotificationsPolling()` changes how often it checks, and `null` checks only on page load.

```php
$panel->databaseNotificationsPolling('60s');
```

## Icons and actions

The bell shows a notification's `icon()`, and those of its actions that link somewhere.

```php
use Xelqun\Notifications\NotificationAction;

DatabaseNotification::make()
    ->title('Your weekly report is ready')
    ->icon('chart-column')
    ->actions([NotificationAction::make('report')->label('See the report')->url(route('reports'))])
    ->sendToDatabase($user);
```

An action that runs code shows in the toast only. Its token has expired by the time anyone opens the bell.

[Try it in the demo](demo:/): press "Send me a notification", then open the bell for its icon and its "See products" link.

<!-- screenshot: notification-actions | Notifications in the bell, with an icon and a link action -->
