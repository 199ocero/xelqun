---
title: Overview
description: A resource is one class per model that gives you a table, a form, pages, filters and actions.
---

A resource describes how a model behaves in a panel.

```php
class ProductResource extends Resource
{
    protected static string $model = Product::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationIcon = 'package';

    protected static ?string $navigationGroup = 'Catalog';

    public static function form(): array { /* fields */ }

    public static function columns(): array { /* columns */ }
}
```

<!-- screenshot: resource-list | A resource list in a panel -->

`php artisan make:xelqun-resource Product` creates this and a policy. A panel made with `make:xelqun-panel` picks it up by itself, through `discoverResources()`. Otherwise, add it to `resources()`.

```php
$panel->discoverResources(in: app_path('Xelqun/Admin/Resources'), for: 'App\\Xelqun\\Admin\\Resources');
```

## What to override

| Method             | What it sets                                              |
| ------------------ | --------------------------------------------------------- |
| `form()`           | The fields of the create and edit pages.                  |
| `columns()`        | The table's columns.                                      |
| `filters()`        | The table's filters.                                      |
| `recordActions()`  | Buttons on each row.                                      |
| `bulkActions()`    | Buttons for selected rows.                                |
| `configureTable()` | Anything else on the table.                               |
| `infolist()`       | The [view page](/panels/infolists/overview).              |
| `getRelations()`   | [Relation managers](/panels/resources/relation-managers). |
| `getTabs()`        | [List tabs](/panels/resources/tabs).                      |

## Saving hooks

`mutateBeforeSave()`, `beforeCreate()`, `afterCreate()`, `beforeSave()` and `afterSave()` run around a save.

## Access

Access goes through the model's policy: `viewAny`, `view`, `create`, `update` and `delete`. With no policy, or no such method, the answer is no.

## The query

`getEloquentQuery()` is the security boundary. A record it leaves out is a 404 on every page, table action and form request.

```php
use Illuminate\Database\Eloquent\Builder;

public static function getEloquentQuery(): Builder
{
    return parent::getEloquentQuery()->with('category');
}
```

## Navigation

`$navigationIcon`, `$navigationGroup` and `$navigationSort` place it in the sidebar. `getNavigationBadge()` shows a count.

```php
public static function getNavigationBadge(): int
{
    return static::getEloquentQuery()->count();
}
```

The sidebar's New button lists every resource with a create page that the user may `create`.

[See one in the demo](demo:/products).
