---
title: Tabs
description: Tabs put groups of fields on separate tabs, with a badge when a tab holds an error.
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

## Linking to a tab

`persistTabInQueryString()` keeps the open tab in the URL, as `?tab=pricing`, so a link can open it.

```php
Tabs::make('Details')->persistTabInQueryString()->tabs([...]);
```

Pass a key when a page has more than one set of tabs.

```php
Tabs::make('Details')->persistTabInQueryString('details-tab')->tabs([...]);
```
