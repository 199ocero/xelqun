---
title: Styles
description: Four free styles, Default, Soft, Sharp and Mono, that change a panel's shape without touching its colors.
---

```php
use Xelqun\Panels\Support\Preset;

$panel->preset(Preset::Soft);
```

A style changes shape: corners, borders and shadows. Colors still come from [colors()](/panels/theming/colors), so any style works with any palette.

| Style     | Looks like                                                                                |
| --------- | ----------------------------------------------------------------------------------------- |
| `Default` | Rounded corners, a thin light edge along a solid button's top, and a dim gray dark theme  |
| `Soft`    | Rounder corners, pill buttons, softer shadows and quieter borders                         |
| `Sharp`   | Square corners on everything, badges and chips included, and firmer borders               |
| `Mono`    | Black, white and neutral grays only. Buttons, focus rings and links leave your colors out |

Mono is the one style that changes color, because black and white is the point of it. Charts keep their colors so the series stay apart.

A closure runs on every request, so the style can come from a setting:

```php
$panel->preset(fn (): Preset => Preset::tryFrom((string) config('app.panel_style')) ?? Preset::Default);
```

With `themeSwitcher()`, anyone can pick their own style from the user menu. See [letting people choose](/panels/theming/colors#letting-people-choose).

## Outside a panel

Set `data-xq-preset` on `<html>` to `soft`, `sharp` or `mono`. Forms and tables follow it too.

```html
<html data-xq-preset="sharp"></html>
```

[Try it in the demo](demo:/): the palette button at the bottom right switches between the four styles.
