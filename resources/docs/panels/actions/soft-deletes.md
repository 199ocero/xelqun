---
title: Restore and force delete
description: Bring back a soft deleted record, or remove it for good.
---

```php
use Xelqun\Panels\Actions\ForceDeleteAction;
use Xelqun\Panels\Actions\RestoreAction;

public static function recordActions(): array
{
    return [
        RestoreAction::for(static::class),
        ForceDeleteAction::for(static::class),
    ];
}
```

<!-- screenshot: action-menu | Delete, restore and force delete in a row menu -->

They appear by themselves on a model with `SoftDeletes`, along with their bulk versions, and need `restore` and `forceDelete` in the policy. See [soft deletes](/panels/resources/soft-deletes).
