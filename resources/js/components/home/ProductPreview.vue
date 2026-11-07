<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import {
    ArrowUpRight,
    BookOpen,
    FileText,
    GitBranch,
    LayoutDashboard,
    PlayCircle,
    Table,
} from 'lucide-vue-next';
import { computed } from 'vue';
import type { Component } from 'vue';
import InstallCommand from '@/components/home/InstallCommand.vue';
import XelqunMark from '@/components/docs/XelqunMark.vue';

type Package = { label: string; tagline: string; url: string; pages: number };

const props = defineProps<{
    packages: Package[];
    demoUrl: string;
    githubUrl: string;
}>();

const icons: Record<string, Component> = {
    Forms: FileText,
    Tables: Table,
    Panels: LayoutDashboard,
};

/** The color of each card's title and its small preview. */
const tints: Record<string, string> = {
    Forms: '#8b5cf6',
    Tables: '#f43f5e',
    Panels: '#3b82f6',
    Demo: '#f59e0b',
};

const totalPages = computed(() =>
    props.packages.reduce((total, item) => total + item.pages, 0),
);
const mostPages = computed(() =>
    Math.max(...props.packages.map((item) => item.pages)),
);

const cardStyle = (name: string) => ({ '--tint': tints[name] });
</script>

<template>
    <div
        class="home-preview-fade grid grid-cols-1 overflow-hidden rounded-t-[1.25rem] border border-b-0 border-xq-border bg-xq-shell text-left lg:grid-cols-[13rem_minmax(0,1fr)]"
    >
        <aside aria-hidden="true" class="hidden flex-col gap-4 p-3 lg:flex">
            <div class="flex h-8 items-center gap-2 px-1 text-sm font-semibold">
                <XelqunMark class="size-5.5" />
                Xelqun
            </div>
            <div class="grid gap-0.5 text-sm">
                <span
                    class="flex h-8 items-center gap-2.5 rounded-xq-sm bg-xq-nav-active px-2.5 font-medium"
                >
                    <LayoutDashboard class="size-4 text-xq-muted-foreground" />
                    Overview
                </span>
            </div>
            <div class="grid gap-0.5 text-sm text-xq-foreground/80">
                <p
                    class="px-2.5 pb-1 text-xs font-medium text-xq-muted-foreground"
                >
                    Packages
                </p>
                <span
                    v-for="item in packages"
                    :key="item.label"
                    class="flex h-8 items-center gap-2.5 px-2.5"
                >
                    <component
                        :is="icons[item.label]"
                        class="size-4 text-xq-muted-foreground"
                    />
                    {{ item.label }}
                </span>
            </div>
            <div class="grid gap-0.5 text-sm text-xq-foreground/80">
                <p
                    class="px-2.5 pb-1 text-xs font-medium text-xq-muted-foreground"
                >
                    Resources
                </p>
                <span class="flex h-8 items-center gap-2.5 px-2.5">
                    <PlayCircle class="size-4 text-xq-muted-foreground" />
                    Live demo
                </span>
                <span class="flex h-8 items-center gap-2.5 px-2.5">
                    <GitBranch class="size-4 text-xq-muted-foreground" />
                    GitHub
                </span>
            </div>
        </aside>

        <div
            class="m-0 min-w-0 border-xq-border bg-xq-background p-4 sm:p-6 lg:my-2 lg:mr-2 lg:rounded-xq-xl lg:border lg:border-b-0"
        >
            <h2 class="text-lg font-semibold tracking-tight sm:text-xl">
                Everything an admin panel needs
            </h2>

            <p class="mt-5 mb-2.5 text-xs font-medium text-xq-muted-foreground">
                THE PACKAGES
            </p>
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-4">
                <Link
                    v-for="item in packages"
                    :key="item.label"
                    :href="item.url"
                    prefetch
                    :style="cardStyle(item.label)"
                    class="group flex min-h-44 flex-col overflow-hidden rounded-xq-lg border border-xq-border bg-xq-card p-4 transition-[scale,border-color] duration-(--xq-duration-base) ease-(--xq-ease-out) hover:border-xq-input active:scale-[0.98]"
                >
                    <h3 class="font-semibold text-(--tint)">
                        {{ item.label }}
                    </h3>
                    <p
                        class="mt-1 text-[0.8125rem] leading-snug text-xq-muted-foreground"
                    >
                        {{ item.tagline }}
                    </p>

                    <div
                        aria-hidden="true"
                        class="mt-3 mb-3 grid h-16 content-center gap-1.5 rounded-xq-md border border-xq-border bg-xq-background p-2 text-[0.6875rem] text-xq-muted-foreground"
                    >
                        <template v-if="item.label === 'Forms'">
                            <span
                                class="h-5 rounded-xq-sm border border-xq-input px-1.5 leading-[1.15rem]"
                                >Ada Lovelace</span
                            >
                            <span class="flex items-center justify-between">
                                Published
                                <span
                                    class="flex h-3.5 w-6 items-center justify-end rounded-full bg-(--tint) p-0.5"
                                >
                                    <span
                                        class="size-2.5 rounded-full bg-white"
                                    ></span>
                                </span>
                            </span>
                        </template>
                        <template v-else-if="item.label === 'Tables'">
                            <span
                                v-for="row in [
                                    'Air Desk Lamp',
                                    'Studio Keyboard',
                                ]"
                                :key="row"
                                class="flex items-center justify-between gap-2"
                            >
                                <span class="truncate">{{ row }}</span>
                                <span
                                    class="rounded-full bg-[color-mix(in_oklab,var(--tint)_14%,transparent)] px-1.5 text-(--tint)"
                                    >active</span
                                >
                            </span>
                        </template>
                        <template v-else>
                            <span class="flex items-end gap-1">
                                <span
                                    v-for="height in [
                                        14, 26, 18, 34, 24, 40, 30,
                                    ]"
                                    :key="height"
                                    class="flex-1 rounded-sm bg-(--tint)"
                                    :style="{
                                        height: `${height}px`,
                                        opacity: 0.35 + height / 64,
                                    }"
                                ></span>
                            </span>
                        </template>
                    </div>

                    <span
                        class="mt-auto inline-flex items-center gap-1 text-[0.8125rem] font-medium"
                    >
                        Explore {{ item.label }}
                        <ArrowUpRight
                            class="size-3.5 text-xq-muted-foreground transition-transform duration-(--xq-duration-base) ease-(--xq-ease-spring) group-hover:translate-x-0.5 group-hover:-translate-y-0.5"
                            aria-hidden="true"
                        />
                    </span>
                </Link>

                <a
                    :href="demoUrl"
                    :style="cardStyle('Demo')"
                    class="group flex min-h-44 flex-col overflow-hidden rounded-xq-lg border border-xq-border bg-xq-card p-4 transition-[scale,border-color] duration-(--xq-duration-base) ease-(--xq-ease-out) hover:border-xq-input active:scale-[0.98]"
                >
                    <h3 class="font-semibold text-(--tint)">Live demo</h3>
                    <p
                        class="mt-1 text-[0.8125rem] leading-snug text-xq-muted-foreground"
                    >
                        A full admin panel to click through, with one-click sign
                        in.
                    </p>
                    <div
                        aria-hidden="true"
                        class="mt-3 mb-3 grid h-16 content-center gap-1.5 rounded-xq-md border border-xq-border bg-xq-background p-2 text-[0.6875rem] text-xq-muted-foreground"
                    >
                        <span class="flex items-center gap-1.5">
                            <span
                                class="size-1.5 rounded-full bg-xq-success"
                            ></span>
                            demo@xelqun.com
                        </span>
                        <span>Filled in for you</span>
                    </div>
                    <span
                        class="mt-auto inline-flex items-center gap-1 text-[0.8125rem] font-medium"
                    >
                        Open the demo
                        <ArrowUpRight
                            class="size-3.5 text-xq-muted-foreground transition-transform duration-(--xq-duration-base) ease-(--xq-ease-spring) group-hover:translate-x-0.5 group-hover:-translate-y-0.5"
                            aria-hidden="true"
                        />
                    </span>
                </a>
            </div>

            <p class="mt-5 mb-2.5 text-xs font-medium text-xq-muted-foreground">
                GET STARTED
            </p>
            <div
                class="grid grid-cols-1 gap-3 md:grid-cols-[minmax(0,1.6fr)_minmax(0,1fr)_minmax(0,1fr)]"
            >
                <InstallCommand />

                <div
                    class="flex items-center gap-3 rounded-xq-lg border border-xq-border bg-xq-card p-3"
                >
                    <div class="min-w-0 flex-1">
                        <p class="text-[0.8125rem] font-medium">
                            {{ totalPages }} docs pages
                        </p>
                        <p class="text-xs text-xq-muted-foreground">
                            Across three packages
                        </p>
                    </div>
                    <div aria-hidden="true" class="flex h-9 items-end gap-1">
                        <span
                            v-for="item in packages"
                            :key="item.label"
                            class="w-3 rounded-sm"
                            :style="{
                                height: `${(item.pages / mostPages) * 100}%`,
                                backgroundColor: tints[item.label],
                            }"
                        ></span>
                    </div>
                </div>

                <div
                    class="grid grid-cols-2 divide-x divide-xq-border rounded-xq-lg border border-xq-border bg-xq-card text-[0.8125rem] font-medium"
                >
                    <Link
                        href="/introduction/installation"
                        class="flex items-center justify-center gap-1.5 rounded-l-xq-lg px-2 py-3 transition-colors duration-(--xq-duration-fast) hover:bg-xq-nav-hover"
                    >
                        <BookOpen
                            class="size-4 text-xq-primary"
                            aria-hidden="true"
                        />
                        Install
                    </Link>
                    <a
                        :href="githubUrl"
                        class="flex items-center justify-center gap-1.5 rounded-r-xq-lg px-2 py-3 transition-colors duration-(--xq-duration-fast) hover:bg-xq-nav-hover"
                    >
                        <GitBranch
                            class="size-4 text-xq-primary"
                            aria-hidden="true"
                        />
                        GitHub
                    </a>
                </div>
            </div>

            <div class="h-36" aria-hidden="true"></div>
        </div>
    </div>
</template>
