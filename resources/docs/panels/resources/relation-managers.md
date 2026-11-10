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

With two or more, the tables sit on one tab bar under the record. Set `$relatedResource` to reuse a resource's form, columns and filters.

> [!NOTE]
> Upgrading from 0.4: the tables now share one tab bar above them, where before each carried its own.

It works for has-many, morph-many and many-to-many, where a new record is attached to the parent.

A relationship [repeater](/forms/fields/repeater) in the resource's form loads every child into the page. It stays quick to a few hundred rows; past that, each request loads them all, so use a relation manager: its table pages through them.

<!-- screenshot: relation-manager | A table of order items under the edit form of an order -->

## Badges

`getBadge()` puts a count or a short word on the tab, and `getBadgeColor()` colors it. Both get the record that owns the relationship.

```php
use Illuminate\Database\Eloquent\Model;

public static function getBadge(Model $owner): int
{
    return static::getRelation($owner)->count();
}

public static function getBadgeColor(Model $owner): string
{
    return 'info';
}
```

<!-- screenshot: relation-manager-badge | A relation manager tab with a count badge -->

## Groups and layout

`RelationGroup` puts several managers under one tab, one table after another. It sits in `getRelations()` beside managers that keep a tab of their own.

```php
use Xelqun\Panels\Resources\RelationGroup;

public static function getRelations(): array
{
    return [
        OrdersRelationManager::class,
        RelationGroup::make('Contacts', [AddressesRelationManager::class, PhonesRelationManager::class]),
    ];
}
```

`getRelationManagersLayout()` on the resource returning `stacked` shows every table at once, one after another, in place of tabs.

```php
public static function getRelationManagersLayout(): string
{
    return 'stacked';
}
```

## Lazy loading

`$isLazy` loads a manager's rows after the page renders, so a slow relationship doesn't hold the record back.

```php
protected static bool $isLazy = true;
```

## A page of its own

`ManageRelatedRecords` puts one manager on a page of its own, at `{record}/key`, instead of under the form. Name the manager, and add the page to `getPages()`:

```php
use Xelqun\Panels\Resources\Pages\ManageRelatedRecords;

class ManageCustomerOrders extends ManageRelatedRecords
{
    protected static string $relationManager = OrdersRelationManager::class;
}
```

```php
public static function getPages(): array
{
    return [...parent::getPages(), 'orders' => ManageCustomerOrders::class];
}
```

`CustomerResource::getUrl('orders', ['record' => $customer])` links to it, and it needs the customer's `view`. Set `$routePath` on the page to move it. Link to it from the record with [tabs across a record](/panels/resources/pages#tabs-across-a-record).

## Access

Access goes through the related model's policy and the parent's `view`. Changing anything also takes the parent's `update`, and the view page is read-only.

`isReadOnly()` overrides the page: `true` keeps a table read-only on the edit page too, and `false` lets it change things on the view page. `null`, the default, follows the page.

```php
public static function isReadOnly(): ?bool
{
    return true;
}
```

## Linking existing records

See [attach and associate](/panels/actions/relations). [See it in the demo](demo:/orders/1/edit), below the order's form, where the line items' tab shows their count.
