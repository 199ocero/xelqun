---
title: How forms work
description: What happens inside xelqun/forms, from a PHP field to the JSON schema, the encrypted token, live updates, validation, saving and uploads.
---

## The big picture

A form goes through four stages. Each stage has one class that does the work, so when something goes wrong you know where to look.

```text
 1. Define        2. Serialize        3. Interact            4. Submit
┌───────────┐   ┌──────────────┐   ┌───────────────────┐   ┌──────────────────┐
│ schema()  │──▶│ toSchema()   │──▶│ Vue store         │──▶│ $form->save()    │
│ fields as │   │ JSON + token │   │ live updates,     │   │ lock, validate,  │
│ PHP       │   │ Inertia prop │   │ uploads, search   │   │ save to Eloquent │
└───────────┘   └──────────────┘   └───────────────────┘   └──────────────────┘
   Form.php      Component.php      store.ts, queue.ts       FormValidator.php
                                    UpdateProcessor.php      ModelPersister.php
```

If you haven't read [How Xelqun works](/introduction/architecture) yet, start there. It explains the schema and the token, which this page builds on.

## Where the code lives

The package has three PHP namespaces and one npm package.

| Folder           | Namespace              | What's in it                                                      |
| ---------------- | ---------------------- | ----------------------------------------------------------------- |
| `src/`           | `Xelqun\Forms`         | The form engine: fields, layouts, validation, saving, the token.  |
| `actions/`       | `Xelqun\Actions`       | Buttons that run PHP, and their modals.                           |
| `notifications/` | `Xelqun\Notifications` | Toasts, and getting them to the browser.                          |
| `resources/js/`  | `@xelqun/forms`        | The Vue components and the browser-side store.                    |
| `routes/`        |                        | The five JSON endpoints under `/_xelqun/forms`.                   |
| `config/`        |                        | The route prefix, middleware, rate limits, token lifetime, disks. |

Inside `src/`, the folders you'll open most often:

| Folder                 | What it does                                                              |
| ---------------------- | ------------------------------------------------------------------------- |
| `Form.php`             | The form's whole life: make, fill, serialize, validate, save.             |
| `Components/`          | The `Component` base class, the `Field` base class, and every field.      |
| `Components/Concerns/` | Traits shared by fields, such as labels, rules and options.               |
| `Layouts/`             | Sections, grids, tabs, the wizard and groups.                             |
| `Conditions/`          | `When::eq(...)` and the other rules that run in the browser.              |
| `Expressions/`         | The parser for `computed('qty * price')`.                                 |
| `Security/`            | The token: `FormToken` and `FormTokenEncrypter`.                          |
| `Validation/`          | `FormValidator`, which locks, validates and cleans the posted state.      |
| `Eloquent/`            | `ModelPersister`, which saves the model and its relationships.            |
| `Live/`                | `UpdateProcessor`, which answers live updates with a patch.               |
| `Uploads/`             | Temporary uploads, and moving them to their final disk.                   |
| `Http/Controllers/`    | One controller per endpoint.                                              |
| `Contracts/`           | Interfaces that give a field a server feature, such as uploads or search. |

## Follow the code

Here's one form's whole trip, in the package's own code. Each excerpt is cut down to the lines that matter, and `// ...` marks what was left out. The file path above each one is inside `vendor/xelqun/forms` or `node_modules/@xelqun/forms`.

### 1. Your controller makes the schema

The form is a prop like any other. `make()` creates and fills the form, and `toSchema()` turns it into an array.

```php
// app/Http/Controllers/OrderController.php (your app)
public function edit(Order $order): Response
{
    return Inertia::render('Orders/Edit', [
        'form' => OrderForm::make($order)->toSchema(),
    ]);
}
```

### 2. `toSchema()` builds the JSON

It asks each top-level component for its node, makes the token, and adds the endpoints and the values.

```php
// src/Form.php
public function toSchema(): array
{
    $components = [];

    foreach ($this->getComponents() as $component) {
        if (($node = $component->toNode()) !== null) {
            $components[] = $node;
        }
    }

    return [
        '$xq' => SchemaVersion::CURRENT,
        'id' => $this->getId(),
        'token' => $this->isAnonymous() ? null : $this->makeToken(),
        'endpoints' => [
            'update' => route('xelqun.forms.update', absolute: false),
            // ... upload, search, validate and action
        ],
        'state' => [StatePath::ROOT => $this->getJsonData()],
        'components' => $components,
    ];
}
```

### 3. Each component makes its node

`toNode()` is on `Component`, so fields and layouts share it. A component hidden on the server returns `null` and never reaches the browser. Layouts add their children through `getChildNodes()`, which calls `toNode()` on each child.

