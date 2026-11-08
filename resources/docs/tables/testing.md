---
title: Testing
description: Test a table's rows, search, filters and actions with the helpers in Xelqun Tables.
---

Add `InteractsWithTables` to your test case to reach the table's endpoints.

```php
use Xelqun\Tables\Testing\InteractsWithTables;

abstract class TestCase extends BaseTestCase
{
    use InteractsWithTables;
}
```

| Method                                               | What it does                                        |
| ---------------------------------------------------- | --------------------------------------------------- |
| `queryTable($table, $query)`                         | Fetches rows, with a search, sort, filters or page. |
| `mountTableAction($table, $name, $record)`           | Opens an action's modal.                            |
| `runTableAction($table, $name, $record, $data)`      | Runs an action on one row.                          |
| `runBulkTableAction($table, $name, $records, $data)` | Runs a bulk action on selected rows.                |

Pass the context the table reads, and log in with `actingAs()`, because an action's `authorize('update')` asks the [policy](/tables/your-first-table#things-to-know) as that user.

```php
it('hides another team\'s products', function (): void {
    $user = User::factory()->create(['team_id' => 1]);
    $mine = Product::factory()->create(['team_id' => 1]);
    Product::factory()->create(['team_id' => 2]);

    $this->actingAs($user)
        ->queryTable(ProductsTable::make(['team' => 1]))
        ->assertOk()
        ->assertJsonPath('pagination.total', 1)
        ->assertJsonPath('rows.0.key', (string) $mine->id);
});

it('archives a product', function (): void {
    $user = User::factory()->create(['team_id' => 1]);
    $product = Product::factory()->create(['team_id' => 1]);

    $this->actingAs($user)
        ->runTableAction(ProductsTable::make(['team' => 1]), 'archive', $product->id)
        ->assertOk();

    expect($product->fresh()->status)->toBe('archived');
});
```

## What a query returns

`queryTable()` returns JSON you can assert on:

| Path                        | What it holds                                              |
| --------------------------- | ---------------------------------------------------------- |
| `rows.*.key`                | The row's key, as a string.                                |
| `rows.*.cells.<name>.state` | A column's value, as the cell shows it.                    |
| `rows.*.actions`            | The record actions the row shows.                          |
| `pagination.total`          | How many rows match the search and filters.                |
| `activeFilters`             | How many filters are filtering.                            |
| `query`                     | The search, sort, filters and page, as the URL holds them. |

There's no helper for inline edits or reordering. Post to `route('xelqun.tables.update')` with the `token` from `$table->makeToken()`, the `record` key, the `column` and the `value`, or to `route('xelqun.tables.reorder')` with a `direction` of `up` or `down` in place of the column and value.
