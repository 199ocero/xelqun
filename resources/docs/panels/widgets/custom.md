---
title: Custom widgets
description: Render a widget with your own Vue component.
---

Extend `Widget`, then register a Vue component in `createXelqunForms({ components })`. `getType()` names the component, and `getData()` is what it gets.

```php
use Xelqun\Panels\Widgets\Widget;

class ServerStatus extends Widget
{
    protected static int $columnSpan = 6;

    protected function getType(): string
    {
        return 'app/server-status';
    }

    protected function getData(): array
    {
        return ['online' => true];
    }
}
```

```ts
app.use(
    createXelqunForms({
        components: {
            'app/server-status': () => import('./components/ServerStatus.vue'),
        },
    }),
);
```

`XqPanelWidgetCard` gives your component the card every other widget uses.
