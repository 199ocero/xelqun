<script setup lang="ts">
import {
    ChevronLeft,
    ChevronRight,
    Pause,
    Play,
    RotateCcw,
} from 'lucide-vue-next';
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import { flows } from '@/components/docs/flows';
import type { FlowStep } from '@/components/docs/flows';
import { iconButtonClass } from '@/lib/classes';
import { cn } from '@/lib/utils';

const props = defineProps<{ name: string }>();

const STEP_MS = 2600;
const buttonClass = cn(
    iconButtonClass,
    'disabled:pointer-events-none disabled:opacity-40',
);

const flow = computed(() => flows[props.name] ?? null);
const lastStep = computed(() => (flow.value?.steps.length ?? 1) - 1);

// -1 is before the first step, so the first arrow animates in too.
const current = ref(-1);
const isPlaying = ref(false);
const element = ref<HTMLElement>();

let timer: ReturnType<typeof setTimeout> | undefined;
let observer: IntersectionObserver | undefined;

function prefersReducedMotion(): boolean {
    return window.matchMedia('(prefers-reduced-motion: reduce)').matches;
}

function go(step: number): void {
    current.value = Math.min(Math.max(step, 0), lastStep.value);
}

function play(): void {
    if (current.value >= lastStep.value) {
        current.value = -1;
    }

    isPlaying.value = true;
}

function pause(): void {
    isPlaying.value = false;
}

function step(by: number): void {
    pause();
    go(current.value + by);
}

watch([isPlaying, current], () => {
    clearTimeout(timer);

    if (!isPlaying.value) {
        return;
    }

    if (current.value >= lastStep.value) {
        isPlaying.value = false;

        return;
    }

    timer = setTimeout(
        () => go(current.value + 1),
        current.value === -1 ? 300 : STEP_MS,
    );
});

/**
 * Where an arrow starts and ends, as a span of lanes and the inset that puts
 * its ends in the middle of the first and last lane.
 */
function arrowStyle(item: FlowStep, index: number): Record<string, string> {
    const start = Math.min(item.from, item.to);
    const lanes = Math.abs(item.to - item.from) + 1;
    const inset = `${50 / lanes}%`;

    return {
        gridColumn: `${start + 1} / span ${lanes}`,
        // Its own row, or the grid would put a step beside the one before it.
        gridRow: `${index + 2}`,
        '--docs-flow-inset': inset,
    };
}

function state(index: number): 'past' | 'current' | 'future' {
    if (index === current.value) {
        return 'current';
    }

    return index < current.value ? 'past' : 'future';
}

// It plays when it comes into view, unless the reader asked for less motion.
onMounted(() => {
    if (prefersReducedMotion()) {
        current.value = 0;

        return;
    }

    observer = new IntersectionObserver(
        ([entry]) => {
            if (entry.isIntersecting) {
                observer?.disconnect();
                play();
            }
        },
        { root: document.querySelector('[scroll-region]'), threshold: 0.4 },
    );

    if (element.value) {
        observer.observe(element.value);
    }
});

onBeforeUnmount(() => {
    clearTimeout(timer);
    observer?.disconnect();
});
</script>

