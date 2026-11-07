<script setup lang="ts">
import { Check, Copy, Terminal } from 'lucide-vue-next';
import { ref } from 'vue';

const COMMAND = 'composer require xelqun/panels';
const COPIED_FOR_MS = 1600;

const copied = ref(false);

async function copy(): Promise<void> {
    await navigator.clipboard.writeText(COMMAND);
    copied.value = true;
    setTimeout(() => (copied.value = false), COPIED_FOR_MS);
}
</script>

<template>
    <div
        class="flex min-w-0 items-center gap-3 rounded-xq-lg border border-xq-border bg-xq-card p-3"
    >
        <span
            class="flex size-9 shrink-0 items-center justify-center rounded-xq-md bg-[color-mix(in_oklab,var(--xq-primary)_12%,transparent)] text-xq-primary"
        >
            <Terminal class="size-4.5" aria-hidden="true" />
        </span>
        <div class="min-w-0 flex-1">
            <p class="text-xs text-xq-muted-foreground">Install in one line</p>
            <code class="block truncate text-[0.8125rem] font-medium">{{
                COMMAND
            }}</code>
        </div>
        <button
            type="button"
            :aria-label="copied ? 'Copied' : 'Copy the command'"
            class="flex size-8 shrink-0 cursor-pointer items-center justify-center rounded-full text-xq-muted-foreground transition-[color,background-color,scale] duration-(--xq-duration-fast) hover:bg-xq-nav-hover hover:text-xq-foreground focus-visible:ring-2 focus-visible:ring-xq-ring focus-visible:outline-none active:scale-90"
            @click="copy"
        >
            <Check
                v-if="copied"
                class="size-4 animate-xq-badge-pop text-xq-success"
                aria-hidden="true"
            />
            <Copy v-else class="size-4" aria-hidden="true" />
        </button>
    </div>
</template>
