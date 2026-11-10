---
title: Overview
description: Columns define what a table shows, with labels, search, sorting, links and relationships.
---

```php
use Xelqun\Tables\Columns\TextColumn;

TextColumn::make('name')
    ->label('Product')
    ->searchable()
    ->sortable();
```

<!-- example: table-column-options -->

| Column                                        | Use it for                     |
| --------------------------------------------- | ------------------------------ |
| [TextColumn](/tables/columns/text)            | Text, dates, money and badges  |
| [IconColumn](/tables/columns/icon)            | Icons and true or false values |
| [ImageColumn](/tables/columns/image)          | Images                         |
| [ColorColumn](/tables/columns/color)          | Color swatches                 |
| [SelectColumn](/tables/columns/select)        | Picking a value in the cell    |
| [ToggleColumn](/tables/columns/toggle)        | A switch in the cell           |
| [TextInputColumn](/tables/columns/text-input) | Typing in the cell             |
| [CheckboxColumn](/tables/columns/checkbox)    | A checkbox in the cell         |
| [CustomColumn](/tables/columns/custom)        | Your own Vue component         |

## Relationships

A dot reaches through a relationship. Eager load it in `query()` to avoid extra queries.

```php
TextColumn::make('category.name')->label('Category');
```

## Related totals

`counts()` loads how many related records each row has, and `sum()` adds up a related column. Both load with the rows, in the same query. Name the column after the result.

```php
TextColumn::make('orders_count')->counts('orders')->sortable();
TextColumn::make('orders_sum_total')->sum('orders', 'total')->money('USD')->sortable();
```

They sort with no closure of their own.

## Working out a value

`state()` works out the cell's value, instead of reading it from the record.

```php
IconColumn::make('verified')
    ->state(fn (User $record): bool => $record->email_verified_at !== null)
    ->boolean();
```

`formatStateUsing()` changes the value for display. Sorting and search still use the original.

```php
TextColumn::make('sku')->formatStateUsing(fn (string $state): string => strtoupper($state));
```

## Empty values and hover text

`default()` stands in when the record has nothing there. A closure gets `$record`. `tooltip()` shows text on hover, and a closure gets `$record` and `$state`.

```php
TextColumn::make('country')->default('Unknown');
IconColumn::make('is_featured')->boolean()->tooltip(fn (bool $state): string => $state ? 'On the home page' : 'Not featured');
```

## Links

`url()` links the cell.

```php
TextColumn::make('website')->url(fn (Brand $record): ?string => $record->website, openInNewTab: true);
```

A URL outside http(s), mailto, tel or this site is dropped.

## Color and icon

`color()` and `icon()` take a value or a closure.

```php
TextColumn::make('status')
    ->badge()
    ->color(fn (string $state): string => $state === 'active' ? 'success' : 'gray');
```

## Width and alignment

| Method         | What it does                                                            |
| -------------- | ----------------------------------------------------------------------- |
| `alignment()`  | `start`, `center` or `end`, for the heading, the cells and the summary. |
| `width()`      | Any CSS width, such as `'8rem'` or `'20%'`.                             |
| `grow()`       | Takes the room the other columns leave.                                 |
| `wrapHeader()` | Lets a long heading run onto more lines.                                |

```php
TextColumn::make('name')->grow();
TextColumn::make('stock')->alignment('end')->width('6rem');
```

## Column groups

`ColumnGroup` puts one heading over neighbouring columns, above their own headings. It goes in `columns()` beside the other columns.

```php
use Xelqun\Tables\Columns\ColumnGroup;

public function columns(): array
{
    return [
        TextColumn::make('name'),
        ColumnGroup::make('Inventory', [
            TextColumn::make('sku')->label('SKU'),
            TextColumn::make('stock')->numeric()->alignment('end'),
        ]),
    ];
}
```

<!-- example: table-column-groups -->

## Search

`searchable()` adds a column to the search above the table. `isIndividual: true` also gives it a search box of its own, under its heading, and `isGlobal: false` keeps it out of the search above. See [search and sorting](/tables/search-and-sorting).

```php
TextColumn::make('sku')->searchable(isIndividual: true, isGlobal: false);
```

## Enums

If a value is a cast enum, its `getLabel()`, `getColor()` and `getIcon()` fill in the cell. The column's own `color()`, `icon()` and `formatStateUsing()` win.

## Wide tables

Cells stay on one line, and a table that's too wide scrolls sideways, with its scrollbar on screen while the table is in view. `wrap()` on a [text column](/tables/columns/text) lets long text run onto more lines.

## Hiding columns

`hidden()` and `visible()` remove a column on the server. A hidden column isn't sent, searched or sorted. To let viewers choose, see [column toggles](/tables/column-toggles).

## Summaries

`summarize()` adds a line under a column. See [summaries](/tables/summaries).
