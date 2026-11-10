---
title: Custom data
description: Show rows that don't come from Eloquent, such as an API's, with search, sorting, filters and pages.
---

Call `records()` in `boot()` instead of writing `query()`. The closure returns the rows, keyed by a key that stays the same for each row.

```php
use Illuminate\Support\Facades\Http;
use Illuminate\Pagination\LengthAwarePaginator;

protected function boot(): void
{
    $this->records(function (?string $search, ?string $sortColumn, ?string $sortDirection, int $page, ?int $recordsPerPage) {
        $response = Http::get('https://dummyjson.com/products/search', [
            'q' => $search ?? '',
            'sortBy' => $sortColumn,
            'order' => $sortDirection,
            'limit' => $recordsPerPage ?? 0,
            'skip' => ($page - 1) * ($recordsPerPage ?? 0),
        ]);

        return new LengthAwarePaginator(
            $response->collect('products')->keyBy('id'),
            $response->json('total'),
            $recordsPerPage ?? $response->json('total'),
            $page,
        );
    });
}

public function columns(): array
{
    return [
        TextColumn::make('title')->searchable()->sortable(),
        TextColumn::make('price')->money('USD'),
    ];
}
```

## What the closure gets

It asks for any of these by name:

| Name              | Holds                                                  |
| ----------------- | ------------------------------------------------------ |
| `$search`         | The search box, or null                                |
| `$columnSearches` | Each column's own search box, by column name           |
| `$filters`        | The active filters' values, by filter name             |
| `$sortColumn`     | The column to sort by, or null                         |
| `$sortDirection`  | `asc` or `desc`, or null                               |
| `$page`           | The page, from 1                                       |
| `$recordsPerPage` | The page size, or null when the viewer shows every row |

Return an array or a collection of every row, and the table pages it for you. Return a paginator, and it's used as one page, as given. The table doesn't sort, search or filter the rows itself: the closure does.

## Rows in columns and actions

Each row reaches columns and actions as an `ArrayRecord`. Read it like the array you returned, as `$record['title']` or `$record->title`, and get the array back with `toArray()`. It can't be saved or deleted, so write changes through your own API in the action.

```php
Action::make('restock')
    ->authorize(fn (): bool => auth()->user()->isAdmin())
    ->action(fn (ArrayRecord $record) => Http::post("https://example.com/products/{$record->getKey()}/restock"));
```

An action finds its row again by key. Without anything more, `records()` runs again with no search or filters and every row on one page. For a big API, look the rows up yourself:

```php
$this->resolveSelectedRecordsUsing(fn (array $keys): array => Http::get('https://example.com/products', ['ids' => $keys])->collect()->keyBy('id')->all());
```

## Things to know

- **Nothing scopes the rows for you.** Return only what the viewer may see, whatever the search, filters or keys say. Keys come from the browser.
- **Authorize actions with a closure.** No policy can exist for an `ArrayRecord`, so a string `authorize()` ability denies, unless a gate of that name allows it.
- **What needs a database query** doesn't work here: summaries, counts and sums of related rows, inline editing, the query builder, relationship filters, groups, reordering and cursor pagination. A table that uses one fails while the page renders, and the message names it.
