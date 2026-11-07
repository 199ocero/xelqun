---
title: Markdown editor
description: MarkdownEditor edits Markdown with a toolbar, attachments and a preview rendered on the server.
---

```php
use Xelqun\Forms\Components\MarkdownEditor;

MarkdownEditor::make('body')->minHeight('12rem')->maxHeight('32rem');
```

<!-- example: form-markdown-editor -->

The server renders the preview.

## Toolbar

```php
MarkdownEditor::make('body')->toolbarButtons([['bold', 'italic'], ['link']]);

MarkdownEditor::make('body')->disableToolbarButtons(['attachFiles']);
```

## Attachments

The same options as the [rich editor](/forms/fields/rich-editor#attachments): `fileAttachments()`, `fileAttachmentsDisk()`, `fileAttachmentsDirectory()`, `fileAttachmentsMaxSize()` and `fileAttachmentsAcceptedFileTypes()`.

## Showing it later

`render()` turns Markdown into HTML that's safe to print.

```php
use Xelqun\Forms\Components\MarkdownEditor;

$html = MarkdownEditor::render($post->body);
```
