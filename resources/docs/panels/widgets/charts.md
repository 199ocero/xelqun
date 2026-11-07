---
title: Charts
description: Line, bar and doughnut charts, drawn with no chart library.
---

```php
use Xelqun\Panels\Widgets\ChartWidget;

class OrdersChart extends ChartWidget
{
    protected static ?string $heading = 'Orders';

    protected function getType(): string
    {
        return 'line'; // or 'bar' or 'doughnut'
    }

    protected function getData(): array
    {
        return [
            'labels' => ['Jan', 'Feb', 'Mar'],
            'datasets' => [['label' => 'Orders', 'data' => [12, 19, 8]]],
        ];
    }
}
```

<!-- screenshot: chart-widget | A line chart -->

> [!NOTE]
> There's no filter on a chart yet, and no radar, scatter or bubble type.

[See them in the demo](demo:/).
