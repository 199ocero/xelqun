<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { XqIcon } from '@xelqun/forms';
import {
    ArrowUpRight,
    BookOpen,
    ChevronRight,
    FileText,
    LayoutDashboard,
    Table,
} from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
import type { Component } from 'vue';
import XelqunMark from '@/components/docs/XelqunMark.vue';
import { useGlidingPills } from '@/composables/useGlidingPills';
import type { DocsShell } from '@/types/docs';

const props = defineProps<{ docs: DocsShell }>();

const icons: Record<string, Component> = {
    'book-open': BookOpen,
    'file-text': FileText,
    table: Table,
    'layout-dashboard': LayoutDashboard,
};

const page = usePage();
const demoUrl = computed(() => (page.props.links as { demo: string }).demo);

const openSections = ref(new Set([props.docs.package]));

// The section being read always opens, and the ones already open stay that way.
watch(
    () => props.docs.package,
    (section) => {
        openSections.value = new Set([...openSections.value, section]);
    },
);

function toggle(section: string): void {
    const next = new Set(openSections.value);

    if (!next.delete(section)) {
        next.add(section);
    }

    openSections.value = next;
}

const list = ref<HTMLElement>();
const { activeClass, activeStyle, glideClass, glideStyle, glideTo, hideGlide } =
    useGlidingPills(list, () => [props.docs.sections, openSections.value]);
</script>

<template>
    <nav
        aria-label="Documentation"
        class="flex h-full min-h-0 flex-col gap-4 p-3"
    >
        <div class="flex h-9 items-center gap-2.5 px-1">
            <Link
                href="/"
                class="flex min-w-0 flex-1 items-center gap-2.5 font-semibold"
            >
                <XelqunMark class="size-6.5 shrink-0" />
                <span class="truncate">Xelqun</span>
            </Link>
        </div>

        <div
            ref="list"
            class="relative flex min-h-0 flex-1 flex-col gap-1 overflow-y-auto"
            @mouseover="glideTo"
            @focusin="glideTo"
            @mouseleave="hideGlide"
            @focusout="hideGlide"
        >
            <span
                aria-hidden="true"
                :class="activeClass"
                :style="activeStyle"
            ></span>
            <span
                aria-hidden="true"
                :class="glideClass"
                :style="glideStyle"
            ></span>

            <section
                v-for="section in docs.sections"
                :key="section.key"
                class="relative"
            >
                <button
                    type="button"
                    :aria-expanded="openSections.has(section.key)"
                    data-docs-nav-item
                    class="group relative flex h-9 w-full cursor-pointer items-center gap-2.5 rounded-xq-sm px-2.5 text-sm font-medium transition-[scale] duration-(--xq-duration-fast) focus-visible:ring-2 focus-visible:ring-xq-ring focus-visible:outline-none active:scale-[0.98]"
                    @click="toggle(section.key)"
                >
                    <component
                        :is="icons[section.icon]"
                        class="size-4.5 shrink-0 text-xq-muted-foreground transition-transform duration-(--xq-duration-base) ease-(--xq-ease-spring) group-hover:scale-110"
                        aria-hidden="true"
                    />
                    <span class="truncate">{{ section.label }}</span>
                    <ChevronRight
                        :class="[
                            'ml-auto size-4 shrink-0 text-xq-muted-foreground transition-[rotate] duration-(--xq-duration-base) ease-(--xq-ease-out)',
                            openSections.has(section.key) && 'rotate-90',
                        ]"
                        aria-hidden="true"
                    />
                </button>

                <div
                    v-show="openSections.has(section.key)"
                    class="grid gap-3 pt-1 pb-3 data-[open=true]:animate-xq-fade-in"
                    :data-open="openSections.has(section.key)"
                >
                    <div
                        v-for="group in section.groups"
                        :key="group.label"
                        role="group"
                        :aria-label="group.label"
                        class="relative grid gap-0.5"
                    >
                        <p
                            v-if="section.groups.length > 1"
                            aria-hidden="true"
                            class="pt-1 pr-2.5 pb-1 pl-9.5 text-xs font-medium text-xq-muted-foreground"
                        >
                            {{ group.label }}
                        </p>
                        <Link
                            v-for="item in group.items"
                            :key="item.url"
                            :href="item.url"
                            :aria-current="item.active ? 'page' : undefined"
                            prefetch
                            data-docs-nav-item
                            :class="[
                                'relative flex h-8 items-center rounded-xq-sm pr-2.5 pl-9.5 text-sm transition-[color,scale] duration-(--xq-duration-fast) active:scale-[0.98]',
                                'focus-visible:ring-2 focus-visible:ring-xq-ring focus-visible:outline-none',
                                item.active
                                    ? 'font-medium text-xq-foreground'
                                    : 'text-xq-foreground/80',
                            ]"
                        >
                            <span class="truncate">{{ item.label }}</span>
                        </Link>
                    </div>
                </div>
            </section>
        </div>

        <a
            :href="demoUrl"
            class="group flex items-center gap-2.5 rounded-xq-md border border-xq-border bg-xq-card p-3 text-sm transition-[scale] duration-(--xq-duration-fast) active:scale-[0.98]"
        >
            <XqIcon
                name="eye"
                class="size-4.5 shrink-0 text-xq-muted-foreground"
                aria-hidden="true"
            />
            <span class="min-w-0 flex-1">
                <span class="block font-medium">Open the demo</span>
                <span class="block truncate text-xs text-xq-muted-foreground"
                    >A full admin panel, ready to use</span
                >
            </span>
            <ArrowUpRight
                class="size-4 shrink-0 text-xq-muted-foreground transition-transform duration-(--xq-duration-base) ease-(--xq-ease-spring) group-hover:scale-110"
                aria-hidden="true"
            />
        </a>
    </nav>
</template>
