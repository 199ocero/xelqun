---
title: Nested resources
description: Give related records pages of their own under their parent.
---

When related records need pages of their own, nest the resource under its parent, as in `/orders/1/order-items/5/edit`.

```php
class OrderItemResource extends Resource
{
    protected static string $model = OrderItem::class;

    protected static ?string $parentResource = OrderResource::class;

    protected static ?string $parentRelationship = 'items';
}
```

<!-- screenshot: nested-resource | Order items, nested under an order -->

`$parentRelationship` defaults to this model's plural.

It leaves the sidebar, the New menu and search, and only reaches the parent's related records. A parent out of reach is a 404 for everything under it, and new records join the parent whatever the browser sends.

To get there, point a relation manager's `$relatedResource` at it, and its rows and New button link to the nested pages.

[See it in the demo](demo:/orders/1/order-items).
