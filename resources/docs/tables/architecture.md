---
title: How tables work
description: What happens inside xelqun/tables, from the Table class to the query, the cells, filters, actions and inline edits.
---

## The big picture

A table is a PHP class that says what to query and how to show it. The first page load sends the table and its first page of rows as an Inertia prop. After that, the table fetches its own rows from a JSON endpoint, without reloading the page.

```text
First visit (Inertia)                     Every search, sort, filter or page
─────────────────────                     ──────────────────────────────────
Controller                                Browser
  ProductsTable::make()->toSchema()         POST /_xelqun/tables/query
    columns, filters, actions                 { token, query: { search, sort, filters, page } }
    first page of rows                      Server
    encrypted token                           rebuild table from token
  ─────────▶ <XqTable :table="table">         clean the query, build the Eloquent query
                                              return rows, pagination, summaries
                                            Browser
                                              replace rows, update the URL
```

Here's a sort, step by step:

<!-- flow: tables-query -->

If you haven't read [How Xelqun works](/introduction/architecture) yet, start there. It explains the schema and the token, which tables reuse from forms.

## Where the code lives

| Folder                     | What's in it                                                                   |
| -------------------------- | ------------------------------------------------------------------------------ |
| `src/Table.php`            | The base class. It holds the definition, builds the query and serializes rows. |
| `src/Columns/`             | The `Column` base class and every column type.                                 |
| `src/Filters/`             | Filters, the filter form, and the query builder's constraints.                 |
| `src/Actions/`             | `TableActionHost`, which plugs forms actions into a table, and `BulkAction`.   |
| `src/State/TableState.php` | The cleaned-up search, sort, filters and page of one request.                  |
| `src/Security/`            | Rebuilds a table from its token.                                               |
| `src/Summarizers/`         | Sum, average, count and range.                                                 |
| `src/Http/Controllers/`    | One controller per endpoint: query, update, reorder and action.                |
| `resources/js/core/`       | The browser-side store and the URL helpers.                                    |
| `resources/js/components/` | `XqTable` and its parts, and one cell component per column type.               |

## Follow the code

Here's one table's whole trip, in the package's own code. Each excerpt is cut down to the lines that matter, and `// ...` marks what was left out. The file path above each one is inside `vendor/xelqun/tables` or `node_modules/@xelqun/tables`.

### 1. Your controller makes the schema

```php
// app/Http/Controllers/ProductController.php (your app)
public function index(): Response
{
    return Inertia::render('Products/Index', [
        'products' => ProductsTable::make()->toSchema(),
    ]);
}
```

### 2. `toSchema()` adds the parts that never change

It checks `authorize()`, reads the table's part of the query string, then adds the column definitions, filters and token. The rows come from `toResult()`, in the next step.

```php
// src/Table.php
public function toSchema(): array
{
    abort_unless($this->authorize(auth()->user()), 403);

    $input = request()->query($this->getId());
    $result = $this->toResult(TableState::from(is_array($input) ? $input : [], $this));

    return [
        '$xq' => SchemaVersion::CURRENT,
        'id' => $this->getId(),
        'token' => $this->makeToken(),
        'endpoints' => [
            'query' => route('xelqun.tables.query', absolute: false),
            // ... update, reorder and action
        ],
        'columns' => array_map(fn (Column $column): array => $column->toDefinition(), $this->getColumns()),
        'filters' => $this->getFilters() === [] ? null : $this->getFiltersForm()->toSchema(),
        // ... header actions, layout, empty state
        ...$result,
    ];
}
```

Notice that `filters` is a whole form schema. The filter panel is drawn by `XqForm`, like any other form.

### 3. `toResult()` runs the query and makes the rows

This is the part that changes with every search, sort and page. It's also exactly what the query endpoint returns later.

```php
// src/Table.php
public function toResult(TableState $state): array
{
    $columns = $this->getColumns();
    $query = $this->buildQuery($state); // your query() + eager loads, search, filters, sort
    $total = $query->toBase()->getCountForPagination();

    // Counted first, because deleting the last row of the last page leaves the viewer past the end.
    $state = $state->withPage(min($state->page, max((int) ceil($total / $state->perPage), 1)));
    $records = $query->paginate($state->perPage, page: $state->page, total: $total);

    return [
        'query' => (object) $state->toQuery($this),
        'rows' => $records->getCollection()->map(fn (Model $record): array => [
            'key' => (string) $record->getKey(),
            'cells' => collect($columns)
                ->mapWithKeys(fn (Column $column): array => [$column->getName() => $column->toCell($record)])
                ->all(),
            // ... url, group and record actions
        ])->all(),
        'pagination' => [
            'page' => $records->currentPage(),
            'total' => $records->total(),
            // ... perPage, lastPage, options
        ],
        // ... sort, bulk actions, summaries
    ];
}
```

