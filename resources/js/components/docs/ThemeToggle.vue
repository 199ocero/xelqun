<script setup lang="ts">
import { Moon, Sun } from 'lucide-vue-next';
import { iconButtonClass } from '@/lib/classes';

/** The browser fades the whole page across, where it can, as a panel does. */
function toggle(): void {
    const root = document.documentElement;
    const change = (): void => {
        const isDark = root.classList.toggle('dark');

        try {
            localStorage.setItem('xq-panel-theme', isDark ? 'dark' : 'light');
        } catch {
            // Storage can be blocked. The choice still holds for this visit.
        }
    };
    const isReduced = window.matchMedia(
        '(prefers-reduced-motion: reduce)',
    ).matches;

    if ('startViewTransition' in document && !isReduced) {
        document.startViewTransition(change);
    } else {
        change();
    }
}
</script>

<template>
    <button
        type="button"
        :class="iconButtonClass"
        aria-label="Switch theme"
        @click="toggle"
    >
        <Sun class="hidden size-4 dark:block" aria-hidden="true" />
        <Moon class="size-4 dark:hidden" aria-hidden="true" />
    </button>
</template>
