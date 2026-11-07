---
title: Attach and associate
description: Link records that already exist from a relation manager.
---

To link records that already exist, add `AttachAction` and `DetachAction` for a many-to-many, or `AssociateAction` and `DissociateAction` for a has-many.

```php
use Illuminate\Database\Eloquent\Model;
use Xelqun\Panels\Actions\AttachAction;
use Xelqun\Panels\Actions\DetachAction;

public static function headerActions(Model $owner): array
{
    return [...parent::headerActions($owner), AttachAction::for(static::class, $owner)];
}

public static function recordActions(Model $owner): array
{
    return [...parent::recordActions($owner), DetachAction::for(static::class, $owner)];
}
```

<!-- screenshot: attach-action | The associate modal on a relation manager -->

In the demo, [associate](demo:/authors/1/edit) is below an author's form, and [attach](demo:/orders/1/edit) is on the Tags tab below an order's form.
