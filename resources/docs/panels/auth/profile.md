---
title: Profile
description: A page for people to change their name, email and password.
---

```php
$panel->profile();
```

<!-- screenshot: profile | The profile page -->

Pass a form class to change the fields.

```php
$panel->profile(CustomProfileForm::class);
```

Changing the email address or the password asks for the current password.

Two-factor is set up here too. See [two-factor](/panels/auth/two-factor).

## Verifying a new email address

```php
$panel->profile()->emailChangeVerification();
```

A new email address then waits until someone opens the link sent to it, and the old address is told about the change. Until then, the account keeps the old one, and a link stops working once the address has changed again.

[See it in the demo](demo:/profile). The shared demo account can't be changed, so register your own to try it: change its email, and the link arrives in the demo's inbox.
