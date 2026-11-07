---
title: Checkbox list
description: CheckboxList picks many options with all of them on show, with columns, search and bulk toggles.
---

```php
use Xelqun\Forms\Components\CheckboxList;

CheckboxList::make('technologies')
    ->options([
        'laravel' => 'Laravel',
        'vue' => 'Vue',
        'tailwind' => 'Tailwind CSS',
    ])
    ->columns(2);
```

<!-- example: form-checkbox-list -->

The state is a list of values, so a model needs an array cast.

## Descriptions

```php
CheckboxList::make('notifications')
    ->options(['mail' => 'Email', 'sms' => 'Text message'])
    ->descriptions(['mail' => 'Sent to your work address.', 'sms' => 'Standard rates apply.']);
```

## Search and bulk buttons

```php
CheckboxList::make('permissions')
    ->options($permissions)
    ->searchable()
    ->bulkToggleable();
```

`bulkToggleable()` adds buttons that select and deselect every option at once.

## Disabling options

```php
CheckboxList::make('plans')
    ->options($plans)
    ->disableOptionWhen(fn (string $value): bool => $value === 'legacy');
```

A disabled option can be seen but not chosen, and the server refuses it too.
