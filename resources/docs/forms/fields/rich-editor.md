---
title: Rich editor
description: RichEditor edits formatted text and stores clean HTML, with toolbars, attachments and sanitizing.
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
