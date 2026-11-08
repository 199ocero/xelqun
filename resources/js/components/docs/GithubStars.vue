<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { Github, Star } from 'lucide-vue-next';
import { computed } from 'vue';

const page = usePage();
const links = computed(
    () => page.props.links as { github: string; githubStars: number | false },
);
const stars = computed(() =>
    links.value.githubStars === false
        ? null
        : new Intl.NumberFormat('en', { notation: 'compact' }).format(
              links.value.githubStars,
          ),
);
</script>

<template>
    <a
        :href="links.github"
        target="_blank"
        rel="noopener"
        :aria-label="stars ? `GitHub, ${stars} stars` : 'GitHub'"
        class="inline-flex h-8 shrink-0 items-center gap-1.5 rounded-full px-2.5 text-[0.8125rem] font-medium text-xq-muted-foreground transition-[color,background-color,scale] duration-(--xq-duration-fast) hover:bg-xq-nav-hover hover:text-xq-foreground focus-visible:ring-2 focus-visible:ring-xq-ring focus-visible:outline-none active:scale-[0.97]"
    >
        <Github class="size-4" aria-hidden="true" />
        <template v-if="stars">
            <Star class="size-3.5" aria-hidden="true" />
            <span class="tabular-nums">{{ stars }}</span>
        </template>
    </a>
</template>
