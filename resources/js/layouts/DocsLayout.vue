<script setup lang="ts">
import { Link, router, usePage } from '@inertiajs/vue3';
import { Menu, Search } from 'lucide-vue-next';
import {
    DialogContent,
    DialogDescription,
    DialogOverlay,
    DialogPortal,
    DialogRoot,
    DialogTitle,
} from 'reka-ui';
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import DocsSearch from '@/components/docs/DocsSearch.vue';
import DocsSidebar from '@/components/docs/DocsSidebar.vue';
import ThemeToggle from '@/components/docs/ThemeToggle.vue';
import { iconButtonClass } from '@/lib/classes';
import type { DocsDocument, DocsShell } from '@/types/docs';

const page = usePage();
const docs = computed(() => page.props.docs as DocsShell);
const documentData = computed(() => page.props.document as DocsDocument);
const demoUrl = computed(() => (page.props.links as { demo: string }).demo);

const drawerOpen = ref(false);
const searchOpen = ref(false);

function onKeydown(event: KeyboardEvent): void {
    if ((event.metaKey || event.ctrlKey) && event.key.toLowerCase() === 'k') {
        event.preventDefault();
        searchOpen.value = true;
    }
}

const stopListening = router.on('navigate', () => {
    drawerOpen.value = false;
});

onMounted(() => window.addEventListener('keydown', onKeydown));
onBeforeUnmount(() => {
    stopListening();
    window.removeEventListener('keydown', onKeydown);
});
</script>

<template>
    <div class="flex h-dvh overflow-hidden bg-xq-shell text-xq-foreground">
        <aside class="hidden w-70 shrink-0 lg:block">
            <DocsSidebar :docs="docs" />
        </aside>

        <DialogRoot v-model:open="drawerOpen">
            <DialogPortal>
                <DialogOverlay
                    class="fixed inset-0 z-40 bg-black/40 data-[state=closed]:animate-xq-fade-out data-[state=open]:animate-xq-fade-in lg:hidden"
                />
                <DialogContent
                    class="fixed inset-y-0 left-0 z-50 w-72 bg-xq-shell outline-none data-[state=closed]:animate-xq-drawer-out data-[state=open]:animate-xq-drawer-in lg:hidden"
                >
                    <DialogTitle class="sr-only">Navigation</DialogTitle>
                    <DialogDescription class="sr-only">
                        {{ docs.label }} documentation
                    </DialogDescription>
                    <DocsSidebar :docs="docs" />
                </DialogContent>
            </DialogPortal>
        </DialogRoot>

        <div
            class="flex min-w-0 flex-1 flex-col overflow-hidden bg-xq-background lg:my-2 lg:mr-2 lg:rounded-xq-xl lg:border lg:border-xq-border"
        >
            <header
                class="flex h-14 shrink-0 items-center justify-between gap-3 border-b border-xq-border bg-xq-background px-4 md:grid md:grid-cols-[1fr_auto_1fr]"
            >
                <div class="flex min-w-0 items-center gap-2">
                    <button
                        type="button"
                        :class="[iconButtonClass, 'lg:hidden']"
                        aria-label="Open navigation"
                        @click="drawerOpen = true"
                    >
                        <Menu class="size-4.5" aria-hidden="true" />
                    </button>
                    <nav aria-label="Breadcrumb" class="min-w-0 text-sm">
                        <ol class="flex items-center gap-1.5">
                            <li class="min-w-0">
                                <Link
                                    :href="docs.url"
                                    class="truncate text-xq-muted-foreground hover:text-xq-foreground"
                                >
                                    {{ docs.label }}
                                </Link>
                            </li>
                            <li
                                aria-hidden="true"
                                class="text-xq-muted-foreground"
                            >
                                /
                            </li>
                            <li
                                class="min-w-0 truncate font-medium"
                                aria-current="page"
                            >
                                {{ documentData.title }}
                            </li>
                        </ol>
                    </nav>
                </div>

                <button
                    type="button"
                    class="hidden h-9 w-72 cursor-pointer items-center gap-2 rounded-full bg-xq-muted px-3 text-sm text-xq-muted-foreground transition-[background-color,scale] duration-(--xq-duration-fast) hover:bg-xq-accent focus-visible:ring-2 focus-visible:ring-xq-ring focus-visible:outline-none active:scale-[0.98] md:flex"
                    @click="searchOpen = true"
                >
                    <Search class="size-4" aria-hidden="true" />
                    Search
                    <kbd class="ml-auto font-sans text-xs">⌘K</kbd>
                </button>

                <div class="flex items-center justify-end gap-1">
                    <button
                        type="button"
                        :class="[iconButtonClass, 'md:hidden']"
                        aria-label="Search"
                        @click="searchOpen = true"
                    >
                        <Search class="size-4" aria-hidden="true" />
                    </button>
                    <ThemeToggle />
                    <a
                        :href="demoUrl"
                        data-slot="xq-button"
                        class="ml-1 hidden h-8 items-center bg-xq-primary px-3.5 text-[0.8125rem] font-medium text-xq-primary-foreground transition-[scale,filter] duration-(--xq-duration-fast) hover:brightness-110 active:scale-[0.97] sm:inline-flex"
                    >
                        Demo
                    </a>
                </div>
            </header>

            <div class="relative min-h-0 flex-1">
                <div class="site-glow docs-glow" aria-hidden="true"></div>
                <main class="relative h-full overflow-y-auto" scroll-region>
                    <slot />
                </main>
            </div>
        </div>

        <DocsSearch v-model:open="searchOpen" :package="docs.package" />
    </div>
</template>
