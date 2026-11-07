---
title: Builder
description: Builder holds rows of different block types, each with its own fields, stored as one JSON column.
---

A repeater's rows all look the same. A builder's rows each choose a block.

```php
use Xelqun\Forms\Components\Builder;
use Xelqun\Forms\Components\Builder\Block;
use Xelqun\Forms\Components\Textarea;
use Xelqun\Forms\Components\TextInput;

Builder::make('content')->blocks([
    Block::make('heading')
        ->icon('hash')
        ->maxItems(1)
        ->schema([TextInput::make('text')->required()]),
    Block::make('paragraph')
        ->schema([Textarea::make('text')->required()]),
]);
```

<!-- example: page-editor -->

The rows land in one column as `{type, data}`, so a model needs an array cast.

## Grouping blocks

A string key groups blocks under that heading in the menu.

```php
Builder::make('content')->blocks([
    'Text' => [Block::make('heading'), Block::make('paragraph')],
    'Media' => [Block::make('image')],
]);
```

## Limits

`maxItems()` on a block limits how many times it can be used in one builder. `minItems()` and `maxItems()` on the builder limit the rows.

## Previews

`blockPreviews()` shows each block's `preview()`, with a button to edit it. A preview is a Blade view, and it gets the row's data as variables.

```php
Builder::make('content')
    ->blockPreviews()
    ->blocks([
        Block::make('quote')
            ->preview('blocks.quote')
            ->schema([Textarea::make('text')->required()]),
    ]);
```

A builder takes the same options as a [repeater](/forms/fields/repeater), such as `collapsible()`, `cloneable()` and `reorderable()`.
