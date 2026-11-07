---
title: Logos and favicon
description: Set a panel's name, logos and favicon.
---

```php
$panel
    ->brandName('Acme')
    ->brandLogo('/images/logo.svg')
    ->darkModeBrandLogo('/images/logo-dark.svg')
    ->brandLogoHeight('1.75rem')
    ->favicon('/favicon.svg');
```

<!-- screenshot: dashboard | A logo at the top of the sidebar -->

Both logos are in the page, so the theme changes with no flash.

## Page width

`maxContentWidth()` sets how wide pages get, from `sm` to `7xl`, or `full`.

```php
$panel->maxContentWidth('full');
```
