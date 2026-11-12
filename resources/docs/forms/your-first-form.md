---
title: Your first form
description: Build a form in PHP, show it on an Inertia page, and save it with Eloquent.
---

This page builds a form that edits an order: one PHP class, one controller and one small Vue page.

It assumes an `orders` table with `customer_name` and `country_id` columns, a `countries` table with a `name`, and an `Order` model that belongs to a country:

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }
}
```

## Define the form

A form is a class with a `schema()` method. Start one with the generator:

```bash
php artisan make:xelqun-form OrderForm
```

It writes `app/Forms/OrderForm.php` for the `Order` model, with one field. Fill in the schema:

```php
namespace App\Forms;

use App\Models\Order;
use Xelqun\Forms\Components\Select;
use Xelqun\Forms\Components\TextInput;
use Xelqun\Forms\Form;
use Xelqun\Forms\Layouts\Section;

class OrderForm extends Form
{
    public function model(): string
    {
        return Order::class;
    }

    public function schema(): array
    {
        return [
            Section::make('Order')->columns(2)->schema([
                TextInput::make('customer_name')->required()->maxLength(255),
                Select::make('country_id')->relationship('country', 'name')->searchable(),
            ]),
        ];
    }
}
```

## Show it and save it

Build the form in your controller. `make()` fills it from the record, and `toSchema()` turns it into data for the page.

```php
namespace App\Http\Controllers;

use App\Forms\OrderForm;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class OrderController
{
    public function edit(Order $order): Response
    {
        return Inertia::render('Orders/Edit', [
            'form' => OrderForm::make($order)->toSchema(),
            'orderId' => $order->id,
        ]);
    }

    public function update(Request $request, Order $order): RedirectResponse
    {
        OrderForm::make($order)->save($request);

        return back();
    }
}
```

`save()` validates the request, then creates or updates the record and its relationships inside one transaction.

Add the two routes in `routes/web.php`, behind `auth`, as a starter kit's layout expects a signed-in user:

```php
use App\Http\Controllers\OrderController;

Route::middleware('auth')->group(function () {
    Route::get('orders/{order}/edit', [OrderController::class, 'edit'])->name('orders.edit');
    Route::put('orders/{order}', [OrderController::class, 'update'])->name('orders.update');
});
```

`back()` shows nothing after a save. To show a toast, send a [queued notification](/forms/notifications/queued) before you return.

## Render it

Pass the schema to `XqForm`, and tell it where to submit. `Inertia::render('Orders/Edit')` loads `resources/js/pages/Orders/Edit.vue`.

```vue
<script setup lang="ts">
import { XqForm, type FormSchema } from '@xelqun/forms';

defineProps<{ form: FormSchema; orderId: number }>();
</script>

<template>
    <XqForm :form="form" :action="`/orders/${orderId}`" method="put" />
</template>
```

That's a working form, with validation errors shown under each field.

## Validate without saving

Not every form writes to a model. `validate()` returns the cleaned data and leaves saving to you. Hidden and disabled fields are left out of what it returns. Here `ContactForm` is a form with no `model()`, and `ContactMessage` is your own mailable.

```php
$data = ContactForm::make()->validate($request);

Mail::to('team@example.com')->send(new ContactMessage($data));
```

## Decide who may use it

`authorize()` runs on every request, including search, uploads and live updates. It allows everything until you say otherwise.

```php
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

public function authorize(?Authenticatable $user, ?Model $record): bool
{
    return $user !== null && $user->can('update', $record);
}
```

## Hook into saving

`Form` has three hooks around `save()`:

| Method                        | When it runs                                                                      |
| ----------------------------- | --------------------------------------------------------------------------------- |
| `beforeSave($data)`           | Inside the transaction, before anything is written. Throwing here stops the save. |
| `afterSave($record, $data)`   | Inside the transaction, after the record and its relationships are saved.         |
| `afterCommit($record, $data)` | Once the outermost transaction has committed. Use it for emails and jobs.         |

`mutateBeforeSave($attributes)` changes the attributes just before they reach the model.

The hooks declare `$record` as `Model`. Name your model above the class, as in `/** @extends Form<Order> */`, and Larastan reads it as an `Order` in each of them.

## View-only forms

`disabled()` shows a form that nobody can change, as on a View page. The lock travels in the token, so the package's endpoints (live updates, actions and uploads) refuse a tampered request on the server too. Your own update route doesn't read the token: build the form with the same `disabled()` there, which makes `save()` throw, or don't give a View page an update route at all.

```php
OrderForm::make($order)->disabled()->toSchema();
```

<!-- example: view-only -->
