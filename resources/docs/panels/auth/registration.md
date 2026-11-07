---
title: Registration
description: Let people sign up, off by default.
---

```php
$panel->registration();
```

<!-- screenshot: registration | The sign up page -->

It's off by default, and a new user still has to pass `canAccessPanel()`. Pass a form class to change the fields.

```php
$panel->registration(CustomRegisterForm::class);
```

[Try it in the demo](demo:/register).
