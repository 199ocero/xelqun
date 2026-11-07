---
title: Dark mode
description: Choose the default theme, force one, or remove the dark theme.
---

```php
$panel->defaultThemeMode('dark');
```

`defaultThemeMode()` picks the theme until someone chooses. It takes `light`, `dark` or `system`.

```php
$panel->darkMode(false);            // no dark theme
$panel->darkMode(isForced: true);   // only the dark theme
```

People choose from the user menu, and the page fades across where the browser can. [Try it in the demo](demo:/): the user menu is at the bottom of the sidebar.

<!-- screenshot: user-menu | The user menu, with the theme choices -->