```php
// src/Components/Component.php
public function toNode(): ?array
{
    if ($this->isHiddenStatically()) {
        return null;
    }

    $node = [
        'key' => $this->getKey(),
        'type' => $this->getType(),
        'hidden' => false,
        ...$this->getNodeAttributes(),
        'span' => $this->columnSpan,
        'props' => (object) $this->getProps(),
        'children' => $this->getChildNodes(),
    ];

    $node['rules']['visible'] = $this->visibleWhen?->toArray($this->container);
    $node['deps'] = $this->recordedDependencies;

    return $node;
}
```

`getNodeAttributes()` on `Field` adds what every field has, and `getProps()` adds what only this type has. Here's `TextInput`:

```php
// src/Components/Field.php
protected function getNodeAttributes(): array
{
    return [
        'path' => $this->getStatePath(),
        'label' => $this->getLabel(),
        'required' => $this->isRequired(),
        'disabled' => $this->isDisabled(),
        'live' => $this->getLiveConfig(),
        // ... hint, placeholder, rules, computed
    ];
}

// src/Components/TextInput.php
public function getType(): string
{
    return 'text-input';
}

protected function getProps(): array
{
    return [
        'inputType' => $this->inputType,
        'maxLength' => $this->length ?? $this->maxLength,
        'prefix' => $this->evaluate($this->prefix),
        // ... min, max, step, suffix, mask
    ];
}
```

Notice `$this->evaluate($this->prefix)`. That's where a closure you passed to `prefix()` runs. It runs here, on the server, and only its result goes into the JSON.

### 4. Your Vue page renders `XqForm`

The schema arrives as an ordinary Inertia prop. You hand it to `XqForm`.

```vue
<!-- resources/js/pages/Orders/Edit.vue (your app) -->
<script setup lang="ts">
import { XqForm } from '@xelqun/forms';
import type { FormSchema } from '@xelqun/forms';

defineProps<{ form: FormSchema; order: { id: number } }>();
</script>

<template>
    <XqForm :form="form" :action="`/orders/${order.id}`" method="put" />
</template>
```

### 5. `XqForm` makes a store and loops over the nodes

`useXqForm()` creates the store from the schema. The store is shared with every field below through `provide`.

```vue
<!-- resources/js/components/XqForm.vue -->
<script setup lang="ts">
const store = isStore(props.form) ? props.form : useXqForm(props.form);

provideFormStore(store);
</script>

<template>
    <form :data-xq-form="store.id" novalidate @submit.prevent="submit">
        <div class="xq-grid">
            <XqNode
                v-for="node in store.components"
                :key="node.key"
                :node="node"
            />
        </div>
    </form>
</template>
```

### 6. `XqNode` turns `type` into a component

This is the whole trick. The node's `type` is looked up in the registry, and the Vue component it finds is rendered with the node as its prop.

```vue
<!-- resources/js/components/XqNode.vue -->
<script setup lang="ts">
const props = defineProps<{ node: SchemaNode }>();
const store = useFormStore();
const registry = useRegistry();

const visible = computed(
    () => props.node.type !== 'hidden' && store.isVisible(props.node),
);
const component = computed(() => registry?.get(props.node.type) ?? null);
</script>

<template>
    <div v-if="visible" class="xq-cell" :data-xq-key="node.key">
        <component :is="component" v-if="component" :node="node" />
        <XqUnknownNode v-else :node="node" />
    </div>
</template>
```

The registry is a plain `Map`, filled from this list. Your own `components` in `createXelqunForms()` are merged on top, so they win.

```ts
// resources/js/components/defaults.ts
export function defaultComponents(): Record<string, ComponentResolver> {
    return {
        'text-input': XqTextInputField,
        select: XqSelectField,
        toggle: XqToggleField,
        'file-upload': () => import('./fields/XqFileUploadField.vue'),
        section: XqSection,
        grid: XqGrid,
        // ... every other field and layout
    };
}
```

### 7. The field reads and writes the store

A field component gets its node and calls `useField()`. The `value` it returns is a computed with a getter and a setter, so `v-model` on it writes straight into the store.

```ts
// resources/js/composables/useField.ts
export function useField<T = any>(nodeOrPath: SchemaNode | string) {
    const store = useFormStore();
    const node =
        typeof nodeOrPath === 'string'
            ? store.nodes.get(nodeOrPath)!
            : nodeOrPath;
    const path = node.path ?? node.key;

    const value = computed<T>({
        get: () => store.get(path),
        set: (next) => store.set(path, next, 'user'),
    });

    return {
        value,
        error: computed(() => store.errors[path] ?? null),
        required: computed(() => store.isRequired(node)),
        disabled: computed(() => store.isDisabled(node) || !!node.readOnly),
        // ... label, hint, id, onBlur
    };
}
```

