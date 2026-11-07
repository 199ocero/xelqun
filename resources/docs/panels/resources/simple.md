---
title: Simple resources
description: Keep everything on the list page, with create and edit in modals.
---

For a model with a few fields, keep everything on the list page, with create and edit in modals.

```bash
php artisan make:xelqun-resource Tag --simple
```

```php
use Xelqun\Panels\Resources\Pages\ManageRecords;

public static function getPages(): array
{
    return ['index' => ManageRecords::class];
}
```

<!-- screenshot: simple-resource | Creating a tag in a modal, on the list page -->

[See it in the demo](demo:/tags).
