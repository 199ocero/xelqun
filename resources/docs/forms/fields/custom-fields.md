---
title: Custom fields
description: Add your own field with a PHP class and a Vue component, or wrap one Vue component with CustomField.
---

## A one-off field

`CustomField` renders a Vue component you've registered. It keeps the label, validation and saving of any other field.

```php
use Xelqun\Forms\Components\CustomField;

CustomField::make('rating')
    ->component('app/rating')
    ->props(['max' => 5]);
```

<!-- example: form-custom-field -->

Register the component in your Inertia entry file.

```ts
app.use(
    createXelqunForms({
        components: {
            'app/rating': () => import('./components/RatingField.vue'),
        },
    }),
);
```

## A reusable field

For a field you'll use often, make a class.

```bash
php artisan make:xelqun-field Rating --type=app/rating
```

That creates both halves. Add `--search` or `--uploads` to start from a field that does those.

```php
use Xelqun\Forms\Components\Field;

class Rating extends Field
{
    public function getType(): string
    {
        return 'app/rating';
    }

    protected function getTypeRules(): array
    {
        return ['integer', 'between:1,5'];
    }
}
```

The Vue half uses `useField()` for the value and its errors.

To share a field between apps, see [plugins](/forms/plugins).
