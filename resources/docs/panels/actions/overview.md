---
title: Overview
description: Ready-made actions take their labels, modals and policies from the resource.
---

`recordActions()` starts with edit and delete. Build your own list from the same parts, which take labels, modals and policies from the resource.

```php
use Xelqun\Panels\Actions\DeleteAction;
use Xelqun\Panels\Actions\EditAction;
use Xelqun\Panels\Actions\ReplicateAction;
use Xelqun\Panels\Actions\ViewAction;

public static function recordActions(): array
{
    return [
        ViewAction::for(static::class),
        EditAction::for(static::class),
        ReplicateAction::for(static::class)->excludeAttributes(['slug']),
        DeleteAction::for(static::class),
    ];
}
```

<!-- screenshot: action-menu | The row menu of an order, with ready-made actions -->

Each takes the usual [action methods](/forms/actions/overview), such as `label()` or `visible()`. Mix them with your own actions, and group them with `ActionGroup`.

[See them in the demo](demo:/orders): open the menu on any row.
