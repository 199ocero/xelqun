---
title: Commands
description: The Artisan commands of Xelqun Panels.
---

| Command                        | What it does                                                      |
| ------------------------------ | ----------------------------------------------------------------- |
| `xelqun-panels:install`        | Sets up forms, tables and panels.                                 |
| `make:xelqun-panel`            | Creates a panel and registers its provider.                       |
| `make:xelqun-resource`         | Creates a resource and a policy. `--simple` keeps it on one page. |
| `make:xelqun-relation-manager` | Creates a relation manager.                                       |
| `make:xelqun-page`             | Creates a page.                                                   |
| `make:xelqun-widget`           | Creates a chart widget. `--stats` makes a stats overview.         |

Resources, relation managers, pages and widgets go in the `Admin` panel. `--panel=` picks another one:

```bash
php artisan make:xelqun-resource Project --panel=Workspace
```

That writes `app/Xelqun/Workspace/Resources/ProjectResource.php`. A panel made by `make:xelqun-panel` discovers its resources. Add pages and widgets to its provider yourself, as the command reminds you.

`make:xelqun-resource` makes a policy only when the model has none, and that policy refuses everything until you fill it in. The `make:` commands stop if their file exists. `--force` overwrites it. `xelqun-panels:install` takes `--without-node` to skip the npm install.

`make:xelqun-resource` and `make:xelqun-relation-manager` start the form and table from a column the model's table has: `name`, `title`, `label`, `number`, `email`, `slug`, or its first text column. A generated resource sets `$recordTitleAttribute` to it, so it shows in global search.
