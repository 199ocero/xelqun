---
title: Pages
description: Add pages to a panel with header actions, tables, forms and your own Vue.
---

```bash
php artisan make:xelqun-page Reports
```

```php
use Xelqun\Actions\Action;
use Xelqun\Panels\Pages\Page;

class Reports extends Page
{
    protected static ?string $navigationIcon = 'chart-column';

    public function headerActions(): array
    {
        return [Action::make('export')->authorize('export')->action(fn () => null)];
    }

    public function content(): array
    {
        return [ReportsTable::make()];
    }
}
```

On a page of its own there's no record or model, so `authorize('export')` asks a plain gate. Define it in a service provider's `boot()`, or the button stays hidden:

```php
use Illuminate\Support\Facades\Gate;

Gate::define('export', fn (User $user): bool => (bool) $user->is_admin);
```

A page is open to everyone the panel lets in. Override `canAccess()` to narrow it.

`content()` takes tables, action sets and `FormContent`.

```php
use Xelqun\Panels\Pages\FormContent;

public function content(): array
{
    return [new FormContent(SettingsForm::make(), route('settings.update'))];
}
```

<!-- screenshot: custom-page | A custom page with tabs and a form -->

## What you can set

| Method                                            | What it does                  |
| ------------------------------------------------- | ----------------------------- |
| `getHeading()`, `getSubheading()`, `getEyebrow()` | The text at the top.          |
| `getBreadcrumbs()`                                | The breadcrumbs.              |
| `getTabs()`                                       | Tabs above the content.       |
| `getSubNavigation()`                              | Tabs across the page.         |
| `canAccess()`                                     | Who may open it.              |
| `getNavigationBadge()`                            | A count in the sidebar.       |
| `shouldRegisterNavigation()`                      | Whether it joins the sidebar. |

## Your own Vue page

Override `getComponent()` to render your own Vue page, and use `XqPanelLayout` as its layout to stay inside the panel.

[See custom pages in the demo](demo:/components).
