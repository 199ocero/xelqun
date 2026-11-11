---
title: Tables
description: TableWidget shows a full table on a dashboard.
---

```php
use Xelqun\Panels\Widgets\TableWidget;
use Xelqun\Tables\Table;

class LatestOrders extends TableWidget
{
    protected static ?string $heading = 'Latest orders';

    protected function getTable(): Table
    {
        return LatestOrdersTable::make();
    }
}
```

<!-- screenshot: table-widget | A table on a dashboard -->

The table is a normal [Xelqun table](/tables/your-first-table). Its rows load through the table's own `authorize()`, not the widget's `canView()`, so make the two agree. [See it in the demo](demo:/).
