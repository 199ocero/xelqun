---
title: Repeater
description: Repeater holds rows of fields, as cards, a grid or a table, saved to JSON or a HasMany relationship.
---

```php
use Xelqun\Forms\Components\Repeater;
use Xelqun\Forms\Components\TextInput;

Repeater::make('members')
    ->schema([
        TextInput::make('name')->required(),
        TextInput::make('role')->required(),
    ])
    ->columns(2)
    ->defaultItems(1)
    ->addActionLabel('Add member');
```

Rows have stable keys, so errors land on the right row even after a drag. The state is a list, so a model needs an array cast, unless you use `relationship()`.

<!-- example: repeaters -->

## Limits

```php
Repeater::make('members')->schema([...])->minItems(1)->maxItems(10);
```

## What people can do

| Method                              | What it does                                                                      |
| ----------------------------------- | --------------------------------------------------------------------------------- |
| `addable(false)`                    | Stops adding rows. A row posted anyway is dropped on the server.                  |
| `deletable(false)`                  | Stops deleting rows. A row removed anyway comes back on the server.               |
| `reorderable(false)`                | Stops reordering.                                                                 |
| `reorderableWithDragAndDrop(false)` | Turns dragging off. The move buttons stay, for keyboard and touch users.          |
| `cloneable()`                       | Adds a button that copies a row. File fields start empty in the copy.             |
| `collapsible()`, `collapsed()`      | Lets rows collapse, or starts them collapsed. A row with an error always opens.   |
| `requiresDeleteConfirmation()`      | Asks before deleting a row.                                                       |
| `itemLabel()`                       | The singular name of one row, used in headings and error messages.                |
| `itemNumbers()`                     | Shows each row's number.                                                          |
| `extraItemActions()`                | Adds buttons to each row's header. `$get`, `$set` and `$record` work on that row. |

## A list of one field

`simple()` makes rows of one field, with a plain list of values as the state.

```php
Repeater::make('emails')->simple(TextInput::make('email')->email()->required());
```

## A grid of cards

```php
Repeater::make('members')->schema([...])->grid(2);
```

## A table

`table()` shows the rows as a table, with one column per field, in order.

```php
use Xelqun\Forms\Components\Repeater\TableColumn;

Repeater::make('items')
    ->table([
        TableColumn::make('Product'),
        TableColumn::make('Qty')->width('100px'),
    ])
    ->schema([
        TextInput::make('product')->required(),
        TextInput::make('qty')->integer()->required(),
    ]);
```

`hiddenHeaderLabel()` hides a heading on screen, and `markAsRequired()` shows an asterisk.

## Calculated rows

`computed()` works inside a row. A value in a row is read by its name, and the total reads every row.

```php
Repeater::make('items')->schema([
    TextInput::make('qty')->numeric(),
    TextInput::make('price')->numeric(),
    TextInput::make('subtotal')->computed('qty * price')->disabled(),
]);

TextInput::make('total')->computed('sum(items.*.subtotal)')->disabled();
```

<!-- example: repeater-totals -->

## Saving to a relationship

`relationship()` saves rows to a `HasMany`. The name defaults to the field's.

```php
Repeater::make('items')
    ->relationship(orderColumn: 'sort')
    ->schema([...]);
```

These change a related row's data on its way through:

| Method                                            | When it runs                                             |
| ------------------------------------------------- | -------------------------------------------------------- |
| `mutateRelationshipDataBeforeFillUsing()`         | Before the row fills the form.                           |
| `mutateRelationshipDataBeforeCreateUsing()`       | Before a new row is created. Return null to skip it.     |
| `mutateRelationshipDataBeforeSaveUsing()`         | Before an existing row is saved. Return null to skip it. |
| `afterCreate()`, `afterUpdate()`, `afterDelete()` | After each change, with `$data` and `$record`.           |

## No duplicates

`distinct()` on a field inside a repeater makes sure no two rows share a value.

```php
Select::make('product_id')->options($products)->distinct();
```
