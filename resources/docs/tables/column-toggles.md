---
title: Column toggles
description: Let viewers hide columns, and remember their choice.
---

```php
TextColumn::make('sku')->toggleable();

TextColumn::make('created_at')->toggleable(isHiddenByDefault: true);
```

<!-- example: table-column-toggles -->

`toggleable()` lets the viewer hide a column, and their choice stays. `isHiddenByDefault` starts it hidden.
