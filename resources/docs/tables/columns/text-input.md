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

`rules()` takes Laravel rules. A refused value stays in the cell with the error under it, and the message names the column, as in "The Stock field...". It's read-only until `authorize()` says who can edit, and `'update'` needs a [policy](/tables/your-first-table#things-to-know).
