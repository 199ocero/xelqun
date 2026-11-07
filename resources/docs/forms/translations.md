---
title: Translations
description: Change the text of Xelqun Forms, or add a language, with Laravel's language files.
---

The package's text comes from language files. The browser's text comes from them too, so one translation covers both sides.

Publish them to change them, or to add a language.

```bash
php artisan vendor:publish --tag=xelqun-forms-lang
```

Your own labels are plain strings, so pass them through `__()`.

```php
TextInput::make('name')->label(__('orders.name'));
```
