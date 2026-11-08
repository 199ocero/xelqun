---
title: Testing
description: Test panels with the helpers in Xelqun Panels, alongside those of forms and tables.
---

Add `InteractsWithPanels` to your test case, with forms' and tables' own helpers.

```php
use Xelqun\Forms\Testing\InteractsWithForms;
use Xelqun\Panels\Testing\InteractsWithPanels;
use Xelqun\Tables\Testing\InteractsWithTables;

abstract class TestCase extends BaseTestCase
{
    use InteractsWithForms;
    use InteractsWithPanels;
    use InteractsWithTables;
}
```

| Method                      | What it does                                 |
| --------------------------- | -------------------------------------------- |
| `actingInPanel($user, $id)` | Signs in, inside a panel.                    |
| `enterPanel($id, $tenant)`  | Enters a panel, and a tenant if it has them. |

`actingInPanel()` enters the panel too, so `getUrl()` and the panel's tables work after it. Sign in as someone your `canAccessPanel()` and policy let in, such as an admin from [installation](/panels/installation#let-people-in).

```php
it('lists products', function (): void {
    $this->actingInPanel(User::factory()->create(['is_admin' => true]), 'admin');

    $this->get(ProductResource::getUrl())->assertOk();
});
```

## Testing a resource

The create page posts its form to the resource's `store` URL, under `data`. `ResourceTable::for()` builds the resource's table, for the [tables helpers](/tables/testing).

```php
use Xelqun\Panels\Resources\ResourceTable;

it('creates a product', function (): void {
    $this->actingInPanel(User::factory()->create(['is_admin' => true]), 'admin');

    $this->post(ProductResource::getUrl('store'), ['data' => ['name' => 'Keyboard', 'price' => 49]])
        ->assertRedirect();

    expect(Product::where('name', 'Keyboard')->exists())->toBeTrue();
});

it('searches and deletes products', function (): void {
    $this->actingInPanel(User::factory()->create(['is_admin' => true]), 'admin');
    $product = Product::factory()->create(['name' => 'Keyboard']);
    $table = ResourceTable::for(ProductResource::class);

    $this->queryTable($table, ['search' => 'Key'])->assertJsonPath('pagination.total', 1);
    $this->runTableAction($table, 'delete', $product->id)->assertOk();

    expect(Product::find($product->id))->toBeNull();
});
```

Build the table after `actingInPanel()`, so its token carries the panel.
