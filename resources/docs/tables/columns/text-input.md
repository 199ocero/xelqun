---
title: Text input
description: TextInputColumn edits a value right in the cell, with validation.
---

```php
use Xelqun\Tables\Columns\TextInputColumn;

TextInputColumn::make('stock')
    ->authorize('update')
    ->rules(['required', 'integer', 'min:0']);
```

<!-- example: table-text-input -->

`rules()` takes Laravel rules, and an error shows in the cell. It's read-only until `authorize()` says who can edit.
