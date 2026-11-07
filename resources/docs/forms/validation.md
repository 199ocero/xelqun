---
title: Validation
description: Forms use Laravel's validation on the server, and check the simple rules in the browser as well.
---

Rules on a field are Laravel rules. They run on the server every time, and errors land on the right field, even inside tabs and repeater rows.

```php
TextInput::make('email')->email()->required()->unique(ignoreRecord: true);
```

<!-- example: validation-errors -->

## Dedicated rules

Most rules have a method. Here are the common ones.

| Kind       | Methods                                                                                                                                                                                                                 |
| ---------- | ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| Presence   | `required()`, `nullable()`, `filled()`, `requiredIf()`, `requiredUnless()`, `requiredWith()`, `requiredWithAll()`, `requiredWithout()`, `requiredWithoutAll()`, `requiredIfAccepted()`                                  |
| Prohibited | `prohibited()`, `prohibitedIf()`, `prohibitedUnless()`, `prohibits()`                                                                                                                                                   |
| Format     | `string()`, `alpha()`, `alphaDash()`, `alphaNum()`, `ascii()`, `json()`, `uuid()`, `ulid()`, `ip()`, `ipv4()`, `ipv6()`, `macAddress()`, `hexColor()`, `activeUrl()`, `regex()`, `notRegex()`, `multipleOf()`, `enum()` |
| Text       | `startsWith()`, `endsWith()`, `doesntStartWith()`, `doesntEndWith()`                                                                                                                                                    |
| Comparing  | `same()`, `different()`, `confirmed()`, `gt()`, `gte()`, `lt()`, `lte()`, `in()`, `notIn()`                                                                                                                             |
| Dates      | `after()`, `afterOrEqual()`, `before()`, `beforeOrEqual()`                                                                                                                                                              |
| Database   | `exists()`, `unique()`, `scopedExists()`, `scopedUnique()`                                                                                                                                                              |

## Any other rule

`rules()` adds Laravel rules, and `rule()` adds one, with an optional condition.

```php
TextInput::make('code')->rules(['digits:6']);

TextInput::make('vat')->rule('required', fn (Get $get): bool => $get('type') === 'business');
```

A closure rule works too.

```php
use Closure;

TextInput::make('slug')->rules([
    fn () => function (string $attribute, mixed $value, Closure $fail): void {
        if ($value === 'admin') {
            $fail('That name is taken.');
        }
    },
]);
```

## Database rules

`unique()` and `exists()` take the record into account.

```php
TextInput::make('email')->unique(ignoreRecord: true);

Select::make('owner_id')->exists('users', 'id');
```

Global scopes don't apply to them. `scopedUnique()` and `scopedExists()` go through the model's query, so its scopes do.

## Confirming a value

`confirmed()` needs a field named `{name}_confirmation` next to it.

```php
TextInput::make('password')->password()->confirmed();
TextInput::make('password_confirmation')->password()->saved(false);
```

## Messages

`validationMessages()` sets errors by rule name, and `validationAttribute()` sets what they call the field.

```php
TextInput::make('recipient')
    ->required()
    ->validationAttribute('recipient name')
    ->validationMessages(['required' => 'Who is this for?']);
```

## Checking in the browser

`frontendValidation()` on a form checks the simple rules before calling the server: required, length, ranges and formats.

```php
OrderForm::make()->frontendValidation()->toSchema();
```

`liveValidation()` on a field checks its rules on the server as soon as it changes, which suits something like an email that must be unique.

```php
TextInput::make('email')->unique()->liveValidation();
```
