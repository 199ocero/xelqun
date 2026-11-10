---
title: How panels work
description: What happens inside xelqun/panels, from the panel provider to its routes, middleware, resource pages, sign in, tenancy and the Vue shell.
---

## The big picture

A panel is a set of routes, plus a shell around them: the sidebar, topbar, search and user menu. Each page in it is a PHP class that returns forms, tables and widgets. The panel doesn't draw forms or tables itself. It hands them to the forms and tables packages.

```text
AdminPanelProvider::panel()        once, when the app boots
  │  registers the Panel in the Panels registry
  ▼
routes/xelqun-panels.php           once, when the app boots
  │  a route for each page, resource page and sign-in screen
  ▼
GET /admin/products/5/edit         every request
  │  middleware: web, EnterPanel, Authenticate, IdentifyTenant
  ▼
EditRecord page (invokable)
  │  findRecord(5) → policy "update" → ResourceForm::toSchema()
  ▼
Inertia::render('xelqun/Panel', ['page' => ...])
  │  plus the shared "xelqunPanel" prop: navigation, theme, user
  ▼
XqPanelLayout draws the shell, XqPanelPage draws the content
  with <XqForm> from forms and <XqTable> from tables
```

If you haven't read [How Xelqun works](/introduction/architecture) yet, start there. It explains the schema and the token, which this page builds on.

## Where the code lives

| Folder                  | What's in it                                                                  |
| ----------------------- | ----------------------------------------------------------------------------- |
| `src/Panel.php`         | The settings of one panel, as a fluent builder.                               |
| `src/Panels.php`        | The list of panels, and which panel and tenant this request is in.            |
| `src/PanelProvider.php` | The base class your panel providers extend.                                   |
| `src/Resources/`        | `Resource`, its pages, relation managers, and the form and table it builds.   |
| `src/Pages/`            | The `Page` base class and the dashboard.                                      |
| `src/Widgets/`          | Stats, chart, list, tile and table widgets.                                   |
| `src/Infolists/`        | Read-only entries for view pages.                                             |
| `src/Actions/`          | Ready-made actions, such as edit, delete, restore, import and export.         |
| `src/Auth/`             | The sign-in forms, profile, and two-factor codes.                             |
| `src/Http/Middleware/`  | `EnterPanel`, `Authenticate`, `IdentifyTenant` and `RequireMultiFactorSetup`. |
| `src/Http/Controllers/` | Sign in, saving records, global search, notifications and exports.            |
| `src/Navigation/`       | Navigation items and clusters.                                                |
| `routes/`               | Makes every panel's routes.                                                   |
| `resources/views/`      | `app.blade.php`, the root view. It sets the theme before the page paints.     |
| `resources/js/`         | The shell layout, the page component, widgets and the `panelsPlugin()`.       |

## Follow the code

Watch it first, then read the code behind each step below.

<!-- flow: panels-edit -->

Here's what happens when someone opens and saves `/admin/products/5/edit`, in the package's own code. Each excerpt is cut down to the lines that matter, and `// ...` marks what was left out. The file path above each one is inside `vendor/xelqun/panels` or `node_modules/@xelqun/panels`.

### 1. Your provider registers the panel

When Laravel registers your `AdminPanelProvider`, the base class calls your `panel()` and adds the result to the registry.

```php
// src/PanelProvider.php
abstract class PanelProvider extends ServiceProvider
{
    abstract public function panel(Panel $panel): Panel;

    public function register(): void
    {
        $this->app->singletonIf(Panels::class);
        $this->app->make(Panels::class)->register($this->panel(Panel::make()));
    }
}
```

### 2. The routes file makes a route for each resource page

This runs once, when the app boots. Notice that the controller is the page class itself, such as `EditRecord::class`, and the resource is passed as a route default.

