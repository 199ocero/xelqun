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

An action does nothing without one, and the table's `authorize()` doesn't count.

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

`maxSelectableRecords()` sets the most rows one bulk action can take, and the server enforces it.

```php
protected function boot(): void
{
    $this->maxSelectableRecords(500);
}
```