### 4. Each column makes its cell

`toCell()` gets the value, formats it, and adds the extras. A closure you gave to `formatStateUsing()` or `color()` runs here.

```php
// src/Columns/Column.php
public function toCell(Model $record): array
{
    $state = $this->reduceToKeys($this->getState($record));

    return [
        'state' => is_array($state)
            ? array_map(fn (mixed $value): mixed => $this->formatValue($value, $record), $state)
            : $this->formatValue($state, $record),
        ...array_filter([
            'color' => $state === null ? null : $this->evaluateForCell($this->color, $record, $state) ?? $this->fromEnum($state, 'getColor'),
            'icon' => $state === null ? null : $this->evaluateForCell($this->icon, $record, $state) ?? $this->fromEnum($state, 'getIcon'),
            'url' => SafeUrl::filter($this->evaluateForCell($this->url, $record, $state)),
            ...$this->getCellProps($record, $state),
        ], fn (mixed $value): bool => $value !== null),
    ];
}
```

So one row in the JSON looks like `{ "key": "5", "cells": { "name": { "state": "Blue shirt" }, "status": { "state": "Active", "color": "success" } } }`.

### 5. Your Vue page renders `XqTable`

```vue
<!-- resources/js/pages/Products/Index.vue (your app) -->
<script setup lang="ts">
import { XqTable } from '@xelqun/tables';
import type { TableSchema } from '@xelqun/tables';

defineProps<{ products: TableSchema }>();
</script>

<template>
    <XqTable :table="products" />
</template>
```

### 6. `XqTable` makes a store and picks a cell for each column

The store holds the schema. Each column's `type` is looked up in the forms registry first, then in the built-in cells.

```ts
// resources/js/components/XqTable.vue
const registry = useRegistry();
const store = createTableStore(props.table, { onChange: syncUrl });
const { schema } = store;

const columns = computed(() =>
    schema.value.columns
        .filter((column) => !hiddenColumns.value.has(column.name))
        .map((column) => ({
            ...column,
            cell: registry?.get(column.type) ?? builtInCells[column.type],
        })),
);
```

```ts
// resources/js/components/cells.ts
export const builtInCells: Record<string, Component> = {
    'text-column': XqTextCell,
    'icon-column': XqIconCell,
    'toggle-column': XqToggleCell,
    // ... every other column type
};
```

### 7. Each row draws its cells

`XqTableRow` renders the cell component for each column, and passes it the column and that row's cell.

```vue
<!-- resources/js/components/XqTableRow.vue -->
<component
    :is="column.cell"
    :column="column"
    :cell="row.cells[column.name]"
    @update="store.updateCell(row.key, column.name, $event)"
/>
```

The cell component just shows `cell.state`:

```vue
<!-- resources/js/components/cells/XqTextCell.vue -->
<script setup lang="ts">
const props = defineProps<{ column: TableColumn; cell: TableCell }>();

const values = computed(() =>
    (Array.isArray(props.cell.state)
        ? props.cell.state
        : [props.cell.state]
    ).filter((value) => value !== null && value !== undefined),
);
</script>

<template>
    <span
        :class="cn('inline-flex items-center gap-1.5', textTones[cell.color])"
    >
        <XqIcon v-if="cell.icon" :name="cell.icon" class="size-3.5" />
        {{ values.join(', ') }}
    </span>
</template>
```

### 8. A search or sort posts to the query endpoint

When the user types in the search box or clicks a header, the store posts the new query with the token. A newer request cancels the older one, and the answer is merged into the schema.

```ts
// resources/js/core/store.ts
async function load(query: TableQuery): Promise<void> {
    request?.abort();
    request = new AbortController();

    const mine = ++sequence;

    const result = await postJson<TableResult>(
        schema.value.endpoints.query,
        { token: schema.value.token, query },
        request.signal,
    );

    if (mine !== sequence) {
        return; // a newer request was sent, so this answer is old
    }

    schema.value = { ...schema.value, ...result };
    options.onChange?.(result.query); // XqTable writes it to the URL
    // ... loading and error handling
}
```

### 9. The server rebuilds the table and answers

The query endpoint rebuilds the table from its token, cleans up the query, and calls the same `toResult()` as step 3.

