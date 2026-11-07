---
title: Summaries
description: Add totals, averages, counts and ranges under a column, worked out over every row.
---

```php
use Xelqun\Tables\Summarizers\Average;
use Xelqun\Tables\Summarizers\Sum;

TextColumn::make('price')
    ->money('USD')
    ->summarize([Sum::make()->label('Total'), Average::make()]);
```

<!-- example: table-summaries -->

A summary covers every row of the search and filters, not just this page, and it's formatted like the column.

| Summarizer | Shows                         |
| ---------- | ----------------------------- |
| `Sum`      | The total                     |
| `Average`  | The mean                      |
| `Count`    | The number of rows            |
| `Range`    | The lowest and highest values |
