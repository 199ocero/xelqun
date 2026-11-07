---
title: Email verification
description: Hold users at a prompt until they verify their address.
---

```php
$panel->emailVerification();
```

The user model needs `MustVerifyEmail`.

```php
use Illuminate\Contracts\Auth\MustVerifyEmail;

class User extends Authenticatable implements MustVerifyEmail, PanelUser
{
    //
}
```

> [!NOTE]
> A queued verification email gets Laravel's route, not the panel's.

[Register in the demo](demo:/register) and you land on this prompt. The link lands in your [demo inbox](demo:/inbox).
