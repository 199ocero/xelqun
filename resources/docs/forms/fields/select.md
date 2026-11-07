---
title: Select
description: Select picks one or many options from a list or a relationship, with search, server loading and inline record creation.
---

```php
use Xelqun\Forms\Components\Select;

Select::make('status')->options([
    'draft' => 'Draft',
    'reviewing' => 'Reviewing',
    'published' => 'Published',
]);
```

<!-- example: form-select -->

## Option groups

A nested array groups the options under a heading.

```php
Select::make('status')->options([
    'In progress' => ['draft' => 'Draft', 'reviewing' => 'Reviewing'],
    'Done' => ['published' => 'Published'],
]);
```

## Options from an enum

`options()` takes a backed enum's class name too.

```php
Select::make('status')->options(OrderStatus::class);
```

## Choosing several

```php
Select::make('technologies')->multiple()->options([...]);
```

The state is then a list, so a model needs an array cast. `reorderable()` lets people change the order of what they picked.

## Searching

`searchable()` filters options as you type.

```php
Select::make('author_id')->options(User::pluck('name', 'id'))->searchable();
```

| Method                     | What it does                                   |
| -------------------------- | ---------------------------------------------- |
| `searchDebounce()`         | Milliseconds to wait after the last keystroke. |
| `searchLimit()`            | How many results to show.                      |
| `searchPrompt()`           | Shown before the user types.                   |
| `searchingMessage()`       | Shown while a search runs.                     |
| `noSearchResultsMessage()` | Shown when a search finds nothing.             |
| `noOptionsMessage()`       | Shown when the field has no options at all.    |
| `loadingMessage()`         | Shown while the options load.                  |

## Relationships

`relationship()` loads options from a `BelongsTo` or `BelongsToMany`, and saves the choice.

```php
Select::make('country_id')->relationship('country', 'name');

Select::make('tags')->multiple()->relationship('tags', 'name');
```

The third argument changes the query. Options load from the server when you search, and `preload()` loads them all up front.

```php
use Illuminate\Database\Eloquent\Builder;

Select::make('author_id')
    ->relationship('author', 'name', fn (Builder $query) => $query->where('is_active', true))
    ->searchable()
    ->preload();
```

`pivotData()` sets extra pivot columns on the records it attaches.

```php
Select::make('tags')
    ->multiple()
    ->relationship('tags', 'name')
    ->pivotData(['added_by' => auth()->id()]);
```

## Creating a record from the select

`createOptionForm()` shows a button that opens these fields, and selects the record they create.

```php
Select::make('author_id')
    ->relationship('author', 'name')
    ->createOptionForm([
        TextInput::make('name')->required(),
        TextInput::make('email')->email()->required(),
    ]);
```

`editOptionForm()` adds a button that edits the selected record. `manageOptionForm()` uses the same fields for both.

`createOptionUsing()` and `updateOptionUsing()` save it your own way. `createOptionAction()`, `editOptionAction()` and `manageOptionActions()` change the buttons themselves, such as their `authorize()`.

## Yes and no

```php
Select::make('feedback')->boolean();
```

## Disabling options

Options a user can see but not choose. A value in this list is refused on the server too.

```php
Select::make('plan')
    ->options(['free' => 'Free', 'pro' => 'Pro'])
    ->disableOptionWhen(fn (string $value): bool => $value === 'pro');
```

## Options that depend on another field

```php
use Xelqun\Forms\State\Get;

Select::make('state_id')
    ->options(fn (Get $get): array => State::where('country_id', $get('country_id'))->pluck('name', 'id')->all())
    ->disabled(fn (Get $get): bool => blank($get('country_id')));
```

See [live and dependent fields](/forms/live-fields).
