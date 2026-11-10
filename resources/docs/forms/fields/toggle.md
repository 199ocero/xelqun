---
title: Toggle
description: Toggle is an on and off switch that holds a true or false value.
---

```php
use Xelqun\Forms\Components\Toggle;

Toggle::make('is_featured')->default(true);
```

<!-- example: form-toggle -->

It works as a [checkbox](/forms/fields/checkbox) does, and takes the same `description()`.

```php
Toggle::make('is_visible')
    ->label('Visible to customers')
    ->description('Hidden products stay in reports.');
```

A model needs a `boolean` cast for this field.

## Icons and colors

`onIcon()` and `offIcon()` put an icon in the knob. `onColor()` and `offColor()` color the track: `primary`, `danger`, `info`, `success`, `warning` or `gray`.

```php
Toggle::make('is_published')
    ->onIcon('check')
    ->offIcon('x')
    ->onColor('success')
    ->offColor('danger');
```

<!-- example: form-toggle-icons -->

> [!NOTE]
> Before 0.9, the switch had a `data-[state=checked]:bg-xq-primary` class. It's gone now that the track takes its color from `onColor()`, so CSS aimed at that class needs changing.
