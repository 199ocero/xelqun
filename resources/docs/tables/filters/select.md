---
title: Select
description: SelectFilter narrows rows to one or many values, from a list or a relationship.
---

```php
use Xelqun\Tables\Filters\SelectFilter;

SelectFilter::make('status')->options(['draft' => 'Draft', 'active' => 'Active']);
```

<!-- example: table-filter-select -->

`attribute()` sets the column, which defaults to the filter's name. `multiple()` lets people pick several.

```php
SelectFilter::make('state')->attribute('status')->multiple()->options($statuses);
```

## Relationships

```php
SelectFilter::make('category')->relationship('category', 'name')->searchable();
```

It keeps rows that have the chosen record. `searchable()` searches the related records on the server, so the list never reaches the page. Use it for anything over a few hundred related records. Without it, the filter sends every related record, until there are more than 500: then it searches on the server anyway.
