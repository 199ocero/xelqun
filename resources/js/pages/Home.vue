<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import {
    ArrowUp,
    FileText,
    LayoutDashboard,
    Search,
    Sparkles,
    Table,
} from 'lucide-vue-next';
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import DocsSearch from '@/components/docs/DocsSearch.vue';
import GithubStars from '@/components/docs/GithubStars.vue';
import ThemeToggle from '@/components/docs/ThemeToggle.vue';
import XelqunMark from '@/components/docs/XelqunMark.vue';
import ProductPreview from '@/components/home/ProductPreview.vue';

defineProps<{
    packages: { label: string; tagline: string; url: string; pages: number }[];
}>();

const page = usePage();
const links = computed(
    () =>
        page.props.links as {
            demo: string;
            github: string;
            shareImage: string;
        },
);

const description =
    'Xelqun lets you define forms, tables and admin panels in PHP, and renders them with Inertia and Vue. A Filament-inspired stack for Laravel.';

const searchOpen = ref(false);

function onKeydown(event: KeyboardEvent): void {
    if ((event.metaKey || event.ctrlKey) && event.key.toLowerCase() === 'k') {
        event.preventDefault();
        searchOpen.value = true;
    }
}

onMounted(() => window.addEventListener('keydown', onKeydown));
onBeforeUnmount(() => window.removeEventListener('keydown', onKeydown));

const pillButton =
    'inline-flex items-center justify-center rounded-full bg-xq-foreground font-medium text-xq-background transition-[scale,opacity] duration-(--xq-duration-fast) hover:opacity-90 focus-visible:ring-2 focus-visible:ring-xq-ring focus-visible:ring-offset-2 focus-visible:ring-offset-xq-background focus-visible:outline-none active:scale-[0.97]';
</script>

