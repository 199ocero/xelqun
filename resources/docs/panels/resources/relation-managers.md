---
title: Relation managers
description: A table of a relationship under a record's pages, with create, edit and delete in modals.
---

```bash
php artisan make:xelqun-relation-manager Customer orders
```

```php
use Xelqun\Forms\Components\TextInput;
use Xelqun\Panels\Resources\RelationManager;
use Xelqun\Tables\Columns\TextColumn;

class OrdersRelationManager extends RelationManager
{
    protected static string $relationship = 'orders';

    public static function form(): array
    {
        return [TextInput::make('number')->required()];
    }

    public static function columns(): array
    {
        return [TextColumn::make('number')->searchable()];
    }
}
```

Return it from `getRelations()` on the resource.

```php
public static function getRelations(): array
{
    return [OrdersRelationManager::class];
}
```

With two or more, the tables sit on tabs under the record. Set `$relatedResource` to reuse a resource's form, columns and filters.

It works for has-many, morph-many and many-to-many, where a new record is attached to the parent.

A relationship [repeater](/forms/fields/repeater) in the resource's form loads every child into the page. It stays quick to a few hundred rows; past that, each request loads them all, so use a relation manager: its table pages through them.

<!-- screenshot: relation-manager | A table of order items under the edit form of an order -->

## Access

Access goes through the related model's policy and the parent's `view`. Changing anything also takes the parent's `update`, and the view page is read-only.

## Linking existing records

See [attach and associate](/panels/actions/relations). [See it in the demo](demo:/orders/1/edit), below the order's form.