### 8. A change may go to the server

`store.set()` saves the value, then asks whether the server needs to hear about it. It does if the field is live, or if another node's `deps` include this path.

```ts
// resources/js/core/store.ts
function set(path: string, value: unknown, source: SetSource = 'user'): void {
    // ...
    setPath(state, path, value);

    if (source === 'user') {
        delete errors[path];
    }

    if (source !== 'server') {
        notify(path, source); // queues a POST to /_xelqun/forms/update when needed
    }
}
```

On the server, the update endpoint rebuilds the form from the token and lets `UpdateProcessor` build the patch:

```php
// src/Http/Controllers/UpdateController.php
public function __invoke(Request $request, FormTokenEncrypter $tokens, UpdateProcessor $processor): JsonResponse
{
    $validated = $request->validate([
        'token' => ['required', 'string'],
        'seq' => ['required', 'integer', 'min:0'],
        'state' => ['present', 'array'],
        'changed' => ['present', 'array', 'max:100'],
        // ... deps
    ]);

    $form = $tokens->resolveForm($validated['token']);

    return response()->json($processor->process(
        $form,
        $validated['state']['data'] ?? [],
        array_values($validated['changed']),
        // ... deps and seq
    ));
}
```

### 9. Submit is an Inertia visit

When the form is sent, the store waits for live requests, checks the rules it can check, and makes a normal Inertia visit. `errorBag` keeps this form's errors apart from other forms on the page.

```ts
// resources/js/core/store.ts
async function submit(
    url: string,
    submitOptions: SubmitOptions = {},
): Promise<void> {
    processing.value = true;
    await settled(); // wait for live requests and uploads

    if (!validate()) {
        processing.value = false;
        // ...
        return;
    }

    router.visit(url, {
        method: submitOptions.method ?? 'post',
        data: { data: clone(state.data) },
        errorBag: schema.id,
        onError: (incoming) => setErrors(incoming as Record<string, string>),
        // ... onSuccess, onFinish
    });
}
```

### 10. Your controller saves

Your route gets the visit, and `save()` does the rest: validate, move uploads, and write to the database.

```php
// app/Http/Controllers/OrderController.php (your app)
public function update(Request $request, Order $order): RedirectResponse
{
    OrderForm::make($order)->save($request);

    return back();
}

// src/Form.php
public function save(Request|array $input): Model
{
    $uploads = app(UploadBatch::class);
    $data = $uploads->promote($this, $this->validateInput($input));

    try {
        $this->record = app(ModelPersister::class)->save($this, $data, $uploads);
    } catch (Throwable $exception) {
        $uploads->discardUnlessSettled();

        throw $exception;
    }

    return $this->record;
}
```

If validation fails, `validateInput()` throws Laravel's `ValidationException`. Inertia sends the errors back under the form's error bag, and step 9's `onError` puts each one on its field.

The sections below go through each of these parts in more detail.

## Components

Every field and layout is a `Component`. A field is a `Field`, which extends `Component`.

`TextInput::make('title')` creates the object, runs its `setUp()`, and applies any defaults you set with `configureUsing()`. Each fluent method, such as `->label()` or `->required()`, just stores a value on the object. Nothing runs yet.

A stored value can be a closure. Closures run later, when the form is filled or serialized, through `Component::evaluate()`. It uses Laravel's container, so you ask for what you need by name:

| Parameter    | What you get                                     |
| ------------ | ------------------------------------------------ |
| `$get`       | Reads another field's value.                     |
| `$set`       | Changes another field's value.                   |
| `$state`     | This field's own value.                          |
| `$record`    | The model being edited, or `null` when creating. |
| `$operation` | `create`, `edit` or `view`.                      |
| `$form`      | The form itself.                                 |
| `$component` | This component.                                  |

```php
use Xelqun\Forms\Components\Select;
use Xelqun\Forms\State\Get;

Select::make('state_id')
    ->options(fn (Get $get): array => State::where('country_id', $get('country_id'))->pluck('name', 'id')->all());
```

`$get` does one more thing: it remembers every field it reads. Here it records `country_id`. That list goes into the schema as the field's `deps`. When `country_id` changes, the browser knows it must ask the server for new options. You don't have to tell it.

## Filling a form

`OrderForm::make($order)` creates the form and calls `fill()`. That:

1. Reads each field's value from the record, or its default when there's no record.
2. Runs any `afterStateHydrated()` hooks.
3. Works out `computed()` fields.
4. Keeps a copy of the starting values, so the form knows what changed.

