---
title: Before going to production
description: The checks to make before a table built with Xelqun Tables goes live.
---

## Add your rules to `authorize()`

A table allows everything by default.

```php
use Illuminate\Contracts\Auth\Authenticatable;

public function authorize(?Authenticatable $user): bool
{
    return $user?->can('viewAny', Product::class) ?? false;
}
```

## Give each action its own `authorize()`

An action does nothing without one, and the table's `authorize()` doesn't count. A string such as `'update'` is checked against the row's [policy](/tables/your-first-table#things-to-know).

## Scope `query()` to what the viewer may see

Every row and every action stays inside `query()`, so it's your main line of defence.

```php
use Illuminate\Database\Eloquent\Builder;

public function query(): Builder
{
    return Order::query()->whereBelongsTo(auth()->user()->team);
}
```

## Limit bulk actions

`maxSelectableRecords()` sets the most rows one bulk action can take, and the server enforces it. The default is 1,000. With a higher limit, take [`$query`](/tables/actions/bulk#many-rows-at-once) in the action rather than `$records`, which loads every selected row as a model in one request.

```php
protected function boot(): void
{
    $this->maxSelectableRecords(500);
}
```

## Check the rate limits

Each endpoint has its own rate limit per person, so a burst of searches or polls doesn't block actions. Change them under `routes.throttle` in `config/xelqun-tables.php`. Publish the file first:

```bash
php artisan vendor:publish --tag=xelqun-tables-config
```

```php
'routes' => [
    // ...
    'throttle' => [
        'query' => '240,1',
        'update' => '120,1',
        'action' => '60,1',
    ],
],
```

`query` covers loading, searching, sorting, filtering and polling. `update` covers editable cells and reordering, and `action` covers every action.
