---
title: Installation
description: Install Xelqun Forms in a Laravel app with Inertia and Vue, and set up its frontend in one command.
---

## Requirements

- PHP 8.4 or newer
- Laravel 13
- Inertia 3 with Vue 3.5
- Tailwind CSS 4

## Install the package

```bash
composer require xelqun/forms
php artisan xelqun-forms:install
```

The installer adds the matching `@xelqun/forms` npm package, then sets up `resources/js/app.ts` and `resources/css/app.css` for you.

If you'd rather install the npm package yourself, pass `--without-node`.

## What the installer changes

Your Inertia entry file registers the plugin:

```ts
import { createInertiaApp } from '@inertiajs/vue3';
import { createXelqunForms } from '@xelqun/forms';

void createInertiaApp({
    withApp: (app) => {
        app.use(createXelqunForms());
    },
});
```

Your stylesheet imports the package's styles:

```css
@import 'tailwindcss';
@import '@xelqun/forms/style.css';
```

It also adds this to your Vite config. Vite can't pre-bundle the packages' `.vue` files, so without it, they'd read a separate copy of shared state, such as the icons, in dev mode.

```ts
optimizeDeps: {
    exclude: ['@xelqun/forms', '@xelqun/tables', '@xelqun/panels'],
},
```

Rebuild your frontend with `npm run build` or `npm run dev`, and you're ready for [your first form](/forms/your-first-form).

> [!NOTE]
> Installing Xelqun Panels? Its installer sets up forms and tables too, so you can skip this page.
