---
title: Singular resources
description: Edit one record with no list, for settings and the like.
---

For settings and the like, return only `ManageSingleRecord` from `getPages()`.

```php
use Xelqun\Panels\Resources\Pages\ManageSingleRecord;

public static function getPages(): array
{
    return ['index' => ManageSingleRecord::class];
}
```

<!-- screenshot: singular-resource | A settings page editing one record, with no list -->

It edits what `getSingularRecord()` returns, which by default is the first record, or a new one until it's saved.

[See it in the demo](demo:/store-settings).
