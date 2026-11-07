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

```php
it('lists products', function (): void {
    $this->actingInPanel(User::factory()->create(), 'admin');

    $this->get(ProductResource::getUrl())->assertOk();
});
```
