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

| Method                               | What it does                                    |
| ------------------------------------ | ----------------------------------------------- |
| `updateForm($form, $changed, $data)` | Sends a live update for the paths that changed. |
| `searchForm($form, $path, $search)`  | Runs a field's search.                          |
| `uploadToForm($form, $path, $file)`  | Uploads a file to a field.                      |
| `formNode($form, $key)`              | Returns one node of the schema.                 |

A path is a field's name, as in `country_id`, or its full path, as in `data.country_id`. `$data` defaults to the form's own state, and `updateForm()` sends the dependencies from the form's schema, as the browser does.

The response holds `set`, the values the server changed, and `schema`, the nodes it sent again. Both are keyed by full path.

```php
it('loads the states of a country', function (): void {
    $japan = Country::factory()->create();
    $tokyo = State::factory()->for($japan)->create(['name' => 'Tokyo']);

    $response = $this->updateForm(AddressForm::make(), ['country_id'], [
        'country_id' => $japan->id,
        'state_id' => 5,
    ]);

    $response->assertOk()->assertJsonPath('set', ['data.state_id' => null]);

    expect($response->json('schema')['data.state_id']['props']['options'])
        ->toBe([['value' => $tokyo->id, 'label' => 'Tokyo']]);
});
```

## Testing a Vue field

`@xelqun/forms/testing` builds a schema by hand, so you can mount `XqForm` with no server.

```ts
import { fieldNode, formSchema } from '@xelqun/forms/testing';

const form = formSchema([fieldNode('rating', 'acme/rating')]);
```