```php
// src/Http/Controllers/QueryController.php
public function __invoke(Request $request, TableTokenEncrypter $tokens): JsonResponse
{
    $validated = $request->validate([
        'token' => ['required', 'string'],
        'query' => ['present', 'array'],
        'keys' => ['sometimes', 'boolean'],
    ]);

    $table = $tokens->resolveTable($validated['token']);
    $state = TableState::from($validated['query'], $table);

    return response()->json($table->toResult($state));
    // ... with "keys": true it returns every matching key, for "select all"
}

// src/Security/TableTokenEncrypter.php
public function resolveTable(string $value): Table
{
    $token = $this->tokens->decrypt($value);

    if (! is_subclass_of($token->formClass, Table::class)) {
        throw InvalidFormToken::make('the token does not describe a table');
    }

    $this->tokens->assertUsable($token, (int) config('xelqun-tables.token_ttl', 720));

    // ... read the class and context from the token
    $table = new $tableClass(is_array($context) ? $context : []);

    if (! $table->authorize(auth()->user())) {
        throw InvalidFormToken::make('this table is unauthorized');
    }

    return $table;
}
```

Because both the first render and every later request go through `toResult()`, the rows always look the same, whichever way they arrived.

The sections below go through each of these parts in more detail.

## The Table class

You extend `Table` and fill in a few methods. Only `query()` and `columns()` are required.

| Method            | What it gives                                  |
| ----------------- | ---------------------------------------------- |
| `query()`         | The Eloquent query the rows come from.         |
| `columns()`       | The columns.                                   |
| `filters()`       | The filters.                                   |
| `recordActions()` | Buttons on each row.                           |
| `headerActions()` | Buttons above the table.                       |
| `bulkActions()`   | Buttons for the selected rows.                 |
| `authorize()`     | Whether this user may see the table at all.    |
| `boot()`          | Settings such as `defaultSort()` and `poll()`. |

The constructor is `final` and only takes a `$context` array. That's on purpose. Every later request rebuilds the table from its class and that context alone, and route parameters aren't there anymore. If your table needs a value, such as a category ID, pass it in the context.

When the table is built, it checks its columns right away. A duplicate column name, or a column marked `sortable()` that can't be sorted, throws while the page renders, not later when someone clicks it.

## From PHP to JSON

`toSchema()` checks `authorize()`, reads the table's part of the query string, and returns everything the browser needs:

```json
{
    "$xq": 1,
    "id": "products-table",
    "token": "eyJpdiI6...",
    "endpoints": {
        "query": "...",
        "update": "...",
        "reorder": "...",
        "action": "..."
    },
    "columns": [
        {
            "name": "name",
            "type": "text-column",
            "label": "Name",
            "sortable": true,
            "props": {}
        }
    ],
    "filters": { "components": [] },
    "query": { "sort": "-created_at" },
    "rows": [
        {
            "key": 5,
            "url": "/admin/products/5/edit",
            "cells": { "name": { "state": "Blue shirt" } },
            "actions": []
        }
    ],
    "pagination": { "page": 1, "perPage": 10, "total": 42, "lastPage": 5 }
}
```

The query string is namespaced by the table's `id`, as in `?products-table[page]=2`. That's why two tables on one page don't fight over `page`.

## Building the query

Each request's search, sort and filters first go through `TableState::from()`, which cleans them up. A sort must name a sortable column, the page size must be one of the options, a search is trimmed and capped at 200 characters, and filter values must pass the filter's own field rules. A bad value falls back to its default, with no error.

Then `buildQuery()` builds the query in this order:

1. **Your `query()`.**
2. **Eager loading.** Each column named after a relation, such as `category.name`, adds `category` to `with()`, so there's no N+1.
3. **Search.** Every searchable column adds its condition inside one group. A search on `category.name` uses `whereHas`.
4. **Filters.** Each active filter adds its conditions inside its own group.
5. **Sort.** Any order from your `query()` is cleared, then the column's sort is applied. Sorting by a BelongsTo or HasOne column uses a subquery.
6. **Grouping.** If rows are grouped, the group's order comes first.
7. **A tiebreak** on the primary key, so pages never skip or repeat a row.

Search and filters go in as global scopes. That keeps an `orWhere` in your `query()` from leaking into them, or theirs into yours.

## From a record to a cell

`Column::toCell()` turns one record into one cell:

1. **Get the value.** With `state()`, your closure gives it. Otherwise the column's name is read as a path, so `tags.name` gives a list of tag names.
2. **Never send a whole model.** A related model is turned into its key.
3. **Format it.** `formatStateUsing()` runs on each value. `money()` and `date()` are shortcuts for it. An enum becomes its label.
4. **Add extras.** Colors, icons and links come from closures, or from the enum's `getColor()` and `getIcon()`. Links are checked, so only relative, `http`, `https`, `mailto` and `tel` links are kept.

