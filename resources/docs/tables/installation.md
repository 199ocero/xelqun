---
title: Installation
description: Install Xelqun Tables in a Laravel app with Inertia and Vue.
---

Tables is built on [Xelqun Forms](/forms/installation), so it needs the same stack: PHP 8.4, Laravel 13, Inertia 3, Vue 3.5 and Tailwind CSS 4. It also needs PHP's `intl` extension.

```bash
composer require xelqun/tables
php artisan xelqun-tables:install
```

The installer adds the npm packages and sets up your Inertia entry file and stylesheet. Forms comes with it.

Rebuild your frontend with `npm run build` or `npm run dev`, and you're ready for [your first table](/tables/your-first-table).

> [!NOTE]
> Installing Xelqun Panels? Its installer sets up forms and tables too.
