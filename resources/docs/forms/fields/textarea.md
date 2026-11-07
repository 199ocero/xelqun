---
title: Textarea
description: Textarea takes longer plain text, with a set height and a length limit.
---

```php
use Xelqun\Forms\Components\Textarea;

Textarea::make('notes')->rows(6)->maxLength(2000);
```

<!-- example: form-textarea -->

| Method        | What it does                                                 |
| ------------- | ------------------------------------------------------------ |
| `rows()`      | The height, in lines.                                        |
| `maxLength()` | The most characters allowed. It's checked on the server too. |

For formatted text, use the [rich editor](/forms/fields/rich-editor) or the [Markdown editor](/forms/fields/markdown-editor).