```php
// routes/xelqun-panels.php
foreach ($panel->getResources() as $resource) {
    $pages = $resource::getPages();
    $slug = $resource::getSlug();

    $parent = $resource::getParentResource();

    Route::prefix($parent === null ? $slug : $parent::getSlug()."/{parentRecord}/{$slug}")->name("resources.{$slug}.")->group(function () use ($pages, $resource, $panel): void {
        Route::get('', $pages['index'])->name('index')->defaults('resource', $resource);

        if (isset($pages['create'])) {
            Route::get('create', $pages['create'])->name('create')->defaults('resource', $resource);
            Route::post('', [ResourceRecordController::class, 'store'])->name('store')->defaults('resource', $resource);
        }

        if (isset($pages['edit'])) {
            Route::get('{record}/edit', $pages['edit'])->name('edit')->defaults('resource', $resource);
            Route::put('{record}', [ResourceRecordController::class, 'update'])->name('update')->defaults('resource', $resource);
        }

        // ... view, export, and the update route of a singular resource
    });
}
```

So `GET /admin/products/5/edit` calls the `EditRecord` page, with `resource` set to `ProductResource` and `record` set to `5`.

### 3. Middleware enters the panel

Before the page runs, `EnterPanel` marks this request as being in the panel, picks the root view, and shares the shell data.

```php
// src/Http/Middleware/EnterPanel.php
public function handle(Request $request, Closure $next, string $id): Response
{
    $panel = $this->panels->get($id);

    abort_if($panel === null, 404);

    $this->panels->enter($panel);

    Inertia::setRootView('xelqun-panels::app');

    // ... encrypt history over HTTPS

    Inertia::share('xelqunPanel', fn (): ?array => $this->panels->current()?->toShell());

    return $next($request);
}
```

Then `Authenticate` checks the user, and `IdentifyTenant` enters the tenant if the panel has one.

### 4. The page finds the record

A resource page is invokable. It reads the resource and the record key from the route, and finds the record through the resource's query.

```php
// src/Resources/Pages/ResourcePage.php
public function __invoke(): Response
{
    $this->mountFromRoute();

    return parent::__invoke();
}

protected function mountFromRoute(): void
{
    $route = request()->route();
    $this->resource = $route->parameter('resource');

    $key = $route->parameter('record');

    $this->record = $key === null ? null : $this->resource::findRecord($key);
}

// src/Resources/Resource.php
public static function findRecord(int|string $key): Model
{
    return static::getEloquentQuery()->whereKey($key)->firstOrFail();
}
```

### 5. The page checks the policy and renders

`Page::__invoke()` is the same for every page. It checks access, then renders `xelqun/Panel` with the page's data.

```php
// src/Pages/Page.php
public function __invoke(): Response
{
    abort_unless($this->isAccessible(), 403, $this->getAccessDeniedReason());

    return Inertia::render($this->getComponent(), [...$this->getWidgetProps(), ...$this->getProps(), 'page' => $this->toData()]);
}

// src/Resources/Pages/EditRecord.php
public function isAccessible(): bool
{
    return $this->resource::can('update', $this->record);
}
```

### 6. The edit page's content is a form

`content()` says what's on the page. For the edit page, it's a `ResourceForm` and the URL it saves to, followed by any relation manager tables.

```php
// src/Resources/Pages/EditRecord.php
public function content(): array
{
    return [
        new FormContent(
            ResourceForm::for($this->resource, ResourceForm::EDIT, $this->record),
            $this->resource::getUrl('update', ['record' => $this->record]),
            'put',
            __('xelqun-panels::resources.save'),
        ),
        ...$this->getRelationTables(isReadOnly: false),
    ];
}
```

`ResourceForm` is an ordinary forms `Form`. The resource travels in its context, so it ends up in the token. Its `schema()` is your resource's `form()`, and its `authorize()` asks your policy.

