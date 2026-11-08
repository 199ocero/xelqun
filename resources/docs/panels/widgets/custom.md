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

Every key of `getData()` arrives as a prop. So do `heading`, when the widget sets `$heading`, and `seeAllUrl`, when `getSeeAllUrl()` returns one. They're left out otherwise.

`XqPanelWidgetCard` gives your component the card every other widget uses. Pass the heading and link on to it:

```vue
<script setup lang="ts">
import { XqPanelWidgetCard } from '@xelqun/panels';

defineProps<{ online: boolean; heading?: string; seeAllUrl?: string }>();
</script>

<template>
    <XqPanelWidgetCard :heading="heading ?? 'Server'" :see-all-url="seeAllUrl">
        <p class="px-5 pb-4">{{ online ? 'Online' : 'Offline' }}</p>
    </XqPanelWidgetCard>
</template>
```
