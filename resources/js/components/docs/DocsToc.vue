<script setup lang="ts">
import { onBeforeUnmount, onMounted, ref, watch } from 'vue';
import type { DocsHeading } from '@/types/docs';

const props = defineProps<{ headings: DocsHeading[] }>();

const current = ref<string | null>(null);

let observer: IntersectionObserver | undefined;

/** The heading nearest the top of the reading area is the one you're on. */
function observe(): void {
    observer?.disconnect();

    const root = document.querySelector('[scroll-region]');

    observer = new IntersectionObserver(
        (entries) => {
            for (const entry of entries) {
                if (entry.isIntersecting) {
                    current.value = entry.target.id;
                }
            }
        },
        { root, rootMargin: '0px 0px -75% 0px' },
    );

    for (const heading of props.headings) {
        const element = document.getElementById(heading.id);

        if (element) {
            observer.observe(element);
        }
    }
}

onMounted(observe);
onBeforeUnmount(() => observer?.disconnect());
watch(() => props.headings, observe, { flush: 'post' });
</script>

<template>
    <nav v-if="headings.length > 0" aria-label="On this page">
        <p class="mb-2 text-xs font-medium text-xq-muted-foreground">
            On this page
        </p>
        <ul class="grid gap-1 border-l border-xq-border text-[0.8125rem]">
            <li v-for="heading in headings" :key="heading.id">
                <a
                    :href="`#${heading.id}`"
                    :aria-current="current === heading.id ? 'true' : undefined"
                    :class="[
                        '-ml-px block border-l py-0.5 transition-[color,border-color] duration-(--xq-duration-base)',
                        heading.level === 3 ? 'pl-6' : 'pl-3',
                        current === heading.id
                            ? 'border-xq-primary text-xq-foreground'
                            : 'border-transparent text-xq-muted-foreground hover:text-xq-foreground',
                    ]"
                >
                    {{ heading.text }}
                </a>
            </li>
        </ul>
    </nav>
</template>
