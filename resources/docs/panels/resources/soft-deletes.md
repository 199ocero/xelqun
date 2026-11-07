---
title: Soft deletes
description: A model with SoftDeletes gets a filter, restore and force delete by itself.
---

A model with `SoftDeletes` gets this by itself: deleted records leave the list, a filter brings them back, and `RestoreAction`, `ForceDeleteAction` and their bulk versions appear.

They need `restore` and `forceDelete` in the policy.

```php
public function restore(User $user, Post $post): bool
{
    return $user->is_admin;
}

public function forceDelete(User $user, Post $post): bool
{
    return $user->is_admin;
}
```

If you override `filters()`, add the filter yourself.

```php
use Xelqun\Panels\Tables\TrashedFilter;

public static function filters(): array
{
    return [TrashedFilter::make('trashed')];
}
```

<!-- screenshot: soft-deletes | The filters panel, with a filter for deleted records -->

[See it in the demo](demo:/posts): delete a post, then open Filters to bring it back.
