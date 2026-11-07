---
title: Tags input
description: TagsInput takes a list of short values, with suggestions and rules for each tag.
---

```php
use Xelqun\Forms\Components\TagsInput;

TagsInput::make('tags')->suggestions(['laravel', 'vue', 'inertia']);
```

<!-- example: form-tags-input -->

The state is a list, so a model needs an array cast. To store one string, use `separator()`.

```php
TagsInput::make('tags')->separator(',');
```

| Method          | What it does                                               |
| --------------- | ---------------------------------------------------------- |
| `suggestions()` | Tags the browser suggests. People can still type anything. |
| `splitKeys()`   | Keys that finish a tag, as well as Enter.                  |
| `reorderable()` | Lets people change the order.                              |
| `tagPrefix()`   | Text shown before each tag. It isn't stored.               |
| `tagSuffix()`   | Text shown after each tag. It isn't stored.                |

## Rules for each tag

`rules()` checks the list as a whole, and `nestedRecursiveRules()` checks each tag.

```php
TagsInput::make('tags')
    ->rules(['max:10'])
    ->nestedRecursiveRules(['min:2', 'max:30']);
```