> [!WARNING]
> A column with a `state()` closure gets no eager loading, because the table can't tell which relation the closure reads. Add the relation to `with()` in your `query()`.

## Filters

Filters are made of form fields. A `Filter` is a toggle, a `SelectFilter` is a select, and the query builder is a repeater of rules.

All the filters together make one `Form`, called `TableFiltersForm`. That's why a filter can have live, dependent and searchable fields: they go through the forms endpoints, like any other form. The filter form's `authorize()` asks the table's, so a user who can't see the table can't use its filters either.

The filter values live in the URL, as in `?products-table[filters][status]=active`. Only values that differ from the defaults are written, so a shared link shows the same rows.

## Actions

Table actions are forms actions. `TableActionHost` is an `ActionSet`, which is a `Form`, so a table action gets the same modals, validation and uploads as a form action.

When an action runs, the browser posts to `/_xelqun/tables/action`:

1. **Rebuild the table** from its token, and check `authorize()`.
2. **Find the record** with `resolveRecord()`, which goes through your `query()`. A record your query doesn't return can't be acted on.
3. **Find the action by name.** An action the table doesn't declare is a 404.
4. **Check `canRun()`**, which includes the action's own `authorize()`.
5. **Open or run.** The `mount` phase returns the modal's schema. The `run` phase calls your closure in a database transaction, and returns notifications and an optional redirect.

### Bulk actions

The selected keys are sent with the request, and the server loads them again through your `query()`. A key that isn't in the query is skipped, and if none are left, it's a 404. The selection is capped at `maxSelectableRecords`.

A `BulkAction` closure can ask for `$records`, a collection, or `$query`, a builder, so a large selection doesn't have to load into memory. A policy ability given as a string is checked on every selected record.

## Inline editing

Toggle, text input, select and checkbox columns save as soon as you change them, through `/_xelqun/tables/update`:

1. Rebuild the table, find the record and the column.
2. Check `isEditable()`. **Editing is off until you allow it**, with the column's `authorize()`, given a policy ability or a closure.
3. Validate the value with the column type's rules, plus your `rules()`. A toggle must be a boolean, a select must be one of its options.
4. Save it with your `updateStateUsing()` closure, or set the attribute and save the model.
5. Send back the new cell.

A column named after a relation, such as `category.name`, needs `updateStateUsing()`, because the table can't guess how to save it.

## Summaries, grouping, reordering and polling

- **Summaries** run every aggregate of a column in one `SELECT` over the filtered query, then format the results like cells.
- **Grouping** sorts by the group column first, and gives each row a `group` value. The browser draws a heading wherever it changes.
- **Reordering** swaps a row's position with its neighbour's in a transaction, through `/_xelqun/tables/reorder`.
- **Polling** reloads the rows every few seconds. It skips a turn while the tab is hidden, a request is running, a modal is open, or someone is typing in the table.

## In the browser

`<XqTable :table="table">` creates a store with `createTableStore()`. The store keeps the schema, the selected keys and the last query.

- **Each change** merges into the last query and posts to the query endpoint. A newer request cancels an older one, and a late answer is thrown away.
- **The URL** is updated after each answer with Inertia's `router.replace()`, so Back and shared links work. If the URL doesn't match the rows, for example after pressing Back, the table loads again from the URL.
- **Cells** come from the forms component registry first, then the built-in cells. That's why a [custom column](/tables/columns/custom) is registered the same way as a custom field.
- **Hidden columns** are remembered in the browser's `localStorage`.
- **The selection** is cleared when the search or filters change, so you never act on rows you can't see.

## What tables takes from forms

| From forms                                | Used for                                           |
| ----------------------------------------- | -------------------------------------------------- |
| `Form`                                    | The filter form.                                   |
| `ActionSet`, `Action`, `ActionGroup`      | Record, header and bulk actions, and their modals. |
| `FormToken`, `FormTokenEncrypter`         | The table's token, and its scope hook.             |
| Fields, `When` and `Get`                  | The fields inside filters and the query builder.   |
| `NotificationQueue`                       | Notifications from actions.                        |
| `XqForm`, `XqAction`, the registry and UI | The Vue side of filters, actions and cells.        |

## Debugging tips

- **A sort or search does nothing.** Look at the query request in the network tab. The server drops a sort on a column that isn't sortable, without an error.
- **The rows are wrong after a filter.** Check `query()` for an `orWhere` that isn't in a group. Search and filters are kept apart from it, but your own conditions can still combine in ways you didn't expect.
- **Too many queries.** Look for columns with a `state()` closure that reads a relation.
- **An inline edit is refused.** Check the column's `authorize()`, and the policy ability it names.
