---
title: Checkbox
description: Checkbox holds one true or false value.
---

```php
use Xelqun\Forms\Components\Checkbox;

Checkbox::make('is_admin');
```

<!-- example: form-checkbox -->

Add a line of text under the label with `description()`.

```php
Checkbox::make('terms')
    ->label('I accept the terms')
    ->description('You can read them at any time from your account.')
    ->required();
```

A model needs a `boolean` cast for this field. With `required()` on it, the box has to be ticked.

`inlineLabel()` isn't needed here: checkboxes and toggles already have their label beside them.
