---
title: Queued notifications
description: Notifications wait in the session and show on the next response, so they survive a redirect.
---

`send()` puts a notification in a queue for this session. It shows on the next response, whether that's a redirect, an Inertia visit, or an endpoint of the form.

```php
public function update(Request $request, Order $order)
{
    OrderForm::make($order)->save($request);

    Notification::make()->title('Saved')->success()->send();

    return back();
}
```

There's nothing to pass to your page.

## Sending from a job

A queued job has no session, so this queue can't reach a person from one. In a panel, use [database notifications](/panels/notifications/database) for that.
