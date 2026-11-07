---
title: Widgets on resource pages
description: Show widgets above a list, or above a record.
---

`getHeaderWidgets()` shows widgets above a resource's list.

```php
public static function getHeaderWidgets(): array
{
    return [OrderStats::class];
}
```

<!-- screenshot: resource-widgets | Stats above the orders list -->

`getRecordWidgets()` shows them above a record, where a widget reads it from `$this->record`.

See [dashboards](/panels/widgets/overview) for what a widget can be. [See it in the demo](demo:/orders).
