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

`make:xelqun-resource` and `make:xelqun-relation-manager` start the form and table from a column the model's table has: `name`, `title`, `label`, `number`, `email`, `slug`, or its first text column. A generated resource sets `$recordTitleAttribute` to it, so it shows in global search.
