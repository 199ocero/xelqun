---
title: Global search
description: ⌘K finds pages and records, through each resource's query and policy.
---

⌘K finds pages and records. A resource with `$recordTitleAttribute` is searched on that column. If the title is an accessor, or you want more columns, override this.

```php
use App\Models\Product;
use Illuminate\Database\Eloquent\Model;

public static function getGloballySearchableAttributes(): array
{
    return ['name', 'sku', 'category.name'];
}

/**
 * @param  Product  $record
 */
public static function getGlobalSearchResultDetails(Model $record): array
{
    return ['SKU' => $record->sku];
}
```

<!-- screenshot: global-search | The search palette, showing matching pages and records -->

Return `[]` to keep a resource out.

Search runs through `getEloquentQuery()`, skips a resource the user can't `viewAny`, and checks `view` on each record, showing up to 5 per resource. Results link to the view page.

> [!NOTE]
> A policy that refuses more than the first 20 matches can hide later ones.

`$panel->globalSearch(false)` removes it. [Try it in the demo](demo:/products): press ⌘K.
