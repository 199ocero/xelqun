---
title: Custom pages
description: Replace a resource's page with your own class.
---

To change a page, extend one of `ListRecords`, `CreateRecord`, `EditRecord`, `ViewRecord` or `ResourcePage`, and return it from `getPages()`.

```php
use Xelqun\Panels\Resources\Pages\ListRecords;

class ListProducts extends ListRecords
{
    public function getSubheading(): ?string
    {
        return 'Everything in the catalog.';
    }
}
```

```php
public static function getPages(): array
{
    return [...parent::getPages(), 'index' => ListProducts::class];
}
```

## Pages of a resource's own

Any other key in `getPages()` gets a route of its own, at the key or at the page's `$routePath`. Extend `ResourcePage`, and put `{record}` in the path for a page about one record, which the page then reads from `$this->record`.

```php
use Xelqun\Panels\Resources\Pages\ResourcePage;

class OrderActivity extends ResourcePage
{
    protected static ?string $routePath = '{record}/activity';

    public function content(): array
    {
        return [OrderActivityTable::make()];
    }
}
```

```php
public static function getPages(): array
{
    return [...parent::getPages(), 'activity' => OrderActivity::class];
}
```

`getUrl('activity', ['record' => $order])` links to it.

A page of your own asks the resource's policy for `view` on its record, or `viewAny` when it has none. Override `isAccessible()` to change that.

> [!NOTE]
> Upgrading from 0.4: before 0.5, an extra key in `getPages()` was quietly left without a route. Now every one gets a route, so a page you'd put there becomes reachable. Give it an `isAccessible()`.

`ManageRelatedRecords` is one ready-made: it puts a [relation manager](/panels/resources/relation-managers#a-page-of-its-own) on a page of its own.

For a page outside a resource, see [pages](/panels/pages).