```php
// src/Resources/ResourceForm.php
public static function for(string $resource, string $operation, ?Model $record = null): self
{
    $form = self::make($record, ['resource' => $resource, 'operation' => $operation]);

    return $operation === self::VIEW ? $form->disabled() : $form;
}

public function schema(): array
{
    $resource = $this->getResource();
    $infolist = $this->getOperation() === self::VIEW ? $resource::infolist() : [];

    return $infolist === [] ? $resource::form() : $infolist;
}

public function resolveRecord(int|string $key): Model
{
    return $this->getResource()::findRecord($key);
}

public function authorize(?Authenticatable $user, ?Model $record): bool
{
    return $this->getResource()::can(match ($this->getOperation()) {
        self::CREATE => 'create',
        self::VIEW => 'view',
        default => 'update',
    }, $record);
}
```

### 7. The content becomes JSON

`toData()` turns each part of the content into JSON. A table calls the tables package's `toSchema()`, and a form calls the forms package's `toSchema()` through `FormContent`.

```php
// src/Pages/Page.php
private function toData(): array
{
    return [
        'title' => $this->getHeading(),
        'breadcrumbs' => $this->getBreadcrumbs(),
        'headerActions' => $this->getHeaderActions() === [] ? null : PageActionSet::for($this)->toSchema(),
        'content' => array_map(fn (Table|ActionSet|FormContent $item): array => match (true) {
            $item instanceof Table => [
                'type' => 'table',
                'schema' => $item->toSchema(),
                'heading' => $item instanceof RelationTable ? $item->getHeading() : null,
            ],
            $item instanceof ActionSet => ['type' => 'actions', 'schema' => $item->toSchema()],
            $item instanceof FormContent => $item->toArray(),
        }, $this->getVisibleContent()),
        // ... subheading, tabs, sub-navigation
    ];
}

// src/Pages/FormContent.php
public function toArray(): array
{
    $schema = $this->form->toSchema();

    return [
        'type' => 'form',
        'schema' => $schema,
        'action' => $this->action,
        'method' => $this->method,
        'submitLabel' => $this->submitLabel,
        // ... revision
    ];
}
```

So the Inertia props look like this:

```json
{
    "page": {
        "title": "Edit Blue shirt",
        "content": [
            {
                "type": "form",
                "schema": {
                    "$xq": 1,
                    "id": "...",
                    "token": "...",
                    "state": {},
                    "components": []
                },
                "action": "/admin/products/5",
                "method": "put",
                "submitLabel": "Save"
            }
        ]
    },
    "xelqunPanel": {
        "navigation": [],
        "theme": {},
        "user": { "name": "...", "email": "..." }
    }
}
```

### 8. Inertia finds the Vue page

Inertia looks for `xelqun/Panel` in your app's pages. The installer wrote a two-line file there that re-exports the package's page:

```vue
<!-- resources/js/pages/xelqun/Panel.vue (your app) -->
<script lang="ts">
import { XqPanelPage } from '@xelqun/panels';

export default XqPanelPage;
</script>
```

Your `app.ts` registers the forms plugin with `panelsPlugin()`, so the panel's components are in the registry:

```ts
// resources/js/app.ts (your app)
createInertiaApp({
    withApp: (app) => {
        app.use(createXelqunForms({ plugins: [panelsPlugin()] }));
    },
    // ...
});
```

### 9. `XqPanelPage` draws each part of the content

The page uses `XqPanelLayout`, which draws the sidebar and topbar from `xelqunPanel`. Then it loops over `page.content`, and hands each schema to the component from its own package. This excerpt is simplified: the real template also puts relation tables under headings or tabs, and draws the header, widgets and render hooks.

```vue
<!-- resources/js/pages/XqPanelPage.vue -->
<script setup lang="ts">
import { XqActions, XqForm } from '@xelqun/forms';
import { XqTable } from '@xelqun/tables';

defineOptions({ layout: XqPanelLayout });
</script>

<template>
    <template v-for="item in page.content" :key="item.schema.id">
        <XqTable v-if="item.type === 'table'" :table="item.schema" />
        <XqForm
            v-else-if="item.type === 'form'"
            :key="item.revision"
            :form="item.schema"
            :action="item.action"
            :method="item.method"
            :submit-label="item.submitLabel"
        />
        <XqActions v-else :actions="item.schema" />
    </template>
</template>
```

