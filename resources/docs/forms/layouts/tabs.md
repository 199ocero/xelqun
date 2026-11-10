---
title: Tabs
description: Tabs put groups of fields on separate tabs, with icons, badges, a vertical layout, and a badge when a tab holds an error.
---

```php
use Xelqun\Forms\Layouts\Tab;
use Xelqun\Forms\Layouts\Tabs;

Tabs::make('Details')->tabs([
    Tab::make('General')->schema([...]),
    Tab::make('Pricing')->columns(2)->schema([...]),
    Tab::make('Media')->schema([...]),
]);
```

<!-- example: form-tabs -->

Every tab is submitted, not just the open one. A tab with an error shows a badge, so nobody hunts for what went wrong.

## Icons and badges

`icon()` puts an icon before a tab's label. `badge()` puts a count or short word after it, and `badgeColor()` colors it: `primary`, `danger`, `info`, `success`, `warning` or `gray`, which is the default.

```php
Tabs::make('Details')->tabs([
    Tab::make('General')->icon('settings')->schema([...]),
    Tab::make('Pricing')->icon('dollar-sign')->schema([...]),
    Tab::make('Reviews')->icon('star')->badge(3)->badgeColor('warning')->schema([...]),
]);
```

<!-- example: form-tabs-icons -->

## Layout

`vertical()` puts the tabs in a column beside their content, on wide screens. `contained()` puts the tabs and their content in a card.

```php
Tabs::make('Settings')->vertical()->contained()->tabs([...]);
```

<!-- example: form-tabs-vertical -->

## The open tab

`activeTab()` sets the tab that starts open, counting from 1. `persistTab()` remembers the open tab in the browser, for the next visit.

```php
Tabs::make('Details')->activeTab(2)->persistTab()->tabs([...]);
```

A tab in the URL still wins over `activeTab()`.

## Linking to a tab

`persistTabInQueryString()` keeps the open tab in the URL, as `?tab=pricing`, so a link can open it.

```php
Tabs::make('Details')->persistTabInQueryString()->tabs([...]);
```

Pass a key when a page has more than one set of tabs.

```php
Tabs::make('Details')->persistTabInQueryString('details-tab')->tabs([...]);
```
