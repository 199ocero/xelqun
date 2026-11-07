---
title: Query builder
description: QueryBuilder is a filter where people build their own rules, matching all or any.
---

```php
use Xelqun\Tables\Filters\Constraints\BooleanConstraint;
use Xelqun\Tables\Filters\Constraints\DateConstraint;
use Xelqun\Tables\Filters\Constraints\NumberConstraint;
use Xelqun\Tables\Filters\Constraints\SelectConstraint;
use Xelqun\Tables\Filters\Constraints\TextConstraint;
use Xelqun\Tables\Filters\QueryBuilder;

QueryBuilder::make('rules')
    ->label('Custom rules')
    ->constraints([
        TextConstraint::make('name'),
        TextConstraint::make('category.name')->label('Category'),
        NumberConstraint::make('price'),
        DateConstraint::make('released_at'),
        BooleanConstraint::make('is_featured'),
        SelectConstraint::make('status')->options(['draft' => 'Draft', 'active' => 'Active']),
    ]);
```

<!-- example: table-filter-query-builder -->

Use a dot for a relation. Only the constraints you list can be asked about, and a rule with a bad value is skipped.

> [!NOTE]
> Rules match all or any. Nested groups aren't supported yet.
