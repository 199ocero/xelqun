---
title: Conditional logic
description: Show, hide, require and disable fields with rules that run in the browser and are checked again on the server.
---

`When` describes a condition that the browser can check by itself, with no server call.

```php
use Xelqun\Forms\Components\TextInput;
use Xelqun\Forms\Conditions\When;

TextInput::make('company')
    ->visibleWhen(When::eq('type', 'business'))
    ->requiredWhen(When::eq('type', 'business'));
```

<!-- example: conditional-fields -->

The server checks the same rules on submit, and drops the values of hidden fields.

## Where a condition goes

| Method           | Works on           |
| ---------------- | ------------------ |
| `visibleWhen()`  | Fields and layouts |
| `hiddenWhen()`   | Fields and layouts |
| `requiredWhen()` | Fields             |
| `disabledWhen()` | Fields             |

## Conditions

| Condition                              | True when                    |
| -------------------------------------- | ---------------------------- |
| `When::eq($path, $value)`              | The value matches.           |
| `When::neq($path, $value)`             | The value differs.           |
| `When::in($path, $values)`             | The value is in the list.    |
| `When::notIn($path, $values)`          | The value isn't in the list. |
| `When::gt()`, `gte()`, `lt()`, `lte()` | The number compares.         |
| `When::filled($path)`                  | There's a value.             |
| `When::blank($path)`                   | There's no value.            |

## Combining them

```php
TextInput::make('vat_number')->requiredWhen(
    When::and(When::eq('type', 'business'), When::in('region', ['eu'])),
);

TextInput::make('reason')->visibleWhen(
    When::or(When::eq('status', 'lost'), When::not(When::filled('owner'))),
);
```

## Text that follows a condition

`label()`, `hint()` and `placeholder()` take text chosen by a condition. Both strings are sent to the browser, so neither should hold anything people may not see.

## Rules that need the server

`When` only reads the form's values. For anything else, a closure runs on the server. See [live and dependent fields](/forms/live-fields).

```php
TextInput::make('discount')->hidden(fn (Get $get): bool => ! Customer::find($get('customer_id'))?->is_vip);
```

## By operation

`hiddenOn()`, `visibleOn()` and `disabledOn()` follow the operation, such as `create` or `edit`.

```php
TextInput::make('password')->visibleOn('create');
```
