---
title: Rich editor
description: RichEditor edits formatted text and stores clean HTML or JSON, with tables, mentions, merge tags, custom blocks, attachments and sanitizing.
---

```php
use Xelqun\Forms\Components\RichEditor;

RichEditor::make('content')->minHeight('16rem');
```

<!-- example: form-rich-editor -->

The editor loads only when a form needs it. The server cleans the HTML before it's saved.

## Toolbar

`toolbarButtons()` takes the buttons in groups. `disableToolbarButtons()` removes some from the defaults.

```php
RichEditor::make('content')->toolbarButtons([
    ['bold', 'italic', 'underline'],
    ['h2', 'h3'],
    ['bulletList', 'orderedList', 'link'],
]);

RichEditor::make('content')->disableToolbarButtons(['codeBlock']);
```

Besides the defaults there are `h1`, `h4`, `h5`, `h6`, `alignJustify` and `horizontalRule`. The `textColor`, `mergeTags` and `customBlocks` buttons are in the default toolbar, and show once they're configured, as below.

## Tables

The `table` button inserts a table. These tools show while the cursor is in one, and the default toolbar has most of them.

| Button                                          | What it does                    |
| ----------------------------------------------- | ------------------------------- |
| `tableAddRowBefore`, `tableAddRowAfter`         | Adds a row above or below.      |
| `tableAddColumnBefore`, `tableAddColumnAfter`   | Adds a column before or after.  |
| `tableDeleteRow`, `tableDeleteColumn`           | Deletes the row or the column.  |
| `tableMergeCells`, `tableSplitCell`             | Merges cells, or splits one.    |
| `tableToggleHeaderRow`, `tableToggleHeaderCell` | Makes the row or cell a header. |
| `tableDelete`                                   | Deletes the table.              |

The defaults leave out `tableAddRowBefore`, `tableAddColumnBefore` and `tableToggleHeaderCell`.

## Text colors

`textColors()` adds a `textColor` button with these colors, as hex and label. The sanitizer keeps a hex color on a `span` and nothing else.

```php
RichEditor::make('content')->textColors([
    '#ef4444' => 'Red',
    '#16a34a' => 'Green',
]);
```

## Merge tags

`mergeTags()` adds a `mergeTags` button that inserts a tag such as `{{ name }}`. Give it `name => label`, or a list of names.

```php
RichEditor::make('email_body')->mergeTags([
    'name' => 'Full name',
    'order_number' => 'Order number',
]);
```

`RichContentRenderer` fills them in when you show the content. It fills tags in text only, never inside a tag such as a link's address, and escapes every value except an `Htmlable` one. A closure runs once, the first time its tag is used.

```php
use Xelqun\Forms\Support\RichContentRenderer;

RichContentRenderer::make($template->email_body)
    ->mergeTags(['name' => $user->name, 'order_number' => $order->number])
    ->toHtml();
```

The renderer cleans the HTML again before it draws it.

## Mentions

`mentions()` offers people, or anything else, after a character. Each `MentionProvider` takes its character and `id => label` items, which the browser filters.

```php
use Xelqun\Forms\Components\RichEditor\MentionProvider;

RichEditor::make('comment')->mentions([
    MentionProvider::make('@')->items(fn (): array => User::pluck('name', 'id')->all()),
]);
```

For a long list, `getSearchResultsUsing()` searches on the server as people type. It gets `$search` and returns a few `id => label` pairs. It runs on every keystroke after the character, so keep it to one indexed query.

```php
MentionProvider::make('#')->getSearchResultsUsing(
    fn (string $search): array => Issue::where('title', 'like', "{$search}%")->limit(5)->pluck('title', 'id')->all(),
);
```

A mention is saved as a `span` with `data-type="mention"`, `data-id` and `data-label`.

## Custom blocks

`customBlocks()` adds a `customBlocks` button that inserts your own blocks, such as a hero or a call to action. A block extends `CustomBlock`.

```php
use Xelqun\Actions\Action;
use Xelqun\Forms\Components\RichEditor\CustomBlock;
use Xelqun\Forms\Components\TextInput;

class HeroBlock extends CustomBlock
{
    public static function getId(): string
    {
        return 'hero';
    }

    public static function getLabel(): string
    {
        return 'Hero';
    }

    public static function configureEditorAction(Action $action): Action
    {
        return $action->schema([
            TextInput::make('heading')->required(),
        ]);
    }

    public static function toPreviewHtml(array $config): string
    {
        return '<strong>'.e($config['heading'] ?? '').'</strong>';
    }

    public static function toHtml(array $config, array $data): string
    {
        return '<section class="hero">'.e($config['heading'] ?? '').'</section>';
    }
}

RichEditor::make('content')->customBlocks([HeroBlock::class]);
```

`configureEditorAction()` gives the block a modal of fields, asked for when it's inserted. The editor keeps only the block's id and those values. `toPreviewHtml()` is what the block looks like in the editor. The server checks a posted block's values against its modal's rules on every save, and drops a block that fails.

`RichContentRenderer::customBlocks()` draws each block's `toHtml()` on the page. Give it a class, or a class and the `$data` for its `toHtml()`. A block not named here is left out.

```php
RichContentRenderer::make($post->content)
    ->customBlocks([HeroBlock::class => ['theme' => 'dark']])
    ->toHtml();
```

> [!WARNING]
> Neither `toPreviewHtml()` nor `toHtml()` is cleaned, so escape every value from `$config`. Content saved some other way than through the editor wasn't checked.

## JSON

`json()` keeps Tiptap's JSON document instead of HTML. Cast the column to `array`.

```php
RichEditor::make('content')->json();
```

The server keeps only the nodes, marks and attributes the editor makes. File attachments are off in this mode.

## Attachments

```php
RichEditor::make('content')
    ->fileAttachments()
    ->fileAttachmentsDisk('public')
    ->fileAttachmentsDirectory('posts')
    ->fileAttachmentsMaxSize(2048);
```

`fileAttachmentsAcceptedFileTypes()` lists the MIME types. SVG can carry scripts, so it isn't one of the defaults.

## Sanitizing

The server sanitizes what's saved. `sanitizeUsing()` cleans it your own way: it gets `$html` and returns the result.

```php
RichEditor::make('content')->sanitizeUsing(fn (string $html): string => clean($html));
```

> [!WARNING]
> A sanitizer of your own replaces the built-in one, so anything it lets through reaches your pages.
