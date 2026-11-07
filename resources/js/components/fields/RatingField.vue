<script setup lang="ts">
import { useField, XqFieldWrapper } from '@xelqun/forms';
import type { SchemaNode } from '@xelqun/forms';
import { Star } from 'lucide-vue-next';

const props = defineProps<{ node: SchemaNode }>();
const { value, error, required, disabled, id } = useField<number | null>(
    props.node,
);
</script>

<template>
    <XqFieldWrapper :id="id" :node="node" :error="error" :required="required">
        <div
            :id="id"
            class="flex gap-1"
            role="radiogroup"
            :aria-label="node.label ?? 'Rating'"
        >
            <button
                v-for="star in 5"
                :key="star"
                type="button"
                role="radio"
                :aria-checked="value === star"
                :aria-label="`${star} out of 5`"
                :disabled="disabled"
                class="cursor-pointer transition-transform duration-(--xq-duration-base) ease-(--xq-ease-spring) hover:scale-115 disabled:cursor-default"
                @click="value = star"
            >
                <Star
                    :class="[
                        'size-5',
                        (value ?? 0) >= star
                            ? 'fill-xq-warning text-xq-warning'
                            : 'text-xq-muted-foreground',
                    ]"
                    aria-hidden="true"
                />
            </button>
        </div>
    </XqFieldWrapper>
</template>
