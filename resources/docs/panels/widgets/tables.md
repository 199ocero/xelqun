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

The table is a normal [Xelqun table](/tables/your-first-table). [See it in the demo](demo:/).
