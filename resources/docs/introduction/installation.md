---
title: Installation
description: Choose the Xelqun package you need, and install it in a Laravel app with Inertia and Vue.
---

## Requirements

- PHP 8.4 or newer
- Laravel 13
- Inertia 3 with Vue 3.5
- Tailwind CSS 4

Xelqun Tables also needs PHP's `intl` extension.

Start from an app that already has Inertia and Vue. Laravel's Vue starter kit is the quickest way.

## Installing a panel

Panels is built on Forms and Tables, and its installer sets up all three.

```bash
composer require xelqun/panels
php artisan xelqun-panels:install
php artisan make:xelqun-panel Admin
```

The installer adds the npm packages, connects them to your Inertia entry file, and adds the styles. The new panel has a login page and a dashboard at `/admin`.

Then add `PanelUser` to your user model. Nobody can sign in until you do.

```php
use Illuminate\Foundation\Auth\User as Authenticatable;
use Xelqun\Panels\Contracts\PanelUser;
use Xelqun\Panels\Panel;

class User extends Authenticatable implements PanelUser
{
    public function canAccessPanel(Panel $panel): bool
    {
        return in_array($this->email, ['you@example.com']);
    }
}
```

Put your own email in the list. For other rules, such as a company domain or an admin column, see [Let people in](/panels/installation#let-people-in).

## Installing forms

For forms on your own Inertia pages:

```bash
composer require xelqun/forms
php artisan xelqun-forms:install
```

## Installing tables

For tables on your own Inertia pages. Forms comes with it.

```bash
composer require xelqun/tables
php artisan xelqun-tables:install
```

## Rebuild your frontend

Whichever you chose, rebuild your assets.

```bash
npm run build
```

Next, [build your first panel](/introduction/getting-started).
