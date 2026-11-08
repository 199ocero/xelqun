---
title: Replicate
description: Copy a record, leaving out what shouldn't be copied.
---

```php
use App\Models\Post;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Xelqun\Panels\Actions\ReplicateAction;

ReplicateAction::for(static::class)
    ->excludeAttributes(['slug'])
    ->beforeReplicaSaved(function (Model $replica): void {
        assert($replica instanceof Post);

        $replica->title .= ' (copy)';
        $replica->slug = Str::slug($replica->title).'-'.Str::lower(Str::random(6));
    });
```

<!-- screenshot: action-menu | Replicate in a row menu -->

`excludeAttributes()` lists what must not be copied, such as a unique slug. An excluded column is empty on the copy, so set it again in `beforeReplicaSaved()`, unless it's nullable or your model fills it. The closure gets the copy, then the original, typed as `Model`, so check the class before you use its columns.

The button reads "Duplicate", and it needs `replicate` in the policy.

[See it in the demo](demo:/orders): open the menu on any row.
