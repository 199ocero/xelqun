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

```php
it('hides another team\'s products', function (): void {
    $table = ProductsTable::make(['team' => 1]);

    $this->queryTable($table)->assertOk();
});

it('archives a product', function (): void {
    $product = Product::factory()->create();

    $this->runTableAction(ProductsTable::make(), 'archive', $product->id)->assertOk();

    expect($product->fresh()->status)->toBe('archived');
});
```
