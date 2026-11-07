---
title: Overview
description: Xelqun Forms lets you define forms in PHP and render them as Vue components, with validation, relationships, uploads and conditional logic included.
---

Xelqun Forms is a form builder for Laravel, Inertia and Vue. You describe a form in a PHP class, and the package renders it, validates it, and saves it to Eloquent. Most forms need no Vue at all.

```php
use Xelqun\Forms\Components\Repeater;
use Xelqun\Forms\Components\Select;
use Xelqun\Forms\Components\TextInput;
use Xelqun\Forms\Form;

class OrderForm extends Form
{
    public function schema(): array
    {
        return [
            TextInput::make('customer')->required(),
            Select::make('country_id')->relationship('country', 'name'),
            Repeater::make('items')->relationship()->schema([
                TextInput::make('qty')->numeric(),
                TextInput::make('price')->numeric(),
                TextInput::make('subtotal')->computed('qty * price')->disabled(),
            ]),
            TextInput::make('total')->computed('sum(items.*.subtotal)')->disabled(),
        ];
    }
}
```

<!-- example: form-overview -->

This is that idea running. The form below is a PHP class, and nothing on this page was written in Vue for it.

## How it works

A form goes through three steps:

1. **Your controller builds it.** `OrderForm::make($order)->toSchema()` turns the class into data, and you pass that to an Inertia page as a prop.
2. **`XqForm` renders it.** The Vue component draws each field from that data.
3. **The server checks it.** `OrderForm::make($order)->save($request)` validates what came back and writes it to the database.

Rules that don't need the database, such as conditions and calculations, run in the browser with no server call. The server checks them again on submit, so the browser is never trusted.

## What you get

- **Every field you'd expect.** Selects with relationships, date and time pickers, tags, sliders, colors, repeaters, a block builder, and rich text, Markdown and code editors.
- **Instant browser rules.** Conditions and calculations run without a round trip.
- **Small server updates.** Dependent fields update through small patches that keep focus and cursor position.
- **Secure by default.** A form rebuilds from an encrypted token, and every endpoint runs `authorize()` and validation again.
- **Wizards, actions and notifications.** Multi-step forms, buttons that run PHP through a modal, and toasts you send from PHP.

> [!TIP]
> Want to see all of it working together? [Open the demo](demo:/), a full admin panel built on these forms.

## Where to go next

- [Install the package](/forms/installation)
- [Build your first form](/forms/your-first-form)
- [Browse the fields](/forms/fields/overview)
