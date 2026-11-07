---
title: Key-value
description: KeyValue edits pairs of keys and values, and stores them as one array.
---

```php
use Xelqun\Forms\Components\KeyValue;

KeyValue::make('specs')
    ->keyLabel('Spec')
    ->valueLabel('Detail')
    ->addActionLabel('Add spec');
```

<!-- example: form-key-value -->

The state is an array, so a model needs an array cast.

| Method                                   | What it does                     |
| ---------------------------------------- | -------------------------------- |
| `keyLabel()`, `valueLabel()`             | The column headings.             |
| `keyPlaceholder()`, `valuePlaceholder()` | The placeholders.                |
| `addActionLabel()`                       | The label of the add button.     |
| `addable(false)`                         | Stops people from adding rows.   |
| `deletable(false)`                       | Stops people from removing rows. |
| `editableKeys(false)`                    | Locks the keys.                  |
| `editableValues(false)`                  | Locks the values.                |
| `reorderable()`                          | Lets people change the order.    |
