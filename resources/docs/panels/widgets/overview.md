---
title: Overview
description: Dashboards show widgets that arrive after the page, with skeletons in their place.
---

`Dashboard` shows the widgets from `Panel::widgets()`. It paints at once, and the widgets arrive after with skeletons in their place.

```bash
php artisan make:xelqun-widget OrderStats --stats
```

```php
$panel->pages([Dashboard::class])->widgets([OrderStats::class, OrdersChart::class]);
```

<!-- screenshot: dashboard | A dashboard of widgets -->

| Widget                                          | For                             |
| ----------------------------------------------- | ------------------------------- |
| [StatsOverview](/panels/widgets/stats)          | A row of numbers                |
| [ChartWidget](/panels/widgets/charts)           | Line, bar and doughnut charts   |
| [ListWidget, TileWidget](/panels/widgets/lists) | Rows, and a grid of small cards |
| [TableWidget](/panels/widgets/tables)           | A full table                    |
| [Widget](/panels/widgets/custom)                | Your own Vue component          |

## Size and order

Set `$columnSpan` (out of 12), `$sort` and `$heading`, and override `canView()` to hide one.

[See a dashboard in the demo](demo:/).
