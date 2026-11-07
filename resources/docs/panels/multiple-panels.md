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

<!-- screenshot: top-navigation | The demo's second panel, with its navigation across the top -->

Two panels can't share an id, or the same path on the same domain.

## A panel on its own domain

`domain()` puts a panel on its own host. With `path('')` it sits at the root of that domain.

```php
$panel->id('admin')->domain('admin.example.com')->path('');
```

Without a domain, a panel answers on every host. With one, it only answers on that host. Panels on different domains can share a path.

> [!NOTE]
> This is how the demo runs: [its panel](demo:/) sits at the root of its own domain, beside the docs you're reading.

## Separate logins

`authGuard()` gives a panel its own session guard, and `authPasswordBroker()` its own password broker.

```php
$panel->authGuard('admin')->authPasswordBroker('admins');
```

`canAccessPanel()` gets the panel, so one user can reach some panels and not others.

```php
public function canAccessPanel(Panel $panel): bool
{
    return $panel->getId() === 'admin' ? $this->is_admin : true;
}
```

> [!NOTE]
> Signing out of one panel signs out of the others in that session.

[See a second panel in the demo](demo:/workspace).
