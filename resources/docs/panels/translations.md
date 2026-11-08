---
title: Translations
description: Change a panel's text, or add a language.
---

All text comes from language files. Publish them to change them, or to add a language. Much of a panel's text, such as paging, filters and confirm dialogs, comes from forms and tables, so publish all three:

```bash
php artisan vendor:publish --tag=xelqun-forms-lang
php artisan vendor:publish --tag=xelqun-tables-lang
php artisan vendor:publish --tag=xelqun-panels-lang
```

A panel uses the app's locale, from `app()->getLocale()`.

In `shell.php`, each key is the English text.
