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

## Result actions

`getGlobalSearchResultActions()` adds links under a result, such as one to edit the record. Each is a `NavigationItem` with a `url()`.

```php
use Illuminate\Database\Eloquent\Model;
use Xelqun\Panels\Navigation\NavigationItem;

public static function getGlobalSearchResultActions(Model $record): array
{
    return [NavigationItem::make('Edit')->url(static::getUrl('edit', ['record' => $record]))];
}
```

<!-- screenshot: global-search-actions | A search result with an Edit link under it -->

## Which results show

Search runs through `getEloquentQuery()`, skips a resource the user can't `viewAny`, and checks `view` on each record, showing up to 5 per resource. Results link to the view page. `$globalSearchResultsLimit` changes how many a resource gives.

```php
protected static int $globalSearchResultsLimit = 10;
```

> [!NOTE]
> The policy sees four times the limit, so one that refuses more than that, 20 matches by default, can hide later ones.

`$panel->globalSearch(false)` removes it. [Try it in the demo](demo:/products): press ⌘K and search for a customer, whose orders have an Edit link.
