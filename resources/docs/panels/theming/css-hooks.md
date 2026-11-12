---
title: CSS hooks
description: The CSS variables and attributes you can style a panel with, which stay the same through 1.x.
---

Restyle a panel from your own CSS. Load it after Xelqun's, and target the variables and attributes on this page. They're the public ones, and they don't change in a 1.x release. Anything else that starts with `data-xq-` is there for tests and may change.

## Seeds

Every color below is worked out from these. Set them on `:root`, and both themes follow. A panel sets them from [colors()](/panels/theming/colors).

| Variable                                                                           | Sets                                                              |
| ---------------------------------------------------------------------------------- | ----------------------------------------------------------------- |
| `--xq-color-primary`                                                               | The brand color                                                   |
| `--xq-color-secondary`                                                             | The tint of secondary buttons, badges and chips (gray unless set) |
| `--xq-color-gray`                                                                  | The tint of every neutral surface                                 |
| `--xq-color-danger`, `--xq-color-success`, `--xq-color-warning`, `--xq-color-info` | The status colors                                                 |
| `--xq-color-primary-fill`, `--xq-color-primary-foreground`                         | A solid primary deep enough for its text, when you have one       |

## Colors

To change one color on its own, set it on `:root[data-xq-panel]`, and again on `:root[data-xq-panel].dark` for the dark theme. Each `-foreground` is the text on that color.

| Variable                                          | Colors                                    |
| ------------------------------------------------- | ----------------------------------------- |
| `--xq-background`, `--xq-foreground`              | The page and its text                     |
| `--xq-shell`                                      | Behind the sidebar and the page           |
| `--xq-card`, `--xq-card-foreground`               | Cards, sections and tables                |
| `--xq-popover`, `--xq-popover-foreground`         | Menus, popovers and dialogs               |
| `--xq-primary`, `--xq-primary-foreground`         | Main buttons and links                    |
| `--xq-secondary`, `--xq-secondary-foreground`     | Secondary buttons, gray badges and chips  |
| `--xq-secondary-hover`                            | A hovered secondary button                |
| `--xq-raised`                                     | Outline buttons                           |
| `--xq-primary-text`                               | Colored text and links                    |
| `--xq-muted`, `--xq-muted-foreground`             | Quiet backgrounds and helper text         |
| `--xq-accent`, `--xq-accent-foreground`           | Hovered rows and menu items               |
| `--xq-destructive`, `--xq-destructive-foreground` | Danger buttons and errors                 |
| `--xq-success`, `--xq-warning`, `--xq-info`       | Badges, notifications and callouts        |
| `--xq-border`, `--xq-input`, `--xq-ring`          | Borders, field borders and the focus ring |
| `--xq-nav-hover`, `--xq-nav-active`               | A hovered and the current sidebar link    |
| `--xq-chart-1` to `--xq-chart-8`                  | Chart series, in order                    |

The panel works these out from `colors()`, so for most changes [colors](/panels/theming/colors) is the simpler place.

## Shape and motion

| Variable                                                         | Sets                                                 |
| ---------------------------------------------------------------- | ---------------------------------------------------- |
| `--xq-radius`                                                    | The corner radius everything else is worked out from |
| `--xq-button-radius`                                             | Buttons' corners                                     |
| `--xq-pill-radius`                                               | Badges, counts, chips and round icon buttons         |
| `--xq-button-highlight`                                          | The light edge along a solid button's top            |
| `--xq-button-shadow`                                             | The shadow under an outline button                   |
| `--xq-duration-fast`, `--xq-duration-base`, `--xq-duration-slow` | How long animations take                             |
| `--xq-ease-in`, `--xq-ease-out`, `--xq-ease-spring`              | Their easing                                         |
| `--xq-stagger`                                                   | The delay between rows that ripple in                |

## Attributes

| Attribute            | On                                                                                      |
| -------------------- | --------------------------------------------------------------------------------------- |
| `data-xq-panel`      | The page's root element, on every panel page                                            |
| `data-xq-panel-auth` | The sign-in, registration and password pages                                            |
| `data-xq-preset`     | The root, as `default`, `soft`, `sharp` or `mono`. See [styles](/panels/theming/styles) |
| `data-xq-page`       | Each page's content                                                                     |
| `data-xq-widget`     | Each widget, with its id                                                                |
| `data-xq-stats`      | A stats overview widget                                                                 |
| `data-xq-logo`       | The logo, as `light` or `dark`, so each theme shows its own                             |
| `data-xq-motion-off` | The root, when the viewer has turned animations off                                     |
| `data-xq-path`       | Each form field, with its state path                                                    |

```css
:root[data-xq-panel] [data-xq-widget="revenue"] {
    border: 1px solid var(--xq-primary);
}
```
