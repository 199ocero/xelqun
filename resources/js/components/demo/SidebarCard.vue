<script setup lang="ts">
import { BookOpen, GitBranch, Inbox } from 'lucide-vue-next';
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';

const TEN_MINUTES = 600_000;

defineProps<{ docsUrl: string; inboxUrl: string; githubUrl: string }>();

const remaining = ref(TEN_MINUTES);

const countdown = computed(() => {
    const seconds = Math.ceil(remaining.value / 1000);

    return `${Math.floor(seconds / 60)}:${String(seconds % 60).padStart(2, '0')}`;
});

let timer: ReturnType<typeof setInterval> | undefined;

/** The reset runs on the clock's ten minute marks. */
function tick(): void {
    remaining.value = TEN_MINUTES - (Date.now() % TEN_MINUTES);
}

onMounted(() => {
    tick();
    timer = setInterval(tick, 1000);
});
onBeforeUnmount(() => clearInterval(timer));
</script>

<template>
    <div class="@container">
        <section
            aria-label="Live demo"
            class="grid gap-2.5 rounded-xq-lg border border-xq-border bg-xq-card p-3 text-sm @max-[11rem]:hidden"
            data-demo-card
        >
            <div class="flex items-center gap-2">
                <span class="relative flex size-2" aria-hidden="true">
                    <span
                        class="absolute inset-0 animate-ping rounded-full bg-xq-success opacity-50 motion-reduce:hidden"
                    ></span>
                    <span
                        class="relative size-2 rounded-full bg-xq-success"
                    ></span>
                </span>
                <span class="font-medium">Live demo</span>
                <span
                    class="ml-auto text-xs text-xq-muted-foreground tabular-nums"
                >
                    resets in
                    <span class="font-medium text-xq-foreground">{{
                        countdown
                    }}</span>
                </span>
            </div>

            <div
                role="progressbar"
                aria-label="Time until the data resets"
                :aria-valuenow="Math.round((remaining / TEN_MINUTES) * 100)"
                aria-valuemin="0"
                aria-valuemax="100"
                class="h-1 overflow-hidden rounded-full bg-xq-muted"
            >
                <div
                    class="h-full rounded-full bg-xq-primary transition-[width] duration-1000 ease-linear"
                    :style="{ width: `${(remaining / TEN_MINUTES) * 100}%` }"
                ></div>
            </div>

            <p class="text-xs leading-relaxed text-xq-muted-foreground">
                Change anything you like. It all goes back when the timer ends.
            </p>

            <nav aria-label="Demo links" class="grid grid-cols-3 gap-1 text-xs">
                <a
                    v-for="link in [
                        { label: 'Docs', url: docsUrl, icon: BookOpen },
                        { label: 'Inbox', url: inboxUrl, icon: Inbox },
                        { label: 'GitHub', url: githubUrl, icon: GitBranch },
                    ]"
                    :key="link.label"
                    :href="link.url"
                    class="flex flex-col items-center gap-1 rounded-xq-sm py-1.5 text-xq-muted-foreground transition-[color,background-color,scale] duration-(--xq-duration-fast) hover:bg-xq-nav-hover hover:text-xq-foreground active:scale-95"
                >
                    <component
                        :is="link.icon"
                        class="size-3.5"
                        aria-hidden="true"
                    />
                    {{ link.label }}
                </a>
            </nav>
        </section>
    </div>
</template>
