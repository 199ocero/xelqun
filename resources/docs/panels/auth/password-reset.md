---
title: Password reset
description: Emailed reset links, with an answer that never says whether an address has an account.
---

```php
$panel->passwordReset();
```

<!-- screenshot: password-reset | The forgot password page -->

The answer never says whether an address has an account. It takes two form classes, for the request and for the reset.

Changing or resetting a password signs out every other session. The one that made the change stays signed in.

The link in the email is built from the request's host, so [trust only your own hosts](/panels/production) in production.

`authPasswordBroker()` gives a panel its own broker.

[Try it in the demo](demo:/forgot-password). The email lands in your [demo inbox](demo:/inbox).
