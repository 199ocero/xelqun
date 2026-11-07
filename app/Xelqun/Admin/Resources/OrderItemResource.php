<?php

namespace App\Xelqun\Admin\Resources;

use App\Models\OrderItem;
use App\Xelqun\Admin\Resources\OrderResource\ItemsRelationManager;
use Xelqun\Panels\Resources\Resource;

/**
 * Line items live under their order, as /orders/1/order-items/2/edit.
 */
class OrderItemResource extends Resource
{
    protected static string $model = OrderItem::class;

    protected static ?string $recordTitleAttribute = 'product';

    protected static ?string $parentResource = OrderResource::class;

    protected static ?string $parentRelationship = 'items';

    public static function form(): array
    {
        return ItemsRelationManager::form();
    }

    public static function columns(): array
    {
        return ItemsRelationManager::columns();
    }
}
