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

For a page outside a resource, see [pages](/panels/pages).
