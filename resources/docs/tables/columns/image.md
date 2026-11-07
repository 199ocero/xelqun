---
title: Image
description: ImageColumn shows an image from a URL or a disk.
---

```php
use Xelqun\Tables\Columns\ImageColumn;

ImageColumn::make('image')->disk('public')->size(40)->circular();
```

<!-- example: table-image -->

| Method       | What it does                                                  |
| ------------ | ------------------------------------------------------------- |
| `disk()`     | The disk the path is on. Without one, the value is a URL.     |
| `size()`     | The size in pixels.                                           |
| `circular()` | Crops it to a circle.                                         |
| `alt()`      | What a screen reader says. It defaults to the column's label. |
