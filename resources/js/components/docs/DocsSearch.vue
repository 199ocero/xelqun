<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { Search } from 'lucide-vue-next';
import {
    DialogContent,
    DialogDescription,
    DialogOverlay,
    DialogPortal,
    DialogRoot,
    DialogTitle,
} from 'reka-ui';
import { computed, ref, watch } from 'vue';
import type { DocsSearchResult } from '@/types/docs';

const DEBOUNCE_MS = 200;
const MIN_SEARCH_LENGTH = 2;

const props = defineProps<{ package?: string }>();
const open = defineModel<boolean>('open', { default: false });

const query = ref('');
const matches = ref<DocsSearchResult[]>([]);
const suggestions = ref<DocsSearchResult[]>([]);
const highlighted = ref(0);
const isSearching = ref(false);
const hasFailed = ref(false);

const term = computed(() => query.value.trim());
const results = computed(() =>
    term.value === '' ? suggestions.value : matches.value,
);

let debounce: ReturnType<typeof setTimeout> | undefined;
let request: AbortController | undefined;

async function fetchResults(params: string): Promise<DocsSearchResult[]> {
    const current = (request = new AbortController());
    const response = await fetch(`/search?${params}`, {
        headers: { Accept: 'application/json' },
        signal: current.signal,
    });

    if (!response.ok) {
        throw new Error(`Search failed with ${response.status}`);
    }

    return (await response.json()).results;
}

async function search(value: string): Promise<void> {
    try {
        matches.value = await fetchResults(`q=${encodeURIComponent(value)}`);
        isSearching.value = false;
    } catch (error) {
        if ((error as Error).name !== 'AbortError') {
            hasFailed.value = true;
            isSearching.value = false;
        }
    }
}

// Old matches go right away, so Enter can't open one that no longer fits what was typed.
watch(query, () => {
    clearTimeout(debounce);
    request?.abort();
    matches.value = [];
    highlighted.value = 0;
    hasFailed.value = false;
    isSearching.value = term.value.length >= MIN_SEARCH_LENGTH;

    if (isSearching.value) {
        const value = term.value;

        debounce = setTimeout(() => void search(value), DEBOUNCE_MS);
    }
});

watch(open, async (isOpen) => {
    if (!isOpen) {
        query.value = '';

        return;
    }

    highlighted.value = 0;

    try {
        suggestions.value = await fetchResults(
            `package=${encodeURIComponent(props.package ?? '')}`,
        );
    } catch {
        // The box still searches without them.
    }
});

function visit(result: DocsSearchResult): void {
    open.value = false;
    router.visit(result.url);
}

function onKeydown(event: KeyboardEvent): void {
    const count = results.value.length;

    if (count === 0) {
        return;
    }

    if (event.key === 'ArrowDown' || event.key === 'ArrowUp') {
        event.preventDefault();
        highlighted.value =
            (highlighted.value + (event.key === 'ArrowDown' ? 1 : -1) + count) %
            count;
    } else if (event.key === 'Enter') {
        event.preventDefault();
        visit(results.value[highlighted.value]);
    }
}
</script>

<template>
    <DialogRoot v-model:open="open">
        <DialogPortal>
            <DialogOverlay
                class="fixed inset-0 z-50 bg-black/40 data-[state=closed]:animate-xq-fade-out data-[state=open]:animate-xq-fade-in"
            />
            <DialogContent
                class="fixed top-[15%] left-1/2 z-50 w-[calc(100%-2rem)] max-w-lg -translate-x-1/2 rounded-xq-xl border border-xq-border bg-xq-popover text-xq-popover-foreground shadow-xl data-[state=closed]:animate-xq-dialog-out data-[state=open]:animate-xq-dialog-in"
            >
                <DialogTitle class="sr-only">Search the docs</DialogTitle>
                <DialogDescription class="sr-only">
                    Results appear as you type.
                </DialogDescription>

                <div
                    class="flex items-center gap-2 border-b border-xq-border px-4"
                >
                    <Search
                        class="size-4 text-xq-muted-foreground"
                        aria-hidden="true"
                    />
                    <input
                        v-model="query"
                        type="search"
                        placeholder="Search the docs"
                        aria-label="Search the docs"
                        class="w-full bg-transparent py-3 text-sm outline-none"
                        @keydown="onKeydown"
                    />
                </div>

                <div
                    v-if="results.length > 0"
                    class="max-h-96 overflow-y-auto p-1.5"
                >
                    <p
                        v-if="term === ''"
                        class="px-2.5 pt-1.5 pb-1 text-xs font-medium text-xq-muted-foreground"
                    >
                        Suggested
                    </p>
                    <ul>
                        <li
                            v-for="(result, index) in results"
                            :key="result.url"
                            class="animate-xq-rise-in"
                        >
                            <button
                                type="button"
                                :class="[
                                    'block w-full cursor-pointer rounded-xq-sm px-2.5 py-2 text-left text-sm',
                                    index === highlighted && 'bg-xq-nav-hover',
                                ]"
                                @mouseenter="highlighted = index"
                                @click="visit(result)"
                            >
                                <span class="block truncate">
                                    {{ result.section || result.title }}
                                </span>
                                <span
                                    class="block truncate text-xs text-xq-muted-foreground"
                                >
                                    {{ result.package
                                    }}<template v-if="result.section">
                                        · {{ result.title }}</template
                                    >
                                </span>
                            </button>
                        </li>
                    </ul>
                </div>
                <p
                    v-else-if="hasFailed"
                    class="p-6 text-center text-sm text-xq-muted-foreground"
                >
                    Search isn't available right now.
                </p>
                <p
                    v-else-if="isSearching"
                    class="p-6 text-center text-sm text-xq-muted-foreground"
                >
                    Searching…
                </p>
                <p
                    v-else-if="term.length >= MIN_SEARCH_LENGTH"
                    class="p-6 text-center text-sm text-xq-muted-foreground"
                >
                    No results for "{{ term }}".
                </p>
            </DialogContent>
        </DialogPortal>
    </DialogRoot>
</template>
