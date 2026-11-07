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

Two-factor is set up here too. See [two-factor](/panels/auth/two-factor).

[See it in the demo](demo:/profile). The shared demo account can't be changed, so register your own to try it.
