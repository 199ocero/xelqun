---
title: Slider
description: Slider picks a number on a track, with a range, steps and a tooltip.
---

```php
use Xelqun\Forms\Components\Slider;

Slider::make('discount')
    ->range(0, 100)
    ->step(5)
    ->tooltips();
```

<!-- example: form-slider -->

| Method            | What it does                                                   |
| ----------------- | -------------------------------------------------------------- |
| `range()`         | The lowest and highest values.                                 |
| `step()`          | The value moves in these increments, counted from the minimum. |
| `decimalPlaces()` | Allows this many decimal places. It replaces `step()`.         |
| `tooltips()`      | Shows the value above the track.                               |
