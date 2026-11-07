---
title: Create, edit, view and delete
description: The ready-made CRUD actions of a resource.
---

| Action                             | Policy   |
| ---------------------------------- | -------- |
| `ViewAction::for(static::class)`   | `view`   |
| `EditAction::for(static::class)`   | `update` |
| `DeleteAction::for(static::class)` | `delete` |

```php
use Xelqun\Actions\ActionGroup;
use Xelqun\Panels\Actions\DeleteAction;
use Xelqun\Panels\Actions\ViewAction;

public static function recordActions(): array
{
    return [
        ActionGroup::make([
            ViewAction::for(static::class)->button(),
            DeleteAction::for(static::class)->button(),
        ]),
    ];
}
```

<!-- screenshot: action-menu | View, edit and delete in a row menu -->

On a [simple resource](/panels/resources/simple), create and edit open in modals.
