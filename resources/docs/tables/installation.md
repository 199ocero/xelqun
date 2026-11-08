---
title: Installation
description: Install Xelqun Tables in a Laravel app with Inertia and Vue.
---

Tables is built on [Xelqun Forms](/forms/installation), so it needs the same stack: PHP 8.4, Laravel 13, Inertia 3, Vue 3.5 and Tailwind CSS 4. It also needs PHP's `intl` extension.

```bash
composer require xelqun/tables
php artisan xelqun-tables:install
```

The installer adds both npm packages, `@xelqun/tables` and `@xelqun/forms`, and runs `xelqun-forms:install` for you. So your entry file registers the forms plugin, and your Vite config gets the `optimizeDeps` setting [forms describes](/forms/installation). Your stylesheet imports both packages' styles. The forms one holds the theme, so badges and the filters panel need it too.

```css
@import 'tailwindcss';
@import '@xelqun/tables/style.css';
@import '@xelqun/forms/style.css';
```

Rebuild your frontend with `npm run build` or `npm run dev`, and you're ready for [your first table](/tables/your-first-table).

> [!NOTE]
> Installing Xelqun Panels? Its installer sets up forms and tables too.
