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

## Polling

`$pollingInterval` reloads the page's widgets that often, in seconds, while the page is open.

```php
protected static ?int $pollingInterval = 60;
```

## Dashboard filters

`filters()` on a page puts fields above its widgets. Extend `Dashboard` to give the panel's home some.

```php
use Xelqun\Forms\Components\DatePicker;
use Xelqun\Forms\Components\Select;
use Xelqun\Panels\Pages\Dashboard;

class Home extends Dashboard
{
    public function filters(): array
    {
        return [
            Select::make('status')->options(['all' => 'All', 'active' => 'Live', 'draft' => 'Drafts'])->default('all'),
            DatePicker::make('from'),
        ];
    }
}
```

Each widget reads the values as `$this->pageFilters`:

```php
protected function getStats(): array
{
    $status = $this->pageFilters['status'] ?? 'all';

    return [
        Stat::make('Products', Product::query()->when($status !== 'all', fn ($query) => $query->where('status', $status))->count()),
    ];
}
```

The values stay in the URL, so a filtered dashboard can be shared. They're checked by the fields' rules, and a value that fails falls back to the field's default.

<!-- screenshot: dashboard-filters | A filter above the dashboard, and a stat that reads it -->

## Footer widgets

`getFooterWidgets()` puts widgets below a page's content. On a dashboard, that's below the others.

```php
protected function getFooterWidgets(): array
{
    return [LatestOrders::class];
}
```

[See a dashboard in the demo](demo:/): it has a filter, stats that reload every minute, and the latest orders at the foot.
