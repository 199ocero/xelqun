---
title: Before going to production
description: The checks to make before a form built with Xelqun Forms goes live.
---

## Add your rules to `authorize()`

A form allows everything by default. Add your own check, because it runs on every endpoint the form uses.

```php
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

public function authorize(?Authenticatable $user, ?Model $record): bool
{
    return $user?->can('update', $record) ?? false;
}
```

## Give each action its own `authorize()`

An action does nothing without one, and the form's `authorize()` doesn't count. See [actions](/forms/actions/overview).

## Schedule upload pruning

Temporary uploads stay on disk until this runs.

```php
use Illuminate\Support\Facades\Schedule;

Schedule::command('xelqun-forms:prune-uploads')->hourly();
```

## Check your upload rules

Limit what people can send, with `acceptedFileTypes()` and `maxSize()`. See [uploads and security](/forms/uploads-security).