<template>
    <figure
        v-if="flow"
        ref="element"
        class="my-6 overflow-hidden rounded-xq-lg border border-xq-border bg-xq-card"
    >
        <figcaption
            class="flex items-center gap-2 border-b border-xq-border px-4 py-2"
        >
            <span class="min-w-0 flex-1 truncate text-sm font-medium">{{
                flow.title
            }}</span>
            <span class="text-xs text-xq-muted-foreground tabular-nums">
                {{ Math.max(current, 0) + 1 }} / {{ flow.steps.length }}
            </span>
            <div class="flex items-center">
                <button
                    type="button"
                    :class="buttonClass"
                    :disabled="current <= 0"
                    aria-label="Previous step"
                    @click="step(-1)"
                >
                    <ChevronLeft class="size-4" />
                </button>
                <button
                    type="button"
                    :class="buttonClass"
                    :aria-label="
                        isPlaying
                            ? 'Pause'
                            : current >= lastStep
                              ? 'Play again'
                              : 'Play'
                    "
                    @click="isPlaying ? pause() : play()"
                >
                    <Pause v-if="isPlaying" class="size-4" />
                    <RotateCcw v-else-if="current >= lastStep" class="size-4" />
                    <Play v-else class="size-4" />
                </button>
                <button
                    type="button"
                    :class="buttonClass"
                    :disabled="current >= lastStep"
                    aria-label="Next step"
                    @click="step(1)"
                >
                    <ChevronRight class="size-4" />
                </button>
            </div>
        </figcaption>

        <div class="relative px-2 pt-3 pb-2 sm:px-4">
            <!-- The lane lines, behind the steps. -->
            <div
                class="pointer-events-none absolute inset-x-2 inset-y-0 grid grid-cols-3 sm:inset-x-4"
                aria-hidden="true"
            >
                <div
                    v-for="lane in flow.lanes"
                    :key="lane"
                    class="mx-auto mt-10 w-px border-l border-dashed border-xq-border"
                ></div>
            </div>

            <div class="relative grid grid-cols-3 gap-x-2" aria-hidden="true">
                <div
                    v-for="lane in flow.lanes"
                    :key="lane"
                    class="mb-2 truncate rounded-xq-md border border-xq-border bg-xq-card px-2 py-1.5 text-center text-xs font-semibold"
                >
                    {{ lane }}
                </div>

                <div
                    v-for="(item, index) in flow.steps"
                    :key="index"
                    :data-state="state(index)"
                    :style="arrowStyle(item, index)"
                    :class="
                        cn(
                            'docs-flow-step relative flex min-h-11 flex-col items-center justify-center py-1 transition-opacity duration-(--xq-duration-slow) motion-reduce:transition-none',
                            state(index) === 'future' && 'opacity-0',
                            state(index) === 'past' && 'opacity-45',
                        )
                    "
                >
                    <template v-if="item.from === item.to">
                        <span
                            :class="
                                cn(
                                    'max-w-full rounded-full border px-2 py-0.5 text-center font-mono text-[0.6875rem] leading-snug wrap-break-word transition-colors duration-(--xq-duration-base)',
                                    state(index) === 'current'
                                        ? 'border-xq-primary bg-xq-primary text-xq-primary-foreground shadow-sm'
                                        : 'border-xq-border bg-xq-card',
                                )
                            "
                        >
                            {{ item.label }}
                        </span>
                    </template>
                    <template v-else>
                        <span
                            class="mb-1 max-w-full px-1 text-center font-mono text-[0.6875rem] leading-snug wrap-break-word"
                            :class="
                                state(index) === 'current' && 'text-xq-primary'
                            "
                        >
                            {{ item.label }}
                        </span>
                        <span
                            class="docs-flow-line"
                            :data-direction="
                                item.to > item.from ? 'right' : 'left'
                            "
                        >
                            <span
                                v-if="state(index) === 'current'"
                                :key="current"
                                class="docs-flow-dot motion-reduce:hidden"
                            ></span>
                        </span>
                    </template>
                </div>
            </div>
        </div>

        <p
            class="min-h-16 border-t border-xq-border px-4 py-3 text-sm leading-relaxed"
            aria-live="polite"
        >
            <template v-if="current >= 0">
                <span class="sr-only"
                    >Step {{ current + 1 }} of {{ flow.steps.length }}:
                    {{ flow.lanes[flow.steps[current].from] }} to
                    {{ flow.lanes[flow.steps[current].to] }},
                    {{ flow.steps[current].label }}.
                </span>
                {{ flow.steps[current].caption }}
            </template>
            <span v-else class="text-xq-muted-foreground"
                >Press play to watch it step by step.</span
            >
        </p>
    </figure>
</template>
