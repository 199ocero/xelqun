---
title: Colors
description: Set a panel's primary, secondary and gray with Tailwind's palettes or a hex value, let people pick their own, and text stays readable whatever they choose.
---

```php
use Xelqun\Panels\Support\Colors\Color;

$panel->colors([
    'primary' => Color::Amber,
    'secondary' => Color::Stone,
    'danger' => Color::Rose,
    'gray' => Color::Slate,
]);
```

`Color` holds Tailwind's 26 palettes. Any color you leave out keeps its default: a blue `primary`, `danger` Red, `gray` Zinc, `info` Blue, `success` Green and `warning` Amber.

- **Primary** is the brand color: solid buttons, links, toggles, the focus ring and the current tab.
- **Secondary** is a soft tint for secondary buttons, gray badges, counts, tags and chips. It follows `gray` unless you name it, so out of the box it's a quiet gray.
- **Gray** tints every neutral surface: the page, cards, borders and muted text.

Text stays readable whatever you choose. A button keeps white text, and its fill deepens just enough for white to clear WCAG AA's 4.5:1. Yellow, Amber and Lime go bright instead, with dark text in their own hue. A hex value gets the same treatment.

## Letting people choose

`themeSwitcher()` adds Style and Colors to the user menu. Anyone can pick a [style](/panels/theming/styles), a primary color and a secondary gray, and reset back to the panel's own. The choice applies at once and before the page paints on the next load, so there's no flash.

```php
$panel->themeSwitcher();
```

Primary offers the 17 colors and secondary the 9 grays, Slate to Olive. A secondary gray shifts the whole neutral family, so badges, counts and the page's grays move together.

The choice is kept in the browser. To keep it on the account, so it follows someone to every browser, add the contract and trait to the user model and publish the column:

```php
use Xelqun\Panels\Auth\Concerns\InteractsWithPanelAppearance;
use Xelqun\Panels\Contracts\HasPanelAppearance;

class User extends Authenticatable implements HasPanelAppearance
{
    use InteractsWithPanelAppearance;
}
```

```bash
php artisan vendor:publish --tag=xelqun-panels-appearance
php artisan migrate
```

Each pick is saved through `POST {panel}/_appearance`, which accepts only the switcher's own choices. A page load reads the account's copy.

## Choosing in code

Pass a closure, which runs on every request, to take colors from a setting.

```php
$panel->colors(fn (): array => ['primary' => Color::fromName(auth()->user()?->theme) ?? Color::Blue]);
```

`Color::options()` gives a Select its choices, and `isGray()` tells the grays apart. Anything that isn't a color is refused, because these end up in a style attribute.

<!-- screenshot: colors | The demo's palette panel, with a base color and a gray to choose from -->

## Outside a panel

Forms and tables on their own read the same seeds. Set them on `:root` in your CSS:

```css
:root {
    --xq-color-primary: #7c3aed;
    --xq-color-secondary: #57534e;
    --xq-color-gray: #57534e;
}
```

Every surface, border, ring and text color follows in both themes. The rest are in [CSS hooks](/panels/theming/css-hooks).

[Try it in the demo](demo:/): the palette button at the bottom right changes the theme, the style, the primary color and the secondary gray. The demo keeps your choice in your session, because its accounts are shared.
