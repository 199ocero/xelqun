---
title: Code editor
description: CodeEditor edits code with syntax highlighting, and loads each language only when it's needed.
---

```php
use Xelqun\Forms\Components\CodeEditor;
use Xelqun\Forms\Components\CodeEditor\Language;

CodeEditor::make('snippet')
    ->language(Language::Php)
    ->minHeight('14rem')
    ->wrap();
```

<!-- example: form-code-editor -->

`language()` takes a `Language` case, and each language loads when a field needs it. `wrap()` wraps long lines.
