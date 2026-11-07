---
title: Radio
description: Radio picks one option with all of them on show, inline or stacked.
---

```php
use Xelqun\Forms\Components\Radio;

Radio::make('type')
    ->options(['individual' => 'Individual', 'business' => 'Business'])
    ->inline();
```

<!-- example: form-radio -->

## Descriptions

```php
Radio::make('plan')
    ->options(['free' => 'Free', 'pro' => 'Pro'])
    ->descriptions(['free' => 'For side projects.', 'pro' => 'For teams.']);
```

## Yes and no

```php
Radio::make('feedback')->boolean('Yes, please', 'No, thanks');
```

## Disabling options

```php
Radio::make('plan')
    ->options($plans)
    ->disableOptionWhen(fn (string $value): bool => $value === 'legacy');
```

The server refuses a disabled option too.
