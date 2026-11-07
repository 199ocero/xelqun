---
title: Password reset
description: Emailed reset links, with an answer that never says whether an address has an account.
---

```php
$panel->passwordReset();
```

<!-- screenshot: password-reset | The forgot password page -->

The answer never says whether an address has an account. It takes two form classes, for the request and for the reset.

`authPasswordBroker()` gives a panel its own broker.

[Try it in the demo](demo:/forgot-password). The email lands in your [demo inbox](demo:/inbox).
