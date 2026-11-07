---
title: Color picker
description: ColorPicker picks a color and stores it as hex, RGB, RGBA or HSL.
---

```php
use Xelqun\Forms\Components\ColorPicker;

ColorPicker::make('brand_color')->hex();
```

<!-- example: form-color-picker -->

| Method   | Stored as              |
| -------- | ---------------------- |
| `hex()`  | `#0071e3`              |
| `rgb()`  | `rgb(0, 113, 227)`     |
| `rgba()` | `rgba(0, 113, 227, 1)` |
| `hsl()`  | `hsl(210, 100%, 45%)`  |
