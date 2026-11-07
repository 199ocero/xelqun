---
title: Replicate
description: Copy a record, leaving out what shouldn't be copied.
---

```php
use Xelqun\Panels\Actions\ReplicateAction;

ReplicateAction::for(static::class)
    ->excludeAttributes(['slug'])
    ->beforeReplicaSaved(fn (Post $replica) => $replica->title .= ' (copy)');
```

<!-- screenshot: action-menu | Replicate in a row menu -->

`excludeAttributes()` lists what must not be copied, such as a unique slug. It needs `replicate` in the policy.

[See it in the demo](demo:/orders): open the menu on any row.
