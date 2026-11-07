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

Leave a key out to remove that page.

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

## Tabs across a record

`getRecordSubNavigation()` on a resource adds tabs across a record's pages.

[See them in the demo](demo:/orders).
