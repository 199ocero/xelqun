---
title: List, create, edit and view
description: A resource comes with its pages, with no page files to generate.
---

A resource gets list, create, edit and view pages from one class, with no page files.

To change one, extend `ListRecords`, `CreateRecord`, `EditRecord` or `ViewRecord`, and return it from `getPages()`.

```php
use Xelqun\Panels\Resources\Pages\CreateRecord;
use Xelqun\Panels\Resources\Pages\EditRecord;
use Xelqun\Panels\Resources\Pages\ListRecords;
use Xelqun\Panels\Resources\Pages\ViewRecord;

public static function getPages(): array
{
    return [
        'index' => ListRecords::class,
        'create' => CreateRecord::class,
        'edit' => EditRecord::class,
        'view' => ViewRecord::class,
    ];
}
```

<!-- screenshot: resource-form | The create page of a resource -->

Leave a key out to remove that page. Add one of your own for a [page of the resource's own](/panels/resources/custom-pages#pages-of-a-resources-own).

## Create & create another

The create page has a "Create & create another" button, which saves and opens a fresh form. Turn it off on your own `CreateRecord`:

```php
use Xelqun\Panels\Resources\Pages\CreateRecord;

class CreateOrder extends CreateRecord
{
    protected static bool $canCreateAnother = false;
}
```

## Header actions

`getHeaderActions()` adds buttons above the list.

```php
use Xelqun\Panels\Actions\ExportAction;
use Xelqun\Panels\Actions\ImportAction;

public static function getHeaderActions(): array
{
    return [ExportAction::for(static::class), ImportAction::for(static::class)];
}
```

## Links

`getUrl()` builds a link to a page.

```php
ProductResource::getUrl();
ProductResource::getUrl('view', ['record' => $product]);
```

It works outside a panel's request too, as in a job, a mail or a test, when one panel registers the resource. When several do, pass the panel as the third argument:

```php
use Xelqun\Panels\Panels;

ProductResource::getUrl('view', ['record' => $product], app(Panels::class)->get('admin'));
```

## Tabs across a record

`getRecordSubNavigation()` on a resource adds tabs across a record's pages. It returns `NavigationItem`s. A tab is only highlighted when you say so with `active()`, and a link to a nested resource needs the `parentRecord`.

```php
use Illuminate\Database\Eloquent\Model;
use Xelqun\Panels\Navigation\NavigationItem;

public static function getRecordSubNavigation(Model $record): array
{
    $view = static::getUrl('view', ['record' => $record]);
    $edit = static::getUrl('edit', ['record' => $record]);

    return [
        NavigationItem::make('Details')->url($view)->active(request()->url() === $view),
        NavigationItem::make('Edit')->url($edit)->active(request()->url() === $edit),
        NavigationItem::make('Items')->url(OrderItemResource::getUrl('index', ['parentRecord' => $record])),
    ];
}
```

The tabs show on the record's own pages, not on the nested list they link to.

[See them in the demo](demo:/orders).
