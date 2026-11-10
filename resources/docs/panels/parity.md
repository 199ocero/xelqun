---
title: Parity with Filament
description: How Xelqun compares with Filament, feature by feature.
---

Xelqun follows [Filament](https://filamentphp.com), for Inertia and Vue. This compares the two: **Yes** means it's here, **Partly** says what's missing, and **No** means it isn't here yet.

## Resources

| Filament                              | Xelqun | Notes                                                                                                                                                                       |
| ------------------------------------- | ------ | --------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| List, create, edit, view              | Yes    | One class, with no page files. "Create & create another".                                                                                                                   |
| Deleting and soft deletes             | Yes    |                                                                                                                                                                             |
| Managing relationships                | Yes    | Has-many, many-to-many, attach, detach, associate and dissociate. Tabs or stacked, `RelationGroup`, badges, lazy loading, and `ManageRelatedRecords` for a page of its own. |
| Nested, singular and simple resources | Yes    | `ManageSingleRecord` and `ManageRecords`.                                                                                                                                   |
| Global search                         | Yes    | Result details and actions, and `$globalSearchResultsLimit`.                                                                                                                |
| Widgets on resource pages             | Yes    | On lists, and on record pages with the record.                                                                                                                              |
| Custom resource pages                 | Yes    | Any key in `getPages()` gets a route, at `$routePath` or its key.                                                                                                           |

## Tables

| Filament                             | Xelqun | Notes                                                                                                                                                           |
| ------------------------------------ | ------ | --------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| All column types, custom columns     | Yes    | Descriptions, placeholders, limits, line clamps, copy, lists, numeric, since and time zones. Editable columns are read-only until `authorize()` says otherwise. |
| Select, ternary and custom filters   | Yes    | Removable chips for active filters, live filters, and ternary labels, `nullable()` and `queries()`.                                                             |
| Query builder                        | Yes    | Groups of rules, nested up to `maxNestingDepth()`, and `maxRules()`.                                                                                            |
| Filter layout                        | Partly | Filters open in one panel, with chips for the active ones. No layout above the table.                                                                           |
| Actions and bulk actions             | Yes    | `authorizeIndividualRecords()`, selectable rows and `recordAction()`.                                                                                           |
| Summaries                            | Partly | Sum, average, count and range, for all rows and for each group. No custom scopes.                                                                               |
| Grouping rows                        | Yes    | `groups()` the viewer picks from, collapsible.                                                                                                                  |
| Layout                               | Partly | `contentGrid()`, `striped()`. No `Split` or `Stack` inside a row.                                                                                               |
| Reordering rows                      | Partly | Up and down buttons, not drag and drop.                                                                                                                         |
| Column toggles, polling, empty state | Yes    | An empty state takes an icon and actions.                                                                                                                       |
| Custom data                          | Yes    | `records()`, with each row an `ArrayRecord`. Summaries, the query builder and groups need a query.                                                              |

## Forms, infolists and actions

| Filament                           | Xelqun | Notes                                                                                                                                      |
| ---------------------------------- | ------ | ------------------------------------------------------------------------------------------------------------------------------------------ |
| Every field, layout and validation | Yes    | `ModalTableSelect` comes with `xelqun/tables`. Section icons, aside and remembered collapse. Tab icons, badges and `persistTab()`.         |
| Prime components                   | Yes    | `Text`, `Icon`, `Image`, and more.                                                                                                         |
| Unsaved changes alerts             | Yes    | `unsavedChangesAlerts()`.                                                                                                                  |
| All entries, custom entries        | Yes    | With the text column's options, plus `hint()`, `inlineLabel()`, `html()`, `markdown()`, `prefix()` and `suffix()`.                         |
| Modals, grouping, CRUD actions     | Yes    | Slide-overs, nested modals, wizard steps, `halt()` and `cancel()`. Replicate, restore and force delete. Saves always run in a transaction. |
| Import                             | Partly | CSV with `ImportColumn` header guesses and rules, and `queue()`. No column picker or failed-rows file.                                     |
| Export                             | Partly | CSV of the list and tab. No queue, XLSX, or table filters.                                                                                 |

## Notifications and widgets

| Filament                                   | Xelqun | Notes                                                                                             |
| ------------------------------------------ | ------ | ------------------------------------------------------------------------------------------------- |
| Flash and database notifications           | Yes    | The bell shows icons and link actions. Actions that run code stay in the toast.                   |
| Broadcast notifications                    | Yes    | Needs Laravel Echo in the app.                                                                    |
| Stats, table and custom widgets            | Yes    |                                                                                                   |
| Dashboard filters, footer widgets, polling | Yes    | `filters()` on a page, read as `$this->pageFilters`. `getFooterWidgets()` and `$pollingInterval`. |
| Chart widgets                              | Partly | Line, bar and doughnut. No filter on a chart, and no radar, scatter or bubble.                    |

## Panels

| Filament                                      | Xelqun | Notes                                                                                              |
| --------------------------------------------- | ------ | -------------------------------------------------------------------------------------------------- |
| Panel configuration                           | Yes    | Brand, logo, favicon, colors, font, width, dark mode, `middleware()` and `authMiddleware()`.       |
| Navigation, user menu, top and sub navigation | Yes    | Group icons and folding, child items, badge colors and tooltips.                                   |
| Domains                                       | Yes    | `domain()`.                                                                                        |
| Clusters                                      | Yes    | Members' URLs start with the cluster's slug.                                                       |
| Authentication flows                          | Yes    | Email change verification included.                                                                |
| Multi-factor authentication                   | Partly | App, email and recovery codes. App setup has no QR code.                                           |
| Multi-tenancy                                 | Partly | Tenant middleware, menu items, a route prefix or a domain. No tenant billing.                      |
| Colors and icons                              | Yes    | The same color names and defaults, apart from a blue primary. Lucide, by name, not Heroicons.      |
| CSS hooks                                     | Yes    | Documented `--xq-*` tokens and `data-xq-*` attributes. See [CSS hooks](/panels/theming/css-hooks). |
| Render hooks                                  | Partly | Eight places, for Vue components.                                                                  |
| Plugins                                       | Yes    |                                                                                                    |
| Testing helpers                               | Partly | `InteractsWithPanels`, with forms' and tables' own helpers.                                        |

## Where Xelqun goes further

- Access is denied by default. With no policy, nobody gets in.
- Resources need no generated page files.
- Charts need no JavaScript library.
- The admin area keeps its layout between pages, with no reloads.
- Motion is built in, from one set of tokens, with a switch in the user menu.
