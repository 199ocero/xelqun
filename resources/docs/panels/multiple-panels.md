---
title: Multiple panels and domains
description: Run several panels in one app, each with its own path or its own domain.
---

An app can hold several panels, each with its own provider, navigation and auth.

```bash
php artisan make:xelqun-panel Workspace
```

```php
$panel->id('workspace')->path('workspace');
```

<!-- screenshot: top-navigation | The demo's second panel, a workspace for each team, at /workspace -->

Two panels can't share an id, or the same path on the same domain.

## A panel on its own domain

`domain()` puts a panel on its own host. With `path('')` it sits at the root of that domain.

```php
$panel->id('admin')->domain('admin.example.com')->path('');
```

Without a domain, a panel answers on every host. With one, it only answers on that host. Panels on different domains can share a path.

> [!NOTE]
> This is how the demo runs: [its panel](demo:/) sits at the root of its own domain, beside the docs you're reading.

## Middleware

`middleware()` adds middleware to every route of the panel, sign in included, such as one that sets the locale. It runs once the panel is known. `authMiddleware()` adds it only to the routes behind sign in, after the user is checked.

```php
$panel->middleware([SetLocale::class])->authMiddleware([LogActivity::class]);
```

A panel with tenants also has [`tenantMiddleware()`](/panels/tenancy#middleware-inside-a-tenant).

## Separate logins

`authGuard()` gives a panel its own session guard, and `authPasswordBroker()` its own password broker.

```php
$panel->authGuard('admin')->authPasswordBroker('admins');
```

Both have to exist in `config/auth.php`, with a provider for the guard:

```php
'guards' => [
    // ...
    'admin' => ['driver' => 'session', 'provider' => 'admins'],
],

'providers' => [
    // ...
    'admins' => ['driver' => 'eloquent', 'model' => App\Models\User::class],
],

'passwords' => [
    // ...
    'admins' => [
        'provider' => 'admins',
        'table' => 'password_reset_tokens',
        'expire' => 60,
        'throttle' => 60,
    ],
],
```

`canAccessPanel()` gets the panel, so one user can reach some panels and not others.

```php
public function canAccessPanel(Panel $panel): bool
{
    return $panel->getId() === 'admin' ? (bool) $this->is_admin : true;
}
```

> [!NOTE]
> Signing out of one panel signs out of the others in that session.

[See a second panel in the demo](demo:/workspace).