From here, the form works exactly as in [How forms work](/forms/architecture): `XqForm` makes a store, `XqNode` picks each field's component, and live changes go to `/_xelqun/forms/update`.

### 10. Saving goes back to the page

When the user saves, `XqForm` sends an Inertia `PUT` to `item.action`. That route points at `ResourceRecordController`, which hands it to the resource's own edit page:

```php
// src/Http/Controllers/ResourceRecordController.php
public function update(Request $request): RedirectResponse
{
    $pages = $this->pages($request);

    return app($pages['edit'] ?? $pages['index'])->update($request);
}

// src/Resources/Pages/EditRecord.php
public function update(Request $request): RedirectResponse
{
    $this->mountFromRoute();

    abort_unless($this->isAccessible(), 403, $this->getAccessDeniedReason());

    ResourceForm::for($this->resource, ResourceForm::EDIT, $this->record)->save($request);

    Notification::make()
        ->title(__('xelqun-panels::resources.saved', ['label' => Str::ucfirst($this->resource::getModelLabel())]))
        ->success()
        ->send();

    return redirect()->to($this->resource::getRecordUrl($this->record) ?? $this->resource::getUrl());
}
```

The page finds the record and checks the policy again, because a `PUT` is a new request. Then `save()` is the forms package's own: lock, validate, save in a transaction.

The sections below go through each of these parts in more detail.

## Registering a panel

Your `AdminPanelProvider` extends `PanelProvider`. When Laravel registers it, it calls your `panel()` method and adds the result to the `Panels` registry.

The registry checks the panel right away. An empty or duplicate ID, two panels on the same path and domain, or required two-factor without a profile page all throw while the app boots.

Resources can be found by `discoverResources()`, or listed with `resources()`. Pages and widgets are always listed by hand.

### Which panel am I in?

`Panels` doesn't keep the current panel on itself. That would leak between requests under Octane. Instead, `Panels::enter()` stores it in the request's attributes, and switches the auth guard to the panel's guard. The tenant is stored the same way.

The `EnterPanel` middleware calls `enter()` on every panel route.

## Routes

`routes/xelqun-panels.php` runs once at boot, and loops over every panel. Each panel's routes are named `xelqun-panels.{id}.` and sit under its path and domain. Here's how they're grouped:

```text
web, EnterPanel:{id}
├── login, register, forgot-password, reset-password, two-factor   only if turned on
└── Authenticate
    └── {tenant}/  (with tenancy)  IdentifyTenant, RequireMultiFactorSetup
        ├── ''                         dashboard
        ├── {page-slug}                each page
        ├── profile, _search, _notifications
        └── {resource-slug}/
            ├── ''                     list
            ├── create                 create page,  POST '' saves it
            ├── {record}               view page
            └── {record}/edit          edit page,    PUT {record} saves it
```

The panel's own `middleware()` joins the first line, `authMiddleware()` follows `Authenticate`, and `tenantMiddleware()` follows `IdentifyTenant`. `tenantRoutePrefix()` adds a segment before `{tenant}`, and `tenantDomain()` moves it into the host.

A nested resource lives under its parent, as in `authors/{parentRecord}/posts`.

The page class is the controller. A list route points straight at the `ListRecords` class, and the resource it's for is passed as a route default. That's how one `ListRecords` class serves every resource.

The package doesn't add Inertia's middleware. It uses your app's `HandleInertiaRequests` from the `web` group.

## Resources

A `Resource` is all static methods. You give it a model, a `form()` and `columns()`, and it builds the pages.

Two methods do the important work:

