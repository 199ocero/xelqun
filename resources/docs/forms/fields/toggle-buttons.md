---
title: Toggle buttons
description: ToggleButtons shows options as a row of buttons, with colors, icons, grouping and multiple choice.
---

```php
use Xelqun\Forms\Components\ToggleButtons;

ToggleButtons::make('status')
    ->options(['draft' => 'Draft', 'published' => 'Published'])
    ->colors(['draft' => 'warning', 'published' => 'success'])
    ->icons(['draft' => 'pencil', 'published' => 'check'])
    ->inline();
```

<!-- example: form-toggle-buttons -->

## Join the buttons

`grouped()` joins them into one compact control.

```php
ToggleButtons::make('size')->options(['s' => 'S', 'm' => 'M', 'l' => 'L'])->grouped();
```

## Choosing several

```php
ToggleButtons::make('days')->multiple()->options($days)->columns(4);
```

The state is then a list of values.

## Yes and no

```php
ToggleButtons::make('feedback')->boolean();
```

## Disabling options

```php
ToggleButtons::make('status')
    ->options($statuses)
    ->disableOptionWhen(fn (string $value): bool => $value === 'archived');
```
