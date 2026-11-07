---
title: Installation
description: Install Xelqun Panels, which sets up forms and tables too, and create your first panel.
---

## Requirements

- PHP 8.4 or newer
- Laravel 13
- Inertia 3 with Vue 3.5
- Tailwind CSS 4

## Install the package

Panels is built on forms and tables, and its installer sets up all three.

```bash
composer require xelqun/panels
php artisan xelqun-panels:install
php artisan make:xelqun-panel Admin
```

The installer adds the matching npm package, two small page files in `resources/js/pages/xelqun`, the styles, and `panelsPlugin()` in your Inertia entry file. The new panel has a login page and a dashboard at `/admin`.

## Let people in

Add `PanelUser` to your user model. Nobody can sign in until you do.

```php
use Illuminate\Foundation\Auth\User as Authenticatable;
use Xelqun\Panels\Contracts\PanelUser;
use Xelqun\Panels\Panel;

class User extends Authenticatable implements PanelUser
{
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->is_admin;
    }
}
```

A user model without it can't enter any panel, in any environment. The check runs on every page and on the endpoints forms and tables use, so taking away access works at once, even on a page that's already open.

Rebuild your frontend with `npm run build` or `npm run dev`, then build [your first panel](/panels/your-first-panel).