## From PHP to JSON

`toSchema()` turns the form into an array. It walks the tree and calls `toNode()` on each component. You pass the result to Inertia yourself:

```php
return Inertia::render('Orders/Edit', [
    'form' => OrderForm::make($order)->toSchema(),
]);
```

The top of the schema looks like this:

```json
{
    "$xq": 1,
    "id": "order-form",
    "token": "eyJpdiI6...",
    "endpoints": {
        "update": "/_xelqun/forms/update",
        "upload": "...",
        "search": "...",
        "validate": "...",
        "action": "..."
    },
    "state": { "data": { "title": "First order", "items": {} } },
    "components": []
}
```

And here's one field in `components`:

```json
{
    "key": "data.title",
    "type": "text-input",
    "path": "data.title",
    "label": "Title",
    "required": true,
    "disabled": false,
    "rules": { "visible": null, "required": null, "disabled": null },
    "computed": null,
    "live": null,
    "props": { "inputType": "text", "maxLength": 255 },
    "children": [],
    "deps": []
}
```

A few things to notice:

- **`$xq` is the schema version.** If the PHP and npm packages drift apart, the browser shows an error instead of drawing something wrong.
- **`key` is the field's address.** It's the field's state path, such as `data.title`, and every update, error and patch uses it.
- **`type` picks the Vue component.** `text-input` maps to `XqTextInputField`.
- **`props` is per type.** A select has `options` there, a file upload has its accepted types.
- **Hidden fields aren't sent.** A field hidden by the server isn't in the schema, and its value isn't in `state` either. When a closure hides it, the schema gets a small placeholder, so it can appear later.
- **Anonymous forms have no token.** `Form::make([...])` works for a simple form, but a live field, an upload or a searchable select needs a class, because the server has to rebuild it.

## In the browser

`createXelqunForms()` sets up the Vue plugin. It builds the component registry, which maps each `type` to a Vue component. Your own `components` win over the built-in ones.

`<XqForm :form="form">` then creates a store with `createFormStore()`. The store holds the values, the errors, and every node by its key. It isn't Inertia's `useForm`.

Each node is drawn by `XqNode`, which looks up `node.type` in the registry. A field component calls `useField(node)` to get its value, error and label. When the user types, `useField` writes to the store.

## What runs in the browser, and what doesn't

Some rules are sent as data, so the browser can run them without asking the server. Others are closures, so only the server can run them.

| Runs in the browser                                             | Runs on the server                                |
| --------------------------------------------------------------- | ------------------------------------------------- |
| `visibleWhen()`, `requiredWhen()`, `disabledWhen()` with `When` | Anything with a closure, such as `hidden(fn ...)` |
| `computed('qty * price')`                                       | `options(fn ...)`, `label(fn ...)`                |
| Required and length checks before sending                       | `afterStateUpdated()` hooks                       |
|                                                                 | All validation and saving                         |

A `When` rule becomes a small tree like `{ "op": "eq", "path": "data.type", "value": "company" }`. A `computed()` expression is parsed once, in PHP, into a similar tree. The browser only evaluates these trees. It never parses code.

PHP has the same evaluators. On submit, the server works out every condition and computed value again, so a changed total in the browser has no effect. Shared test files in `tests/Fixtures/conformance` make sure both sides give the same answer.

## Live updates

When a field changes, the store sends it to the server if the field is `live()`, has an `afterStateUpdated()` hook, or another field's `deps` include it.

<!-- flow: forms-live -->

Three things keep this smooth:

- **One request at a time.** A new change cancels the request in flight and sends both changes together.
- **Sequence numbers.** A late answer to an older request is thrown away.
- **Typing wins.** If the user edited a field after the request left, the answer doesn't overwrite it.

The patch only has the nodes that changed. The browser merges them by key, so the other fields stay as they are and nothing loses focus.

## Submitting

The final submit isn't a package endpoint. The store waits for live requests and uploads to finish, then makes an Inertia visit to your URL:

```php
public function update(Request $request, Order $order): RedirectResponse
{
    OrderForm::make($order)->save($request);

    return back();
}
```

`save()` does this:

1. **Authorize.** It calls your `authorize()`, and aborts with 403 if it says no.
2. **Lock.** `FormValidator::prepare()` loads the posted values, then fixes what the browser can't change. A hidden, disabled or read-only field gets its server value back. A field hidden by `visibleWhen()` loses its value. Computed fields are worked out again.
3. **Validate.** Each visible field gives its rules, made of `required` or `nullable`, then its type's rules, then yours. Laravel's validator runs them. Error keys are state paths, such as `data.items.4f2c.price`, so each error lands on its field. Labels like "Price (item 2)" make the messages readable.
4. **Clean.** Only fields the form knows about are kept. A posted key that isn't a field never reaches your model.
5. **Save.** `ModelPersister::save()` runs your `beforeSave()` hook, fills and saves the model, saves relationships, and runs `afterSave()`, all in one database transaction. `afterCommit()` runs once it's committed.

