---
title: Custom components
description: Replace how a field renders, add icons, and set up your own Vue components.
---

## Your own component for a type

The `components` option of `createXelqunForms()` always wins, so you can restyle a built-in field with no flag.

```ts
app.use(
    createXelqunForms({
        components: {
            select: () => import('./components/FancySelect.vue'),
        },
    }),
);
```

## Writing a field's Vue component

`useField()` binds the component to the form, and `XqFieldWrapper` gives it the label, hint, helper text and error.

```vue
<script setup lang="ts">
import { useField, XqFieldWrapper } from '@xelqun/forms';
import type { SchemaNode } from '@xelqun/forms';

const props = defineProps<{ node: SchemaNode }>();
const { value, error, required, disabled, id } = useField<number | null>(
    props.node,
);
</script>

<template>
    <XqFieldWrapper :node="node" :id="id" :error="error" :required="required">
        <input
            :id="id"
            v-model.number="value"
            type="number"
            :disabled="disabled"
        />
    </XqFieldWrapper>
</template>
```

`useField()` also returns `hint`, the note beside the label, and `helperText`, the text under the field. `describedBy` is the id for `aria-describedby`: the error's when there is one, otherwise the helper text's, which ends in `-helper`. `showsRequired` says whether the label shows an asterisk, which `markAsRequired()` can change.

> [!NOTE]
> Before 0.9, the text under a field was `hint`. A plugin field that shows it should read `helperText` now.

## Commands

| Command                      | What it does                                 |
| ---------------------------- | -------------------------------------------- |
| `xelqun-forms:install`       | Sets up the frontend.                        |
| `xelqun-forms:plugin`        | Installs a plugin.                           |
| `make:xelqun-field`          | Creates a field class and its Vue component. |
| `xelqun-forms:prune-uploads` | Deletes expired temporary uploads.           |

## UI components

`@xelqun/forms` exports the pieces it's built from, such as `XqButton`, `XqBadge`, `XqCard`, `XqDialog`, `XqInput`, `XqSelect`, `XqTabs` and `XqIcon`, so your own pages can match.
