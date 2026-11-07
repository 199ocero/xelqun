---
title: Plugins
description: Add columns from your own package, with the same sorting, search and links as built-in ones.
---

A tables plugin is a [forms plugin](/forms/plugins), so install one the same way.

```bash
php artisan xelqun-forms:plugin acme/sparkline
```

To build one, start from a [custom column](/tables/columns/custom). Column types need a vendor prefix, such as `acme/sparkline`.
