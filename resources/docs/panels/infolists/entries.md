---
title: Entries
description: Text, icon, image, color, code, key-value and repeatable entries.
---

An entry takes the options of its table column, so a value reads the same in both places.

| Entry             | Shows                                               |
| ----------------- | --------------------------------------------------- |
| `TextEntry`       | Text, badges, dates and money                       |
| `IconEntry`       | An icon, or a true or false value                   |
| `ImageEntry`      | An image                                            |
| `ColorEntry`      | A color swatch                                      |
| `CodeEntry`       | Text in a monospaced block. An array shows as JSON. |
| `KeyValueEntry`   | Pairs of keys and values                            |
| `RepeatableEntry` | Rows of entries from a relationship or list         |

```php
use Xelqun\Panels\Infolists\CodeEntry;
use Xelqun\Panels\Infolists\ColorEntry;
use Xelqun\Panels\Infolists\KeyValueEntry;
use Xelqun\Panels\Infolists\RepeatableEntry;
use Xelqun\Panels\Infolists\TextEntry;

ColorEntry::make('accent');

CodeEntry::make('snippet');

KeyValueEntry::make('specs');

RepeatableEntry::make('items')->schema([
    TextEntry::make('product'),
    TextEntry::make('subtotal')->money('USD'),
]);
```

<!-- screenshot: entries | A post, shown with text, color, key-value and code entries -->

Each takes `label()`, `state()`, `formatStateUsing()`, `color()`, `icon()` and `url()`.

## Text entry options

`TextEntry` takes the text column's options, so `description()`, `placeholder()`, `limit()`, `words()`, `lineClamp()`, `copyable()`, `tooltip()`, `numeric()`, `since()`, `badge()`, `money()` and the dates work as they do in a [table](/tables/columns/text). On top of those:

| Method       | What it does                                                                       |
| ------------ | ---------------------------------------------------------------------------------- |
| `prefix()`   | Text before the value, such as a currency sign.                                    |
| `suffix()`   | Text after the value, such as a unit.                                              |
| `html()`     | Shows the value as HTML, cleaned on the server the way a rich editor's content is. |
| `markdown()` | Shows the value as Markdown, turned into clean HTML on the server.                 |

```php
TextEntry::make('weight')->numeric()->suffix(' kg');

TextEntry::make('body')->markdown();

TextEntry::make('sku')->copyable()->placeholder('No SKU yet');
```

## Hints and inline labels

Every entry takes `hint()`, a short note beside the label, and `inlineLabel()`, which puts the label beside the value instead of above it. Both take a closure, which gets `$record`.

```php
TextEntry::make('email')->hint('Used for receipts')->inlineLabel();
```

[See them in the demo](demo:/posts/1).
