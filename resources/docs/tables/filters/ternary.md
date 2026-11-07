---
title: Ternary
description: TernaryFilter narrows rows to yes, no, or either.
---

```php
use Xelqun\Tables\Filters\TernaryFilter;

TernaryFilter::make('is_featured')->label('Featured');
```

<!-- example: table-filter-ternary -->

`attribute()` sets the column, which defaults to the filter's name.
