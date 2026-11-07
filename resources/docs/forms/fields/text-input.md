---
title: Text input
description: TextInput handles text, email, numbers, passwords, phone numbers and URLs, with masks, prefixes and copy buttons.
---

```php
use Xelqun\Forms\Components\TextInput;

TextInput::make('name')->required()->maxLength(255);
```

<!-- example: form-text-input -->

## Input types

Each of these sets the browser's input type and adds the matching validation rule.

```php
TextInput::make('email')->email();
TextInput::make('website')->url();
TextInput::make('phone')->tel();
TextInput::make('price')->numeric();
TextInput::make('quantity')->integer();
```

## Numbers

`minValue()`, `maxValue()` and `step()` set the range.

```php
TextInput::make('quantity')->integer()->minValue(1)->maxValue(100)->step(5);
```

## Text length

```php
TextInput::make('code')->minLength(3)->maxLength(12);

TextInput::make('pin')->length(6);
```

## Prefixes and icons

```php
TextInput::make('domain')->prefix('https://')->suffix('.com');

TextInput::make('email')->prefixIcon('mail');

TextInput::make('search')->suffixIcon('search');
```

Icons are lucide names. Forms ships a small set, and you can add more with the `icons` option of `createXelqunForms()`.

## Passwords

```php
TextInput::make('password')->password()->revealable();
```

A password starts empty, whatever the record holds, and leaving it empty keeps the current one. `revealable()` adds a button that shows the value as plain text.

## Masks

A mask formats what the user types: `9` is a digit, `a` is a letter, and `*` is either.

```php
TextInput::make('card')
    ->mask('9999 9999 9999 9999')
    ->stripCharacters(' ');
```

`stripCharacters()` removes characters before the value is validated and saved.

## Copy button

```php
TextInput::make('api_key')->readOnly()->copyable(copyMessage: 'Copied');
```

## Suggestions

`datalist()` shows suggestions from the browser, and people can still type anything.

```php
TextInput::make('browser')->datalist(['Chrome', 'Firefox', 'Safari']);
```

## Other options

| Method           | What it does                                                     |
| ---------------- | ---------------------------------------------------------------- |
| `autocomplete()` | Sets the `autocomplete` attribute.                               |
| `trim()`         | Removes spaces around the value before it's validated and saved. |
| `autofocus()`    | Focuses the field when the form opens.                           |
