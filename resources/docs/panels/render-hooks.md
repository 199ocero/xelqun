---
title: Render hooks
description: Put your own Vue component in the panel's shell.
---

```php
use Xelqun\Panels\View\RenderHook;

$panel->renderHook(RenderHook::TopbarEnd, 'acme/credits', fn (): array => ['left' => auth()->user()->credits]);
```

Register the component in `createXelqunForms({ components })`, and the array reaches it as props.

| Hook                              | Where                 |
| --------------------------------- | --------------------- |
| `SidebarStart`, `SidebarEnd`      | The sidebar           |
| `TopbarStart`, `TopbarEnd`        | The top bar           |
| `ContentStart`, `ContentEnd`      | Around a page         |
| `AuthFormBefore`, `AuthFormAfter` | Around a sign-in form |

Only the two auth hooks run for guests.

The note under this login form is an `AuthFormAfter` hook.

<!-- screenshot: login | A note under the login form -->

[See them in the demo](demo:/): the card at the bottom of the sidebar and the palette button are both hooks.
