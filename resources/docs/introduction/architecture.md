---
title: How Xelqun works
description: The big picture behind forms, tables and panels. A PHP class becomes JSON, Vue draws it, and every request rebuilds the class on the server from an encrypted token.
---

## The short version

All of Xelqun rests on three ideas. Once you know them, every package reads the same way.

1. **A PHP class becomes JSON.** Your form, table or panel page is a PHP object. Xelqun turns it into a plain array, called the _schema_, and sends it to the page as an Inertia prop.
2. **Vue draws the JSON.** Generic Vue components read the schema and render it. There's one Vue component per field type, column type or widget type, not one per form.
3. **The server rebuilds the class on every request.** The schema carries an encrypted _token_ that names your PHP class. When the browser needs something, it sends the token back. The server decrypts it, builds your class again from scratch, checks permissions, and answers.

The browser never gets your PHP code or closures. It only gets the result of running them.

Here's the whole loop for one form. It plays by itself, and you can pause it or step through with the arrows.

<!-- flow: overview -->

## Why it's built this way

**No state on the server.** Nothing is kept between requests: no session copy of the form, no component cache. Each request rebuilds what it needs from the token and the posted values. That's why it works with queues, several servers and Octane.

**The browser is never trusted.** The browser can hide a field, add up a total or show a button, so the page feels quick. The server works all of it out again before saving anything. A value for a hidden, disabled or unknown field is thrown away.

**Your closures stay private.** A closure like `options(fn () => ...)` only ever runs on the server. The browser gets the options it returned.

## The token

The token is what lets a later request find your class again. It's made by `Form::toToken()` and encrypted with your app key, using Laravel's encrypter, so it can't be read or changed. It holds:

| Field     | What it's for                                                            |
| --------- | ------------------------------------------------------------------------ |
| Class     | Which PHP class to rebuild, such as `App\Forms\OrderForm`.               |
| Record    | The key of the record being edited, if there is one.                     |
| User      | The ID of the user it was made for. Another user can't use it.           |
| Context   | Extra values the class needs, such as which resource or tab it's for.    |
| Issued at | When it was made. It expires after `token_ttl` minutes (720 by default). |
| Scope     | An optional value another package adds, such as the panel and tenant.    |

When a token comes back, `FormTokenEncrypter::resolveForm()` does this, in order:

1. Decrypts it. A token that was changed fails here.
2. Checks it names a real form class.
3. Checks it hasn't expired (419) and belongs to the signed-in user (403).
4. Loads the record again with `resolveRecord()`, so a deleted record is a 404.
5. Calls your `authorize()` again, so a lost permission takes effect at once.
6. Fills the form, ready for the request.

Tables have their own token in the same format, and panels add a scope to it. More on that below.

## How the packages fit together

Each package builds on the one before it. Tables and panels don't have their own way of doing forms. They reuse the forms package.

```text
┌───────────────────────────────────────────────────────────────┐
│ xelqun/panels                                                 │
│ Panel, routes, sign in, Resource, Page, widgets, tenancy      │
│                                                               │
│   ResourceForm extends Form        ResourceTable extends Table│
└───────────────┬───────────────────────────────┬───────────────┘
                │                               │
                │               ┌───────────────▼───────────────┐
                │               │ xelqun/tables                 │
                │               │ Table, columns, filters,      │
                │               │ query, inline edits           │
                │               │                               │
                │               │ Filters form extends Form     │
                │               │ Table actions use ActionSet   │
                │               └───────────────┬───────────────┘
┌───────────────▼───────────────────────────────▼───────────────┐
│ xelqun/forms                                                  │
│ Form, fields, layouts, validation, saving, uploads, token     │
│ Xelqun\Actions (buttons and modals)                           │
│ Xelqun\Notifications (toasts)                                 │
└───────────────────────────────────────────────────────────────┘
```

Some examples of that reuse:

- A **table filter** is made of form fields, and the filter panel is a `Form`.
- A **table action** is a forms `Action`, so its modal is a `Form` with validation and uploads.
- A **panel's edit page** is a `Form` called `ResourceForm`, and its list page is a `Table` called `ResourceTable`.
- The **panel's login page** is a `Form` too.

The same goes for Vue. `@xelqun/tables` draws its filters with `XqForm`, and `@xelqun/panels` draws a page with `XqForm` and `XqTable`. They all share one _component registry_, the list that maps a type such as `select` to a Vue component. That's why a custom component you register once works in forms, tables and panels.

## The endpoints

The first page load is a normal Inertia visit. Everything after that goes to a small set of JSON endpoints, which every app shares.

| Endpoint                       | Package | What it does                                           |
| ------------------------------ | ------- | ------------------------------------------------------ |
| `POST /_xelqun/forms/update`   | Forms   | A live field changed. Returns what else should change. |
| `POST /_xelqun/forms/search`   | Forms   | Searches the options of a select.                      |
| `POST /_xelqun/forms/upload`   | Forms   | Stores a file in temporary storage.                    |
| `POST /_xelqun/forms/validate` | Forms   | Checks one wizard step before moving on.               |
| `POST /_xelqun/forms/action`   | Forms   | Opens an action's modal, or runs the action.           |
| `POST /_xelqun/tables/query`   | Tables  | Gets rows after a search, sort, filter or page change. |
| `POST /_xelqun/tables/update`  | Tables  | Saves an inline edit, such as a toggle column.         |
| `POST /_xelqun/tables/reorder` | Tables  | Moves a row up or down.                                |
| `POST /_xelqun/tables/action`  | Tables  | Opens or runs a record, header or bulk action.         |

They all start the same way: decrypt the token, rebuild the class, check permissions. Each one is in the `web` middleware group, so it has your session and CSRF protection, and each one is rate limited.

Submitting a form is the exception. It doesn't use a package endpoint. It's an Inertia visit to your own route, where your controller calls `$form->save($request)`. In a panel, the panel's own routes do this for you.

## One request, start to finish

Here's what happens when someone edits a product in a panel. Every package takes part. To see the real code behind each step, read [Follow the code](/panels/architecture#follow-the-code) in How panels work.

1. **The browser asks for `/admin/products/5/edit`.** The panel's route sends it to the `EditRecord` page. Middleware sets up the panel and checks that the user is signed in and may enter it.
2. **The page finds the record.** It calls `ProductResource::findRecord(5)`, which goes through the resource's query. A product outside the user's tenant is a 404.
3. **The page checks the policy.** `EditRecord` asks the `update` ability of your `ProductPolicy`.
4. **The form becomes JSON.** The page builds a `ResourceForm` from your resource's `form()`, fills it from the product, and calls `toSchema()`. The schema carries a token.
5. **Inertia renders `xelqun/Panel`.** Vue draws the panel's sidebar from shared data, and the form from the schema.
6. **The user picks a country.** The state field depends on it, so the browser posts to `/_xelqun/forms/update`. The server rebuilds the form from the token, runs your `options()` closure, and sends back the new options. Only that field changes on the page.
7. **The user saves.** The browser sends an Inertia `PUT` to the panel's update route. The server rebuilds the form, restores the values of any locked fields, validates, and saves the product in a database transaction.
8. **The user is redirected** and sees a notification.

## Where to go next

Each package has a page that goes one level deeper, with the classes and files to read.

- [How forms work](/forms/architecture): fields, the token, live updates, saving and uploads.
- [How tables work](/tables/architecture): the query, columns, filters and actions.
- [How panels work](/panels/architecture): routes, resources, sign in and tenancy.
