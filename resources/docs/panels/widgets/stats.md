---
title: Stats
description: StatsOverview shows a row of numbers, with icons, descriptions and small charts.
---

```php
use Xelqun\Panels\Widgets\Stat;
use Xelqun\Panels\Widgets\StatsOverview;

class OrderStats extends StatsOverview
{
    protected function getStats(): array
    {
        return [
            Stat::make('Orders', Order::count())
                ->description('All time')
                ->icon('shopping-cart')
                ->chart([4, 7, 3, 9, 12]),
        ];
    }
}
```

<!-- screenshot: stats-widget | A row of stats -->

`chart()` draws a small line behind the number, oldest value first.

[See it in the demo](demo:/).
