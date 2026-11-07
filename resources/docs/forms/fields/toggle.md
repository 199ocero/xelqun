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
