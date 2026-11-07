---
title: Overview
description: Xelqun Panels builds full admin panels from PHP, with auth, CRUD pages, dashboards, tenancy and notifications, on Laravel, Inertia and Vue.
---

Xelqun Panels turns PHP classes into a complete admin area. Normal panels need no frontend code, and you can add your own Vue page when you want one.

```php
use Xelqun\Panels\Panel;
use Xelqun\Panels\PanelProvider;
use Xelqun\Panels\Support\Colors\Color;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('admin')
            ->path('admin')
            ->brandName('Acme')
            ->colors(['primary' => Color::Blue])
            ->login()
            ->passwordReset()
            ->profile()
            ->resources([ProductResource::class])
            ->pages([Dashboard::class])
            ->widgets([OrderStats::class]);
    }
}
```

<!-- screenshot: dashboard | A Xelqun admin panel, with a sidebar, stats and charts -->

That's a signed-in admin with a dashboard and list, create, edit and view pages for products.

> [!TIP]
> The best way to see it is to use it. [Open the demo](demo:/), sign in with one click, and change whatever you like.

## What you get

- **Resources.** One class per model gives you CRUD pages, filters, actions, soft deletes, CSV export and import, relation managers and nested resources.
- **Closed by default.** Nobody gets in until `canAccessPanel()` says so, and with no policy the answer is no.
- **Authentication built in.** Login, registration, password reset, email verification and profiles, plus two-factor by app or email. Fortify isn't needed.
- **Multi-tenancy.** Split a panel between teams or shops. Records stay in their tenant for every query.
- **Dashboards.** Stats, lists, tiles, tables and charts, with no chart library.
- **Global search and notifications.** ⌘K finds pages and records, and a bell shows database notifications.
- **Theming.** Tailwind's palettes, light and dark themes, and text that stays readable whatever you choose.
- **Made to extend.** Plugins, render hooks, custom pages and translations.

Panels is built on [Xelqun Forms](/forms) and [Xelqun Tables](/tables), so everything there works here.

## Where to go next

- [Install the package](/panels/installation)
- [Build your first panel](/panels/your-first-panel)
- [Read about resources](/panels/resources/overview)
