---
title: Login
description: A login page for each panel, limited to 5 tries a minute per account, with a form you can swap.
---

```php
$panel->login();
```

<!-- screenshot: login | The login page, with the credentials filled in -->

Without it, guests are refused. Auth is built in, per panel, on Laravel's own `Auth`, `Password` and `RateLimiter`, so Fortify isn't needed. Each account gets 5 tries a minute, and each address 20 across all accounts.

## Changing the form

Extend `LoginForm`, and pass the class to `login()`.

```php
use Xelqun\Panels\Auth\LoginForm;

class CustomLoginForm extends LoginForm
{
    public function schema(): array
    {
        [$email, $password, $remember] = parent::schema();

        return [$email->default('demo@example.com'), $password, $remember];
    }
}
```

```php
$panel->login(CustomLoginForm::class);
```

The demo does just this to prefill its login. [See it](demo:/login).
