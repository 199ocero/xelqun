---
title: Testing
description: Test a form's rules, live updates, search and uploads with the helpers in Xelqun Forms.
---

`save()` and `validate()` work in a test as they do in a controller.

```php
it('needs a customer', function (): void {
    OrderForm::make()->validate(['data' => []]);
})->throws(ValidationException::class);
```

## The helpers

Add `InteractsWithForms` to your test case to reach the form's endpoints.

```php
use Xelqun\Forms\Testing\InteractsWithForms;

abstract class TestCase extends BaseTestCase
{
    use InteractsWithForms;
}
```

| Method                              | What it does                                    |
| ----------------------------------- | ----------------------------------------------- |
| `updateForm($form, $changed)`       | Sends a live update for the paths that changed. |
| `searchForm($form, $path, $search)` | Runs a field's search.                          |
| `uploadToForm($form, $path, $file)` | Uploads a file to a field.                      |
| `formNode($form, $key)`             | Returns one node of the schema.                 |

```php
it('loads the states of a country', function (): void {
    $form = AddressForm::make(['country_id' => 1]);

    $this->updateForm($form, ['country_id'])->assertOk();
});
```

## Testing a Vue field

`@xelqun/forms/testing` builds a schema by hand, so you can mount `XqForm` with no server.

```ts
import { fieldNode, formSchema } from '@xelqun/forms/testing';

const form = formSchema([fieldNode('rating', 'acme/rating')]);
```
