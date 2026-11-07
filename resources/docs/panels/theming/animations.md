---
title: Animations
description: Motion is built in, from one set of tokens, with a switch for each viewer.
---

Animations are on by default. Anyone can turn them off for themselves in the user menu, and the choice stays in their browser.

<!-- screenshot: user-menu | The animations switch in the user menu -->

```php
$panel->animations(false);
```

This starts a panel with them off. A system set to reduced motion always wins.

To change the feel, override the tokens.

```css
:root {
    --xq-duration-fast: 120ms;
    --xq-duration-base: 200ms;
    --xq-duration-slow: 320ms;
    --xq-stagger: 45ms;
    --xq-ease-out: cubic-bezier(0.16, 1, 0.3, 1);
}
```
