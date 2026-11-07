<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { XqActions, XqForm } from '@xelqun/forms';
import type { FormSchema } from '@xelqun/forms';
import { XqTable } from '@xelqun/tables';
import type { TableSchema } from '@xelqun/tables';
import { onBeforeUnmount, onMounted, ref } from 'vue';

const props = defineProps<{ name: string }>();

type Example = {
    kind: 'form' | 'actions' | 'table';
    title: string;
    description: string;
    schema: FormSchema & TableSchema;
    secondSchema?: TableSchema | null;
    source: string | null;
    submits: boolean;
};

const element = ref<HTMLElement>();
const example = ref<Example | null>(null);
const hasFailed = ref(false);
const showsCode = ref(false);

let observer: IntersectionObserver | undefined;

async function load(): Promise<void> {
    try {
        const response = await fetch(`/examples/${props.name}`, {
            headers: { Accept: 'application/json' },
        });

        if (!response.ok) {
            throw new Error(`Example failed with ${response.status}`);
        }

        example.value = await response.json();
    } catch {
        hasFailed.value = true;
    }
}

// An action reloads the page, and only this holds what it changed.
const stopListening = router.on('success', () => {
    if (example.value?.kind === 'actions') {
        void load();
    }
});

// Examples load as they near the screen, so a long page stays quick.
onMounted(() => {
    observer = new IntersectionObserver(
        ([entry]) => {
            if (entry.isIntersecting) {
                observer?.disconnect();
                void load();
            }
        },
        {
            root: document.querySelector('[scroll-region]'),
            rootMargin: '300px',
        },
    );

    if (element.value) {
        observer.observe(element.value);
    }
});

onBeforeUnmount(() => {
    observer?.disconnect();
    stopListening();
});
</script>

<template>
    <figure
        ref="element"
        class="my-6 overflow-hidden rounded-xq-lg border border-xq-border bg-xq-card"
    >
        <template v-if="example">
            <figcaption
                class="flex items-center gap-3 border-b border-xq-border px-4 py-2.5"
            >
                <span class="min-w-0 flex-1 truncate text-sm font-medium">{{
                    example.title
                }}</span>
                <div
                    v-if="example.source"
                    class="flex gap-0.5 rounded-full bg-xq-nav-hover p-0.5 text-xs"
                    role="group"
                    aria-label="View"
                >
                    <button
                        v-for="tab in [
                            { code: false, label: 'Preview' },
                            { code: true, label: 'Code' },
                        ]"
                        :key="tab.label"
                        type="button"
                        :aria-pressed="showsCode === tab.code"
                        :class="[
                            'cursor-pointer rounded-full px-2.5 py-0.5 font-medium transition-[background-color,color] duration-(--xq-duration-fast)',
                            showsCode === tab.code
                                ? 'bg-xq-card text-xq-foreground shadow-xs'
                                : 'text-xq-muted-foreground',
                        ]"
                        @click="showsCode = tab.code"
                    >
                        {{ tab.label }}
                    </button>
                </div>
            </figcaption>
            <div
                v-if="showsCode"
                class="docs-example-code max-h-112 overflow-auto"
                v-html="example.source ?? ''"
            ></div>
            <div v-show="!showsCode" class="animate-xq-fade-in p-4 sm:p-5">
                <XqTable
                    v-if="example.kind === 'table'"
                    :table="example.schema"
                />
                <XqTable
                    v-if="example.secondSchema"
                    :table="example.secondSchema"
                    class="mt-6"
                />
                <XqActions
                    v-else-if="example.kind === 'actions'"
                    :actions="example.schema"
                />
                <XqForm
                    v-else-if="example.kind === 'form'"
                    :form="example.schema"
                    :action="example.submits ? `/examples/${name}` : undefined"
                    submit-label="Check it"
                />
            </div>
        </template>
        <p
            v-else-if="hasFailed"
            class="p-6 text-center text-sm text-xq-muted-foreground"
        >
            This example couldn't load.
        </p>
        <div
            v-else
            class="xq-skeleton h-48 animate-xq-skeleton"
            aria-hidden="true"
        ></div>
    </figure>
</template>