If validation fails, Inertia gets the errors in the form's own error bag, named after its `id`. That's why several forms can share one page.

### Relationships

Some components save themselves instead of being a column on the model:

| Component                                    | Relationship  | How it saves                                                      |
| -------------------------------------------- | ------------- | ----------------------------------------------------------------- |
| `Group::make()->relationship()`              | BelongsTo     | Saves the related model and links it, before the parent saves.    |
| `Group::make()->relationship()`              | HasOne        | Saves the related model after the parent saves.                   |
| `Repeater::make()->relationship()`           | HasMany       | Updates existing rows, creates new ones and deletes removed ones. |
| `Select::make()->relationship()->multiple()` | BelongsToMany | Syncs the pivot table.                                            |

A repeater only updates rows that already belong to this record. A row key from another record can't be used to change it.

## Uploads

A file goes to the server as soon as it's picked, not when the form is sent.

1. **Upload.** The browser posts the file to `/_xelqun/forms/upload`. The server rebuilds the form, checks the field accepts uploads, and validates the file with the field's rules.
2. **Store for now.** `TemporaryUploadStore` saves it under `xelqun-tmp/` on the local disk, with a note of who uploaded it, for which form and field, and when. The browser gets a token for the file and puts it in the field's value.
3. **Check on submit.** The field's rules make sure each token belongs to this session, this form and this field, and hasn't expired. A plain path is only accepted if the field already had it, so the browser can't point at someone else's file.
4. **Move.** `UploadBatch` copies the files to their final disk before the transaction. If the save fails, the copies are deleted. If it succeeds, the temporary files are deleted.
5. **Clean up.** `php artisan xelqun-forms:prune-uploads` deletes temporary files that were never used.

## Actions

An `Action` is a component too, so it lives in the same tree and gets the same token.

- **It's hidden until you allow it.** An action with no `authorize()` doesn't show and can't run. A string is a policy ability, checked on the record.
- **Opening a modal** posts `{ phase: "mount" }` to `/_xelqun/forms/action`. The server rebuilds the form, finds the action by its key, checks `canRun()`, and sends back the modal's schema.
- **The modal is a form.** `ActionForm` extends `Form`, so modal fields have validation, live updates and uploads. Its token is the parent's token plus the action's key, so every request inside the modal checks the action again.
- **Running it** posts `{ phase: "run" }`. The server validates the modal's fields and calls your closure inside a database transaction. It answers with a patch, and an optional redirect.

The server only runs actions the form declares. A made-up action key is a 404.

## Notifications

`Notification::make()->send()` adds the notification to `NotificationQueue`, which lasts for one request.

- On a Xelqun endpoint, such as a live update or an action, the notifications go back in the JSON answer. A request that fails sends none.
- Anywhere else, they're flashed with Inertia under `xelqunNotifications`, and shown after the next visit.

The browser shows them in a small separate Vue app, so they survive page changes.

## Extending it

| You want to                        | Use                                                                |
| ---------------------------------- | ------------------------------------------------------------------ |
| Render a Vue component of your own | `CustomField::make()->component('acme/rating')`                    |
| Build a reusable field             | Extend `Field`. `php artisan make:xelqun-field` makes both halves. |
| Give a field search or uploads     | Implement `HasSearchResults` or `AcceptsUploads`.                  |
| Restyle a built-in field           | Register your own component for its type in `createXelqunForms()`. |
| Set defaults for every field       | `TextInput::configureUsing()` in a service provider.               |
| Ship fields to other apps          | A [plugin](/forms/plugins).                                        |

## Debugging tips

- **A field doesn't update.** Check the update request in the browser's network tab. Is the field's key in `changed`? If not, the browser didn't know it mattered. Add `->live()` or `->dependsOn()`.
- **A value is ignored on save.** It's probably locked. A hidden, disabled or read-only field always keeps its server value.
- **419 or 403 from an endpoint.** The token expired, or it was made for another user. Reloading the page gets a new one.
- **"No component is registered for type" in the form.** The `type` isn't in the registry. Check the plugin or component is registered in `createXelqunForms()`.
- **Read the tests.** `tests/Feature` in the package covers every endpoint, and the schema snapshots in `tests/.pest/snapshots` show the exact JSON of each field.