- **`getEloquentQuery()`** is the one query every page, table and search goes through. For a nested resource it goes through the parent's relationship. With tenancy, it's a 404 when there's no tenant.
- **`findRecord($key)`** is `getEloquentQuery()->whereKey($key)->firstOrFail()`. There's no route model binding, on purpose, so a record outside the query is a 404.

Permissions come from your Laravel policy, through `Resource::can()`:

| Page   | Ability   |
| ------ | --------- |
| List   | `viewAny` |
| Create | `create`  |
| Edit   | `update`  |
| View   | `view`    |

### A page, step by step

When the edit page runs:

1. `mountFromRoute()` reads the resource and record key from the route, and calls `findRecord()`.
2. `__invoke()` checks `isAccessible()`, which asks the policy. It's a 403 if the answer is no.
3. `content()` builds a `ResourceForm`. That's one `Form` class for every resource. It knows which resource it's for from its context, which travels in the token. Its `schema()` returns your resource's `form()`.
4. `toData()` turns the content into JSON, such as `{ type: "form", schema: ..., action: ".../products/5", method: "put" }`.
5. `Inertia::render('xelqun/Panel', [...])` sends it, with the widgets as deferred props.

The list page works the same way, with a `ResourceTable` instead. That's one `Table` class for every resource, whose `query()` is the resource's `getEloquentQuery()`, with the active tab and soft deletes applied.

### Saving

The form posts to the resource's `store` or `update` route. `ResourceRecordController` hands it to the resource's own `CreateRecord` or `EditRecord` page, so a page you replace also replaces how it saves.

The page finds the record again, checks the policy again, and calls `ResourceForm::save()`. That's the forms package's `save()`: lock, validate, save in a transaction. On a new record, `ResourceForm` fills in the tenant on the server, so the browser can never choose it. Then the page sends a notification and redirects.

## Relation managers and nested resources

- **A relation manager** adds a table to the edit and view pages. It's a `RelationTable`, whose `query()` is the relationship. The owner's key is in the table's token, and the owner is loaded again with `findRecord()`. It needs `view` on the owner and `viewAny` on the related model. Its create, edit and delete buttons only show when the user can `update` the owner.
- **A nested resource** sets `$parentResource`. Its routes go under the parent record, the parent is loaded and checked with `view` first, and its queries go through the parent's relationship. Nested resources aren't in the sidebar or global search, because they only make sense inside a parent.

## Pages, widgets and the dashboard

A page extends `Page` and returns its parts from `content()`. To use a Vue page of your own, override `getComponent()` and `getProps()`. The shell still comes, because it's shared data.

The dashboard is a page with an empty slug, and its widgets come from the panel's `widgets()`.

Widgets load with `Inertia::defer()`, after the page shows. Each widget's `canView()` is checked, and each one is wrapped in `rescue()`, so one failing widget doesn't break the rest. The browser shows a skeleton until they arrive.

A widget's `type` picks how it's drawn: stats, chart, list, tiles or table. Any other type is looked up in the forms component registry, which is how [custom widgets](/panels/widgets/custom) work.

## Signing in

`Panel::allows($user)` decides who may enter. The user model must implement `PanelUser`, and its `canAccessPanel()` must return true.

The `Authenticate` middleware checks, in order:

1. **A guest** is sent to the panel's login page.
2. **A user the panel doesn't allow** gets a 403.
3. **An unverified email** is sent to the verification page, when verification is on.
4. **A two-factor challenge not passed yet** is sent to the challenge.

The login page is a forms `Form`. When it's submitted, the password is checked, but the session only starts if the panel allows the user and any two-factor challenge has been passed. A passed challenge is kept in the session, and checked on every request.

## Tenancy

With `tenant()`, every route goes under `{tenant}`, and `IdentifyTenant` resolves it:

1. Finds the tenant from the URL. One that doesn't exist, or that the user can't reach through `HasTenants`, is a 404.
2. Calls `Panels::enterTenant()`. That sets `URL::defaults()`, so every `route()` call fills in the tenant for you.
3. Adds a global scope to each resource's model, so its queries only return the tenant's records.

