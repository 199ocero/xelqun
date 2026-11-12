---
title: Before going to production
description: The checks to make before a Xelqun panel goes live.
---

- **Check `canAccessPanel()`.** It decides who gets in, in every environment.
- **Write a policy for every model.** With no policy, or no such method, the answer is no, and a method that returns `true` lets everyone with panel access through.
- **Scope `getEloquentQuery()` to what the viewer may see.** Every page, action and form request stays inside it, so this is your main line of defence.
- **Give each action its own `authorize()`.** An action does nothing without one.
- **Set up trusted proxies behind a proxy.** Otherwise Laravel doesn't see the request as secure, and the browser's history isn't encrypted.
- **Trust only your own hosts.** Password reset links are built from the request's host. Add `$middleware->trustHosts()` in `bootstrap/app.php`, unless your web server already refuses other hosts.
- **Schedule the upload cleanup.** Temporary uploads stay on disk until it runs.

```php
use Illuminate\Support\Facades\Schedule;

Schedule::command('xelqun-forms:prune-uploads')->hourly();
```

## Current limits

- `%` and `_` in a search act as wildcards.
- `discoverResources()` walks its folder on every request.
- Export ignores table filters, and only follows the tab.
- Signing out of one panel signs out of the others in that session.
- A queued verification email gets Laravel's route, not the panel's.

See [parity with Filament](/panels/parity) for the full comparison.
