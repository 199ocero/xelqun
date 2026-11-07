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

## Enums

If a value is a cast enum, its `getLabel()`, `getColor()` and `getIcon()` fill in the cell. The column's own `color()`, `icon()` and `formatStateUsing()` win.

## Wide tables

Cells stay on one line, and a table that's too wide scrolls sideways, with its scrollbar on screen while the table is in view. `wrap()` on a [text column](/tables/columns/text) lets long text run onto more lines.

## Hiding columns

`hidden()` and `visible()` remove a column on the server. A hidden column isn't sent, searched or sorted. To let viewers choose, see [column toggles](/tables/column-toggles).

## Summaries

`summarize()` adds a line under a column. See [summaries](/tables/summaries).
