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

Add `PanelUser` to your user model. Nobody can sign in until you do. `canAccessPanel()` returns `true` for each user who may enter, so you don't need a new column. The simplest rule lists who gets in:

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

Other rules that need no migration:

```php
// Everyone who has an account. If you turn on registration, anyone who signs up gets in.
return true;

// Anyone with a verified email on your company's domain.
return str_ends_with($this->email, '@yourcompany.com') && $this->hasVerifiedEmail();
```

### Use an admin column

When admins change often, keep a flag on the user instead:

```php
public function canAccessPanel(Panel $panel): bool
{
    return $this->is_admin;
}
```

The `users` table has no `is_admin` column by default. Add one with a migration:

```bash
php artisan make:migration add_is_admin_to_users_table
```

```php
Schema::table('users', function (Blueprint $table) {
    $table->boolean('is_admin')->default(false);
});
```

Cast it in the user model, with the casts it already has:

```php
protected function casts(): array
{
    return [
        // ...
        'is_admin' => 'boolean',
    ];
}
```

Run `php artisan migrate`, then make yourself an admin:

```bash
php artisan tinker --execute 'App\Models\User::where("email", "you@example.com")->update(["is_admin" => true]);'
```

Whichever rule you pick, a user model without `PanelUser` can't enter any panel, in any environment. The check runs on every page and on the endpoints forms and tables use, so taking away access works at once, even on a page that's already open.

Rebuild your frontend with `npm run build` or `npm run dev`, then build [your first panel](/panels/your-first-panel).
