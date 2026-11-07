---
title: Sections and fieldsets
description: Section and Fieldset give a group of fields a heading, a description and collapsing.
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

`collapsed()` starts it closed.

## Fieldset

A `Fieldset` is lighter: a border and a legend.

```php
use Xelqun\Forms\Layouts\Fieldset;

Fieldset::make('Shipping address')->columns(2)->schema([...]);
```
