---
title: Morph-to select
description: MorphToSelect edits a polymorphic relationship with a type and a record, limited to the types you list.
---

```php
use App\Models\Post;
use App\Models\Product;
use Xelqun\Forms\Components\MorphToSelect;
use Xelqun\Forms\Components\MorphToSelect\Type;

MorphToSelect::make('commentable')
    ->types([
        Type::make(Product::class)->titleAttribute('name'),
        Type::make(Post::class)->titleAttribute('title'),
    ])
    ->searchable();
```

<!-- example: form-morph-to-select -->

Only these records can be chosen, and a posted type outside the list is refused. `searchable()` searches on the server. Without it, picking a type sends every record of that type to the page, so use `searchable()` for any type with more than a few hundred records.

## Limiting the records

```php
use Illuminate\Database\Eloquent\Builder;

Type::make(Post::class)
    ->titleAttribute('title')
    ->modifyOptionsQueryUsing(fn (Builder $query) => $query->whereNotNull('published_at'));
```

This is also what validation checks, so a record outside it can't be picked by hand.
