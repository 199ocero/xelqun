---
title: Local development
description: Keep Xelqun working smoothly while you develop, with the right Vite setting and a restart after upgrades.
---

## Keep the packages out of Vite's pre-bundling

The installer adds this to your Vite config, and it needs to stay there.

```ts
optimizeDeps: {
    exclude: ['@xelqun/forms', '@xelqun/tables', '@xelqun/panels'],
},
```

Vite can't pre-bundle the packages' `.vue` files. If it bundled the rest, those files would read a separate copy of shared state, and you'd see icons go missing in dev mode. Production builds aren't affected.

If you installed the packages by hand, add it yourself.

## Restart the dev server after upgrading

Vite doesn't watch `node_modules`, so a running dev server keeps serving the old package code after an upgrade. A fix you've just installed can look like it didn't work. Restart `npm run dev` after every `npm update` of a Xelqun package.

## Upgrade Composer and npm together

Each package comes in two halves: the PHP one and the Vue one. Upgrade both, so they stay on the same version.

```bash
composer update "xelqun/*" --with-all-dependencies
npm update @xelqun/forms @xelqun/tables @xelqun/panels
```

## Finding a panel's URL

If a panel is on its own [domain](/panels/multiple-panels), add that host to your local setup. Laravel Herd serves subdomains of a linked site by itself.

## Server-side rendering

Xelqun's panels haven't been tested with Inertia's server-side rendering, and an admin area behind a login gains little from it. If the rest of your app uses it, you can turn it off for the panel's pages.

```php
use Inertia\Inertia;

Inertia::disableSsr(fn (): bool => request()->is('admin*'));
```