<template>
    <div
        class="relative min-h-dvh overflow-x-clip bg-xq-background text-xq-foreground"
    >
        <Head title="Admin panels for Laravel, Inertia and Vue">
            <meta
                head-key="description"
                name="description"
                :content="description"
            />
            <meta head-key="og:title" property="og:title" content="Xelqun" />
            <meta
                head-key="og:description"
                property="og:description"
                :content="description"
            />
            <meta
                head-key="og:image"
                property="og:image"
                :content="links.shareImage"
            />
        </Head>

        <div class="site-glow" aria-hidden="true"></div>

        <div class="relative mx-auto max-w-6xl px-4 pt-4 sm:px-6">
            <header
                class="sticky top-4 z-20 flex h-14 items-center gap-2 rounded-full border border-xq-border bg-xq-card/80 pr-2 pl-5 backdrop-blur-md"
            >
                <Link href="/" class="flex items-center gap-2.5 font-semibold">
                    <XelqunMark class="size-6.5" />
                    Xelqun
                </Link>
                <nav
                    aria-label="Main"
                    class="absolute left-1/2 hidden -translate-x-1/2 items-center gap-7 text-sm text-xq-muted-foreground md:flex"
                >
                    <Link
                        href="/"
                        class="font-medium text-xq-foreground"
                        aria-current="page"
                    >
                        Home
                    </Link>
                    <Link
                        href="/introduction"
                        prefetch
                        class="transition-colors duration-(--xq-duration-fast) hover:text-xq-foreground"
                    >
                        Docs
                    </Link>
                    <Link
                        v-for="item in packages"
                        :key="item.url"
                        :href="item.url"
                        prefetch
                        class="transition-colors duration-(--xq-duration-fast) hover:text-xq-foreground"
                    >
                        {{ item.label }}
                    </Link>
                </nav>
                <div class="ml-auto flex items-center gap-1.5">
                    <GithubStars />
                    <ThemeToggle />
                    <a
                        :href="links.demo"
                        :class="[pillButton, 'h-10 px-5 text-sm']"
                    >
                        Open the demo
                    </a>
                </div>
            </header>

            <main data-xq-page>
                <section
                    class="mx-auto max-w-4xl pt-16 pb-12 text-center sm:pt-24 sm:pb-16"
                >
                    <p
                        class="inline-flex items-center gap-2 text-sm font-medium"
                    >
                        <Sparkles
                            class="size-4 text-xq-primary"
                            aria-hidden="true"
                        />
                        Forms, tables and panels for Laravel
                    </p>
                    <h1
                        class="mt-4 text-[2.75rem] leading-[1.05] font-medium tracking-[-0.04em] text-balance sm:text-7xl"
                    >
                        Build admin panels in PHP, render them in Vue
                    </h1>
                    <p
                        class="mx-auto mt-6 max-w-2xl text-lg leading-relaxed text-pretty text-xq-muted-foreground"
                    >
                        Define forms, tables and full admin panels in PHP.
                        Xelqun renders them with Inertia and Vue, with auth,
                        CRUD and dashboards included.
                    </p>
                    <div
                        class="mt-9 flex flex-wrap items-center justify-center gap-x-3 gap-y-2"
                    >
                        <a
                            :href="links.demo"
                            :class="[
                                pillButton,
                                'h-13 px-7 text-base shadow-lg',
                            ]"
                        >
                            Open the live demo
                        </a>
                        <Link
                            href="/introduction"
                            class="inline-flex h-13 items-center rounded-full px-6 text-base font-medium transition-colors duration-(--xq-duration-fast) hover:bg-xq-nav-hover"
                        >
                            Read the docs
                        </Link>
                    </div>
                </section>

                <section aria-label="What's in Xelqun" class="relative pb-10">
                    <ProductPreview
                        :packages="packages"
                        :demo-url="links.demo"
                        :github-url="links.github"
                    />

                    <div
                        class="absolute inset-x-0 bottom-12 z-10 mx-auto w-full max-w-3xl px-4"
                    >
                        <button
                            type="button"
                            class="group w-full cursor-pointer rounded-xq-xl border border-xq-border bg-xq-card p-3 text-left shadow-xl transition-[border-color,scale] duration-(--xq-duration-base) ease-(--xq-ease-out) hover:border-xq-input focus-visible:ring-2 focus-visible:ring-xq-ring focus-visible:outline-none active:scale-[0.995]"
                            @click="searchOpen = true"
                        >
                            <span
                                class="block px-1 pt-1 pb-6 text-base text-xq-muted-foreground"
                            >
                                Search fields, columns and more…
                            </span>
                            <span class="flex items-center justify-between">
                                <span
                                    class="flex gap-0.5 rounded-xq-md bg-xq-nav-hover p-1 text-xq-muted-foreground"
                                    aria-hidden="true"
                                >
                                    <span
                                        class="flex size-7 items-center justify-center rounded-xq-sm bg-xq-card text-xq-foreground shadow-xs"
                                    >
                                        <Search class="size-3.5" />
                                    </span>
                                    <span
                                        class="flex size-7 items-center justify-center"
                                    >
                                        <FileText class="size-3.5" />
                                    </span>
                                    <span
                                        class="flex size-7 items-center justify-center"
                                    >
                                        <Table class="size-3.5" />
                                    </span>
                                    <span
                                        class="flex size-7 items-center justify-center"
                                    >
                                        <LayoutDashboard class="size-3.5" />
                                    </span>
                                </span>
                                <span
                                    class="flex items-center gap-2 text-xs text-xq-muted-foreground"
                                >
                                    <kbd class="font-sans">⌘K</kbd>
                                    <span
                                        class="flex size-9 items-center justify-center rounded-xq-md bg-xq-nav-hover transition-[scale] duration-(--xq-duration-base) ease-(--xq-ease-spring) group-hover:scale-105"
                                        aria-hidden="true"
                                    >
                                        <ArrowUp class="size-4" />
                                    </span>
                                </span>
                            </span>
                        </button>
                    </div>
                </section>
            </main>
        </div>

        <DocsSearch v-model:open="searchOpen" />
    </div>
</template>
