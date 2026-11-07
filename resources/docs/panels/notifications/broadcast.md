---
title: Broadcast
description: Update the bell right away, through Laravel Echo.
---

```php
$panel->broadcastNotifications();
```

Send with `broadcast()` for an instant update.

```php
DatabaseNotification::make()->title('Order paid')->broadcast($user);
```

It uses your app's `window.Echo` on the user's private channel, and only sends a signal, so the bell fetches the content itself. Without Echo, the bell keeps polling.

> [!NOTE]
> The public demo has no Echo server, so its bell polls.
