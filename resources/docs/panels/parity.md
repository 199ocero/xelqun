---
title: Parity with Filament
description: How Xelqun compares with Filament, feature by feature.
---

Xelqun follows [Filament](https://filamentphp.com), for Inertia and Vue. This compares the two: **Yes** means it's here, **Partly** says what's missing, and **No** means it isn't here yet.

## Resources

| Filament                              | Xelqun | Notes                                                             |
| ------------------------------------- | ------ | ----------------------------------------------------------------- |
| List, create, edit, view              | Yes    | One class, with no page files.                                    |
| Deleting and soft deletes             | Yes    |                                                                   |
| Managing relationships                | Yes    | Has-many, many-to-many, attach, detach, associate and dissociate. |
| Nested, singular and simple resources | Yes    |                                                                   |
| Global search                         | Yes    |                                                                   |
| Widgets on resource pages             | Yes    |                                                                   |
| Custom resource pages                 | Yes    |                                                                   |

## Tables

| Filament                             | Xelqun | Notes                                                               |
| ------------------------------------ | ------ | ------------------------------------------------------------------- |
| All column types, custom columns     | Yes    | Editable columns are read-only until `authorize()` says otherwise.  |
| Select, ternary and custom filters   | Yes    |                                                                     |
| Query builder                        | Partly | Rules match all or any. No nested groups.                           |
| Filter layout                        | Partly | Filters open in one panel.                                          |
| Actions and bulk actions             | Yes    |                                                                     |
| Summaries                            | Partly | Sum, average, count and range. No custom scopes or per-group lines. |
| Grouping rows                        | Partly | A default group in code. The viewer can't choose one.               |
| Layout                               | Partly | `contentGrid()`, `striped()`. No `Split` or `Stack` inside a row.   |
| Reordering rows                      | Partly | Up and down buttons, not drag and drop.                             |
| Column toggles, polling, empty state | Yes    |                                                                     |
| Custom data                          | No     | A table needs an Eloquent query.                                    |

## Forms, infolists and actions

| Filament                           | Xelqun | Notes                                                             |
| ---------------------------------- | ------ | ----------------------------------------------------------------- |
| Every field, layout and validation | Yes    |                                                                   |
| Unsaved changes alerts             | Yes    | `unsavedChangesAlerts()`.                                         |
| All entries, custom entries        | Yes    |                                                                   |
| Modals, grouping, CRUD actions     | Yes    |                                                                   |
| Import                             | Partly | CSV, up to 1,000 rows in the request. No queue or column mapping. |
| Export                             | Partly | CSV of the list and tab. No queue, XLSX, or table filters.        |

## Notifications and widgets

| Filament                         | Xelqun | Notes                                                               |
| -------------------------------- | ------ | ------------------------------------------------------------------- |
| Flash and database notifications | Yes    |                                                                     |
| Broadcast notifications          | Yes    | Needs Laravel Echo in the app.                                      |
| Stats, table and custom widgets  | Yes    |                                                                     |
| Chart widgets                    | Partly | Line, bar and doughnut. No filter, and no radar, scatter or bubble. |

## Panels

| Filament                                   | Xelqun | Notes                                                    |
| ------------------------------------------ | ------ | -------------------------------------------------------- |
| Panel configuration, navigation, user menu | Yes    |                                                          |
| Domains                                    | Yes    | `domain()`.                                              |
| Clusters                                   | Partly | Members keep their own URLs, with no shared prefix.      |
| Authentication flows                       | Yes    |                                                          |
| Multi-factor authentication                | Partly | App, email and recovery codes. App setup has no QR code. |
| Multi-tenancy                              | Partly | Everything but tenant billing.                           |
| Colors and icons                           | Yes    | Lucide, by name, not Heroicons.                          |
| CSS hooks                                  | Partly | `data-xq-*` attributes and `--xq-*` tokens.              |
| Render hooks                               | Partly | Eight places, for Vue components.                        |
| Plugins                                    | Yes    |                                                          |

## Where Xelqun goes further

- Access is denied by default. With no policy, nobody gets in.
- Resources need no generated page files.
- Charts need no JavaScript library.
- The admin area keeps its layout between pages, with no reloads.
- Motion is built in, from one set of tokens, with a switch in the user menu.
