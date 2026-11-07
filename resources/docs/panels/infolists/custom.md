---
title: Custom entries
description: Extend Entry to show a value your own way.
---

Extend `Entry`, and say which column it's built on.

```php
use Xelqun\Panels\Infolists\Entry;
use Xelqun\Tables\Columns\Column;
use Xelqun\Tables\Columns\TextColumn;

class RatingEntry extends Entry
{
    protected static function makeColumn(string $name): Column
    {
        return TextColumn::make($name)
            ->formatStateUsing(fn (?int $state): string => str_repeat('★', (int) $state));
    }
}
```

<!-- screenshot: entries | A star rating entry on a post -->

[See one in the demo](demo:/posts/1).
