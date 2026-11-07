---
title: Grid, flex and groups
description: Grid, Flex, Group and FusedGroup arrange components into columns and rows.
---

## Grid

```php
use Xelqun\Forms\Layouts\Grid;

Grid::make(3)->schema([
    TextInput::make('first_name'),
    TextInput::make('middle_name'),
    TextInput::make('last_name'),
]);
```

<!-- example: form-grid -->

## Flex

`Flex` puts components in a row. Each takes the free space, unless you pass `grow(false)`.

```php
use Xelqun\Forms\Layouts\Flex;

Flex::make([
    TextInput::make('title'),
    Toggle::make('is_published')->grow(false),
])->from('md');
```

`from()` stacks the children below that breakpoint: `sm`, `md`, `lg`, `xl` or `2xl`.

## Group

`Group` holds components with no visual of its own. Use it to give several fields one rule, or one column span.

```php
use Xelqun\Forms\Layouts\Group;

Group::make([
    TextInput::make('company'),
    TextInput::make('vat_number'),
])->columnSpan(2);
```

### Editing a related record

With `relationship()`, the fields of a group edit one `HasOne` or `BelongsTo` record.

```php
Group::make([
    TextInput::make('recipient_name'),
    TextInput::make('recipient_phone'),
])->relationship('giftRecipient');
```

## FusedGroup

`FusedGroup` joins fields into one control.

```php
use Xelqun\Forms\Layouts\FusedGroup;

FusedGroup::make([
    TextInput::make('amount')->numeric(),
    Select::make('currency')->options(['usd' => 'USD', 'eur' => 'EUR']),
])->columns(2);
```