> [!WARNING]
> The tenant scope is added to the model class. During a tenant request it applies to every query on that model, including your own code outside the panel. Set `$isScopedToTenant = false` on a resource whose model isn't owned by a tenant.

Switching tenants is just going to another tenant's URL.

## Requests outside the panel's routes

Live updates, uploads, table queries and actions go to the forms and tables endpoints, under `/_xelqun`. Those routes know nothing about panels. So how do they know the panel and tenant?

The panel adds them to the token. `PanelsServiceProvider` hooks into `FormTokenEncrypter::scopeUsing()`:

- **When a token is made**, it stamps it with the panel ID, the tenant's key, and the parent record's key.
- **When a token comes back**, it enters that panel again, checks the user may still enter it and reach the tenant, and enters the tenant and parent.

So a user removed from a tenant can't keep using an open page.

## Shared data and navigation

`EnterPanel` shares one prop on every page, `xelqunPanel`, made by `Panel::toShell()`. It has the panel's brand, theme colors and font, navigation, translated messages, render hooks, notification settings, and the user's name and email. Nothing else about the user is sent.

The navigation is built on every request by `Panel::getNavigation()`. It lists pages the user can open and resources they can `viewAny`, grouped and sorted. A cluster becomes one item in the sidebar, and its members become tabs.

Global search posts to `_search`. Each resource the user can `viewAny` searches its `getGloballySearchableAttributes()`, and every result is checked against the `view` policy.

## In the browser

The server renders `xelqun/Panel` or `xelqun/PanelAuth`. These are small files in your app, at `resources/js/pages/xelqun/`, written by `xelqun-panels:install`. They re-export `XqPanelPage` and `XqPanelAuthPage` from `@xelqun/panels`, because Inertia only finds pages inside your app.

- `XqPanelPage` uses `XqPanelLayout`, which reads `xelqunPanel` and draws the sidebar, topbar, search, tenant menu and notifications.
- The page loops over `page.content`. A `table` becomes `<XqTable>`, a `form` becomes `<XqForm>`, and actions become `<XqActions>`.
- Table rows aren't in the page props. `XqTable` fetches them from the tables endpoint.
- `panelsPlugin()` is a forms plugin. You add it to `createXelqunForms()`, and it registers the panel's components and icons.

## Theming, render hooks and plugins

- **Colors** become CSS variables, such as `--xq-panel-primary`. `app.blade.php` puts them on `<html>` and sets dark mode before the page paints, so there's no flash.
- **Render hooks** put a Vue component into a spot in the shell, such as `RenderHook::SidebarEnd`. The component is looked up in the forms registry.
- **A plugin** implements `Plugin`. Its `register()` runs when you add it to the panel, and its `boot()` runs when the app boots.

## What panels takes from forms and tables

| From                 | Used for                                                       |
| -------------------- | -------------------------------------------------------------- |
| `Form`               | `ResourceForm`, the sign-in forms and the profile form.        |
| `ModelPersister`     | Saving records from relation managers and modals.              |
| `FormTokenEncrypter` | Putting the panel and tenant in every token.                   |
| `Xelqun\Actions`     | Every button, and the ready-made actions.                      |
| `Notification`       | The "Saved" message after a save, and others.                  |
| `Table`              | `ResourceTable`, `RelationTable` and table widgets.            |
| Columns              | Infolist entries, which show a column's value without a table. |
| `XqForm`, `XqTable`  | Drawing the page content.                                      |

## Debugging tips

- **A 404 on a record that exists.** It's outside `getEloquentQuery()`: another tenant, another parent, or a soft-deleted record.
- **A 403 on a page.** Check the policy ability in the table above. In debug mode the 403 page says why.
- **A page isn't in the sidebar.** The user can't access it, or `shouldRegisterNavigation()` returns false.
- **Run `php artisan route:list --name=xelqun-panels`** to see every route a panel made.
