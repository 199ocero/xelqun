---
title: Reordering
description: Let rows move up and down by a number column.
---

```php
protected function boot(): void
{
    // isAdmin() stands for a method on your own User model.
    $this->reorderable('sort', fn (): bool => auth()->user()?->isAdmin() ?? false);
}
```

<!-- example: table-reordering -->

This adds up and down buttons. It's off until the closure says yes, and both rows come through `query()`.

A move swaps the row's number with its neighbour's, so the column must hold a distinct value for each row. Rows that share a number don't move. Number new rows as you create them:

```php
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected static function booted(): void
    {
        static::creating(function (Product $product): void {
            $product->sort ??= static::max('sort') + 1;
        });
    }
}
```

> [!NOTE]
> Rows move with buttons. Drag and drop isn't supported yet.
