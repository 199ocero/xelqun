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

Use a dot for a relation. Only the constraints you list can be asked about, and a rule with a bad value is skipped. A rule only counts as an active filter when it filters something.

Each kind of value has its own field in a rule, and the panel shows the one the chosen column takes:

| Constraint                           | Value field                      |
| ------------------------------------ | -------------------------------- |
| `TextConstraint`, `NumberConstraint` | `value`                          |
| `SelectConstraint` with `options()`  | `option`, one of the option keys |
| `DateConstraint`                     | `date`, from a date picker       |
| `BooleanConstraint`                  | None                             |

That matters when you build the filter's state yourself, as in a test:

```php
$this->queryTable(ProductsTable::make(['team' => 1]), [
    'filters' => ['rules' => [
        ['constraint' => 'status', 'operator' => 'equals', 'option' => 'draft'],
    ]],
]);
```

## Groups of rules

Pick "Group of rules" as a rule's column to hold rules of its own, with their own "match any" switch. That builds a filter such as price over 15 and (name has Lamp or name has Sofa).

```php
QueryBuilder::make('rules')
    ->constraints([...])
    ->maxNestingDepth(2)
    ->maxRules(20);
```

| Method              | Does                                                                       |
| ------------------- | -------------------------------------------------------------------------- |
| `maxNestingDepth()` | How deep groups go. 3 by default. 1 allows no groups.                      |
| `maxRules()`        | The most rules across every group. A filter past it is ignored as a whole. |

In state, a group is a rule whose `constraint` is `QueryBuilder::GROUP`, with its own `rules` and `match_any`:

```php
['constraint' => QueryBuilder::GROUP, 'match_any' => true, 'rules' => [
    ['constraint' => 'name', 'operator' => 'contains', 'value' => 'Lamp'],
    ['constraint' => 'name', 'operator' => 'contains', 'value' => 'Sofa'],
]]
```
