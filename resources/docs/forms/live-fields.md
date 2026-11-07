---
title: Live and dependent fields
description: Server closures load options and set values, and the browser only calls the server when a value they read changes.
---

Options and values often come from the database. A closure runs on the server, and `Get` reads other fields.

```php
use Xelqun\Forms\Components\Select;
use Xelqun\Forms\State\Get;
use Xelqun\Forms\State\Set;

Select::make('country_id')
    ->options(Country::pluck('name', 'id'))
    ->afterStateUpdated(function (Get $get, Set $set): void {
        $set('state_id', null);
    });

Select::make('state_id')
    ->options(fn (Get $get): array => State::where('country_id', $get('country_id'))->pluck('name', 'id')->all())
    ->disabled(fn (Get $get): bool => blank($get('country_id')));
```

The package notices which values each closure reads. When `country_id` changes, the browser sends one small request and gets back a patch for what changed. Focus and cursor position stay put.

<!-- example: dependent-fields -->

## Sending changes to the server

`live()` sends a field's changes. Most of the time you don't need it, because a closure that reads a field already makes that field live, and so does `afterStateUpdated()`.

```php
TextInput::make('title')->live(onBlur: true);

TextInput::make('search')->live(debounce: 500);
```

`dependsOn()` declares what the tracking can't see, such as a read behind a branch.

```php
TextInput::make('total')->dependsOn(['qty', 'price']);
```

## Reacting to a change

`afterStateUpdated()` runs after someone changes the field. `$state`, `$get`, `$set` and `$record` can all be injected.

```php
TextInput::make('title')
    ->live(onBlur: true)
    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state)));
```

`state()` sets the field's own value from a closure.

## Changing a value going in and out

| Method                  | What it does                                                      |
| ----------------------- | ----------------------------------------------------------------- |
| `formatStateUsing()`    | Changes the value the form shows.                                 |
| `afterStateHydrated()`  | Runs after the form is filled, with every field's state in place. |
| `dehydrateStateUsing()` | Changes the value before it's saved.                              |

## Calculating in the browser

`computed()` calculates a value with no server call, and the server works it out again on submit.

```php
TextInput::make('subtotal')->computed('qty * price')->disabled();
TextInput::make('total')->computed('sum(items.*.subtotal)')->disabled();
```

Expressions take `+`, `-`, `*`, `/` and parentheses, along with `sum()`, `avg()`, `min()`, `max()`, `count()` and `round()`.

<!-- example: repeater-totals -->
