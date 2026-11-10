---
title: Sections and fieldsets
description: Section and Fieldset give a group of fields a heading, a description, an icon, actions and collapsing.
---

## Section

```php
use Xelqun\Forms\Layouts\Section;

Section::make('Shipping')
    ->description('Where the order goes.')
    ->columns(2)
    ->collapsible()
    ->schema([...]);
```

<!-- example: form-sections -->

`collapsed()` starts it closed, and makes it collapsible. Both take a closure too. `persistCollapsed()` remembers in the browser whether the viewer closed it.

```php
Section::make('Notes')
    ->collapsed(fn (string $operation): bool => $operation === 'edit')
    ->persistCollapsed()
    ->schema([...]);
```

## Icons, actions and footers

`icon()` puts an icon before the heading. `afterHeader()` puts actions or text at the end of the header, and `footer()` puts them at the bottom of the card.

```php
use Xelqun\Actions\Action;
use Xelqun\Forms\Layouts\Text;
use Xelqun\Forms\State\Get;
use Xelqun\Forms\State\Set;

Section::make('Billing')
    ->icon('credit-card')
    ->afterHeader(Text::make('Optional')->badge()->color('gray'))
    ->footer([
        Action::make('copy_shipping')
            ->authorize(true)
            ->link()
            ->label('Same as shipping')
            ->action(fn (Get $get, Set $set) => $set('billing_city', $get('shipping_city'))),
    ])
    ->schema([...]);
```

<!-- example: form-section-extras -->

## Layout

| Method      | What it does                                                                   |
| ----------- | ------------------------------------------------------------------------------ |
| `aside()`   | Puts the heading and description in a column beside the card, on wide screens. |
| `compact()` | Less padding, for a section among many.                                        |

```php
Section::make('Profile')
    ->description('Shown on your public page.')
    ->aside()
    ->schema([...]);
```

<!-- example: form-section-aside -->

Since 0.9, a section renders inside a wrapper element. CSS that expects the card as a direct child of the form may need changing.

## Fieldset

A `Fieldset` is lighter: a border and a legend.

```php
use Xelqun\Forms\Layouts\Fieldset;

Fieldset::make('Shipping address')->columns(2)->schema([...]);
```
