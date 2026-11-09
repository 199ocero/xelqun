<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { ArrowLeft, ArrowRight } from 'lucide-vue-next';
import { computed } from 'vue';
import DocsExample from '@/components/docs/DocsExample.vue';
import DocsFlow from '@/components/docs/DocsFlow.vue';
import DocsToc from '@/components/docs/DocsToc.vue';
import DocsLayout from '@/layouts/DocsLayout.vue';
import type { DocsDocument, DocsShell } from '@/types/docs';

defineOptions({ layout: DocsLayout });
const props = defineProps<{ document: DocsDocument; docs: DocsShell }>();

const page = usePage();
const shareImage = (page.props.links as { shareImage: string }).shareImage;
const title = computed(() => `${props.document.title} - ${props.docs.label}`);
</script>

<template>
    <div class="mx-auto flex max-w-6xl gap-12 px-5 py-8 sm:px-8 lg:py-10">
        <Head :title="title">
            <meta
                head-key="description"
                name="description"
                :content="document.description"
            />
            <link
                head-key="canonical"
                rel="canonical"
                :href="document.canonical"
            />
            <meta head-key="og:type" property="og:type" content="article" />
            <meta head-key="og:title" property="og:title" :content="title" />
            <meta
                head-key="og:description"
                property="og:description"
                :content="document.description"
            />
            <meta
                head-key="og:url"
                property="og:url"
                :content="document.canonical"
            />
            <meta
                head-key="og:image"
                property="og:image"
                :content="shareImage"
            />
            <meta
                head-key="twitter:card"
                name="twitter:card"
                content="summary_large_image"
            />
        </Head>

        <article :key="document.canonical" class="min-w-0 flex-1" data-xq-page>
            <header class="mb-8">
                <p class="mb-2 text-sm font-medium text-xq-primary">
                    {{ document.group }}
                </p>
                <h1 class="text-3xl font-bold tracking-tight text-balance">
                    {{ document.title }}
                </h1>
                <p
                    class="mt-3 text-base leading-relaxed text-xq-muted-foreground"
                >
                    {{ document.description }}
                </p>
            </header>

            <template v-for="(block, index) in document.blocks" :key="index">
                <div
                    v-if="block.type === 'html'"
                    class="docs-prose"
                    v-html="block.html"
                ></div>
                <DocsFlow
                    v-else-if="block.type === 'flow'"
                    :name="block.name"
                />
                <DocsExample v-else :name="block.name" />
            </template>

            <nav
                v-if="document.previous || document.next"
                aria-label="Pagination"
                class="mt-12 grid gap-3 border-t border-xq-border pt-6 sm:grid-cols-2"
            >
                <Link
                    v-if="document.previous"
                    :href="document.previous.url"
                    class="group rounded-xq-lg border border-xq-border bg-xq-card p-4 transition-[scale] duration-(--xq-duration-fast) active:scale-[0.98]"
                >
                    <span
                        class="flex items-center gap-1 text-xs text-xq-muted-foreground"
                    >
                        <ArrowLeft
                            class="size-3.5 transition-transform duration-(--xq-duration-base) ease-(--xq-ease-spring) group-hover:-translate-x-0.5"
                            aria-hidden="true"
                        />
                        Previous
                    </span>
                    <span class="mt-1 block text-sm font-medium">{{
                        document.previous.label
                    }}</span>
                </Link>
                <Link
                    v-if="document.next"
                    :href="document.next.url"
                    class="group rounded-xq-lg border border-xq-border bg-xq-card p-4 text-right transition-[scale] duration-(--xq-duration-fast) active:scale-[0.98] sm:col-start-2"
                >
                    <span
                        class="flex items-center justify-end gap-1 text-xs text-xq-muted-foreground"
                    >
                        Next
                        <ArrowRight
                            class="size-3.5 transition-transform duration-(--xq-duration-base) ease-(--xq-ease-spring) group-hover:translate-x-0.5"
                            aria-hidden="true"
                        />
                    </span>
                    <span class="mt-1 block text-sm font-medium">{{
                        document.next.label
                    }}</span>
                </Link>
            </nav>
        </article>

        <aside class="hidden w-52 shrink-0 xl:block">
            <div class="sticky top-0">
                <DocsToc :headings="document.headings" />
            </div>
        </aside>
    </div>
</template>
