<?php

namespace App\Xelqun\Admin\Widgets;

use App\Xelqun\Admin\Resources\OrderResource;
use Xelqun\Panels\Widgets\TableWidget;
use Xelqun\Tables\Table;

class LatestOrders extends TableWidget
{
    protected static int $sort = 6;

    protected static int $columnSpan = 12;

    protected static ?string $heading = 'Latest orders';

    protected function getTable(): Table
    {
        return LatestOrdersTable::make();
    }

    protected function getSeeAllUrl(): string
    {
        return OrderResource::getUrl();
    }
}
