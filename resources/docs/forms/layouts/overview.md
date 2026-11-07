---
title: Overview
description: Layouts arrange fields into grids, sections, tabs and wizards, without adding to the form's data.
---

A layout holds other components. It decides how they sit on the page, and it doesn't change the form's state.

```php
use Xelqun\Forms\Components\TextInput;
use Xelqun\Forms\Layouts\Section;

Section::make('Contact')
    ->columns(2)
    ->schema([
        TextInput::make('name'),
        TextInput::make('email'),
    ]);
```

<!-- example: form-layouts -->

| Layout                                                    | Use it for                   |
| --------------------------------------------------------- | ---------------------------- |
| [Grid, Flex, Group, FusedGroup](/forms/layouts/grid)      | Columns and rows             |
| [Section, Fieldset](/forms/layouts/sections)              | Headed groups of fields      |
| [Tabs](/forms/layouts/tabs)                               | Fields on separate tabs      |
| [Wizard](/forms/layouts/wizard)                           | Steps, checked one at a time |
| [Callout, EmptyState](/forms/layouts/callouts)            | Messages in a form           |
| [Text, Icon, Image, UnorderedList](/forms/layouts/primes) | Plain content                |
| [Actions](/forms/actions/overview)                        | Buttons in a form            |

## Columns

`columns()` takes a number, or an array by breakpoint. A number means one column on small screens.

```php
Section::make('Address')->columns(['md' => 2, 'xl' => 4])->schema([...]);
```

## Conditions

Layouts take the same visibility rules as fields, so a whole section can follow a condition.

```php
use Xelqun\Forms\Conditions\When;
use Xelqun\Forms\Layouts\Fieldset;

Fieldset::make('Shipping address')
    ->visibleWhen(When::eq('ship_elsewhere', true))
    ->schema([...]);
```

A hidden layout's fields are left out of validation and of the saved data.
