---
title: Callouts and empty states
description: Callout and EmptyState show a message inside a form, with a status, an icon and buttons.
---

## Callout

```php
use Xelqun\Forms\Layouts\Callout;

Callout::make('Your plan ends soon')
    ->description('Renew it to keep your data.')
    ->warning();
```

<!-- example: form-callouts -->

`info()`, `success()`, `warning()` and `danger()` each bring an icon. `icon()` changes it, and `footer()` adds components below the text.

## EmptyState

```php
use Xelqun\Forms\Layouts\EmptyState;

EmptyState::make('No items yet')
    ->description('Add one to get started.')
    ->icon('inbox')
    ->footer([...]);
```

`contained(false)` drops the border.
