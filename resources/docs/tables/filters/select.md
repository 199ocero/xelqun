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
SelectFilter::make('category')->relationship('category', 'name');
```

It keeps rows that have the chosen record.

> [!NOTE]
> A relationship filter sends every related record. For a long list, use a [custom filter](/tables/filters/custom) with an async `Select`.
