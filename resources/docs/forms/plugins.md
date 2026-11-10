---
title: Plugins
description: Add fields to Xelqun Forms from your own package, with the same search, uploads, rows and saving as the built-in ones.
---

A plugin adds fields from its own repository. Built-in fields use the same contracts, so a plugin field can do what they do.

## Installing a plugin

Require the Composer package first, then run the plugin command.

```bash
composer require acme/rating
php artisan xelqun-forms:plugin acme/rating
```

The command installs the npm package, adds the plugin to your Inertia entry file, and points Tailwind at its components.

## Laying one out

A plugin is one repository, with a `composer.json` and a `package.json`.

```json
{
    "name": "acme/rating",
    "autoload": { "psr-4": { "Acme\\Rating\\": "src/" } },
    "extra": {
        "laravel": { "providers": ["Acme\\Rating\\RatingServiceProvider"] },
        "xelqun": { "npm": "@acme/rating", "plugin": "ratingPlugin" }
    }
}
```

`package.json` ships your source, because the host's Vite compiles it. Declare the shared packages as **peer dependencies**, because a second copy of `vue` or `reka-ui` silently breaks reactivity and popovers.

```json
{
    "name": "@acme/rating",
    "type": "module",
    "files": ["resources/js"],
    "exports": { ".": "./resources/js/index.ts" },
    "peerDependencies": {
        "@xelqun/forms": "^0.8.0",
        "reka-ui": "^2.11.0",
        "vue": "^3.5.0"
    }
}
```

## Registering the renderer

```ts
import type { XelqunPlugin } from '@xelqun/forms';

export function ratingPlugin(): XelqunPlugin {
    return {
        id: 'acme/rating',
        components: {
            'acme/rating': () => import('./components/RatingField.vue'),
        },
    };
}
```

Plugin types need a vendor prefix, such as `acme/rating`. Names without one are reserved for built-in fields.

## What a field can do

Implement a contract from `Xelqun\Forms\Contracts`, and the matching endpoint starts working for your field.

| Contract             | What it gives you                                |
| -------------------- | ------------------------------------------------ |
| `HasSearchResults`   | The search endpoint                              |
| `AcceptsUploads`     | The upload endpoint and storage on save          |
| `NamesStoredFiles`   | Original file names, as `FileUpload` keeps them  |
| `EmbedsUploads`      | The same, for uploads kept inside a field's text |
| `ContainsRows`       | Rows with their own components                   |
| `PersistsItself`     | Saving somewhere other than a column             |
| `PersistsBeforeSave` | Doing something before the record is saved       |
| `NeedsServer`        | A clear error on forms the server can't rebuild  |

> [!WARNING]
> Search only suggests values. The browser can post anything, so `getTypeRules()` has to check the value too.

To start building, see [custom fields](/forms/fields/custom-fields). The full guide lives in the [package repository](https://github.com/199ocero/xelqun-forms/blob/main/docs/plugins.md).
