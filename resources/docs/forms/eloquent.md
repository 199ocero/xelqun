---
title: Eloquent and relationships
description: Forms fill themselves from a record, and save it and its relationships in one transaction.
---

`model()` names the class a new record is created from. `make()` takes an existing record.

```php
class ProductForm extends Form
{
    public function model(): string
    {
        return Product::class;
    }
}

ProductForm::make();          // create
ProductForm::make($product);  // edit
```

`save()` validates first, then writes everything inside one transaction, so a failed save leaves nothing behind.

## Relationships at a glance

| Relationship                             | Field                                                            |
| ---------------------------------------- | ---------------------------------------------------------------- |
| `BelongsTo`                              | `Select::make('author_id')->relationship('author', 'name')`      |
| `BelongsToMany`                          | `Select::make('tags')->multiple()->relationship('tags', 'name')` |
| `HasMany`                                | `Repeater::make('items')->relationship()`                        |
| `HasMany` of files                       | `FileUpload::make('photos')->relationship('photos')`             |
| `HasOne` or `BelongsTo`, edited in place | `Group::make([...])->relationship('profile')`                    |
| `MorphTo`                                | `MorphToSelect::make('commentable')->types([...])`               |

A relationship `Select` without `searchable()` sends every related record to the page, up to 500. Add `searchable()` for anything over a few hundred records, as on the [select](/forms/fields/select) page.

## Casts

A field that holds a list or an array needs an `array` cast on the model: `CheckboxList`, `TagsInput`, `KeyValue`, `Repeater` and `Builder`, plus `Select` and `ToggleButtons` with `multiple()`, and `FileUpload` with `multiple()`.

A `Checkbox` or `Toggle` needs a `boolean` cast.

## Changing what's saved

`mutateBeforeSave()` adjusts the attributes just before they reach the model.

```php
public function mutateBeforeSave(array $attributes): array
{
    $attributes['user_id'] = auth()->id();

    return $attributes;
}
```

`saved(false)` keeps a field off the model, and it stays in the validated data.

## The form's operation

The operation is `create` for a new record and `edit` for an existing one. `hiddenOn()`, `visibleOn()` and `disabledOn()` read it.

```php
TextInput::make('sku')->disabledOn('edit');
```
