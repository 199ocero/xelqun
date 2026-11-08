---
title: Standalone actions
description: ActionSet gives you buttons on a page, with no form or table around them.
---

An `ActionSet` is a class of actions. Each click rebuilds it on the server and checks the action again, then the page reloads to show what changed.

```php
use App\Models\Order;
use Xelqun\Actions\Action;
use Xelqun\Actions\ActionSet;

class OrderActions extends ActionSet
{
    public function model(): string
    {
        return Order::class;
    }

    public function actions(): array
    {
        return [
            Action::make('ship')
                ->authorize('update')
                ->requiresConfirmation()
                ->successNotificationTitle('Shipped')
                ->action(fn (Order $record) => $record->ship()),
        ];
    }
}
```

<!-- example: standalone-actions -->

A set that works on a record must define `model()`, or the page fails. `authorize('update')` asks the `update` ability of an `OrderPolicy`, so without a policy the button is hidden. `ship()` stands for a method on your own model.

Pass it to your page, and render it with `XqActions`.

```php
return Inertia::render('Orders/Show', [
    'actions' => OrderActions::make($order)->toSchema(),
]);
```

```vue
<script setup lang="ts">
import { XqActions, type FormSchema } from '@xelqun/forms';

defineProps<{ actions: FormSchema }>();
</script>

<template>
    <XqActions :actions="actions" />
</template>
```
