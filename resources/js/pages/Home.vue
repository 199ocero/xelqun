<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { ArrowRight, ArrowUpRight } from 'lucide-vue-next';
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import DocsSearch from '@/components/docs/DocsSearch.vue';
import GithubStars from '@/components/docs/GithubStars.vue';
import ThemeToggle from '@/components/docs/ThemeToggle.vue';
import XelqunMark from '@/components/docs/XelqunMark.vue';
import HeroScene from '@/components/home/HeroScene.vue';
import LayerStack from '@/components/home/LayerStack.vue';
import StepCodeScene from '@/components/home/StepCodeScene.vue';
import StepInterfaceScene from '@/components/home/StepInterfaceScene.vue';
import StepPipelineScene from '@/components/home/StepPipelineScene.vue';

defineProps<{
    packages: { label: string; tagline: string; url: string; pages: number }[];
    canonical: string;
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

/** The same three stops as the hero diagram: PHP in, Xelqun in the middle, Vue out. Each has its own scene. */
const steps = [
    {
        scene: StepCodeScene,
        title: 'Write a PHP class',
        body: 'List the fields, columns and pages you need. That class is the whole definition.',
    },
    {
        scene: StepPipelineScene,
        title: 'Xelqun does the plumbing',
        body: 'Every submission is validated, checked against your policies and saved to Eloquent.',
    },
    {
        scene: StepInterfaceScene,
        title: 'Vue draws the screen',
        body: 'Inertia hands it to ready-made Vue components, so most days you write no Vue at all.',
    },
];

/** Each package's color, shared by its layer in the stack and its dot in the list. */
const tints: Record<string, string> = {
    Forms: '#8b5cf6',
    Tables: '#f43f5e',
    Panels: '#3b82f6',
};

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

const primaryButton =
    'inline-flex h-12 items-center rounded-(--xq-button-radius) border border-black/10 bg-xq-primary px-7 text-base font-medium text-xq-primary-foreground shadow-(--xq-button-highlight) transition-[scale,background-color] duration-(--xq-duration-fast) hover:bg-xq-primary/90 focus-visible:ring-2 focus-visible:ring-xq-ring focus-visible:ring-offset-2 focus-visible:ring-offset-xq-background focus-visible:outline-none active:scale-[0.97]';
const outlineButton =
    'inline-flex h-12 items-center rounded-(--xq-button-radius) border border-xq-border border-b-xq-input bg-xq-raised px-7 text-base font-medium shadow-(--xq-button-shadow) transition-[scale,background-color] duration-(--xq-duration-fast) hover:bg-xq-accent focus-visible:ring-2 focus-visible:ring-xq-ring focus-visible:outline-none active:scale-[0.97]';
const badgeDot = 'size-2.5 rotate-12 rounded-[3px] bg-xq-primary';

const navButton =
    'inline-flex items-center justify-center rounded-(--xq-button-radius) border border-black/10 bg-xq-primary font-medium text-xq-primary-foreground shadow-(--xq-button-highlight) transition-[scale,background-color] duration-(--xq-duration-fast) hover:bg-xq-primary/90 focus-visible:ring-2 focus-visible:ring-xq-ring focus-visible:ring-offset-2 focus-visible:ring-offset-xq-background focus-visible:outline-none active:scale-[0.97]';
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
            <link head-key="canonical" rel="canonical" :href="canonical" />
            <meta head-key="og:type" property="og:type" content="website" />
            <meta head-key="og:url" property="og:url" :content="canonical" />
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
            <meta
                head-key="twitter:card"
                name="twitter:card"
                content="summary_large_image"
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
                        :class="[navButton, 'h-10 px-5 text-sm']"
                    >
                        Open the demo
                    </a>
                </div>
            </header>

            <main data-xq-page>
                <section class="pt-16 text-center sm:pt-24">
                    <p
                        class="home-rise inline-flex items-center gap-2 rounded-full border border-xq-border bg-xq-card/70 py-1 pr-3 pl-3.5 text-sm font-medium shadow-xs"
                    >
                        Open source admin panels for Laravel
                        <span :class="badgeDot" aria-hidden="true"></span>
                    </p>
                    <h1
                        class="home-rise mx-auto mt-6 max-w-4xl text-[2.75rem] leading-[1.05] font-medium tracking-[-0.04em] text-balance sm:text-7xl"
                        style="--rise: 1"
                    >
                        Write it in PHP.
                        <span class="text-xq-primary">Ship it in Vue.</span>
                    </h1>
                    <p
                        class="home-rise mx-auto mt-6 max-w-2xl text-lg leading-relaxed text-pretty text-xq-muted-foreground"
                        style="--rise: 2"
                    >
                        Describe forms, tables and whole admin panels in plain
                        PHP. Xelqun turns them into an Inertia and Vue app, with
                        auth, CRUD and dashboards built in.
                    </p>
                    <div
                        class="home-rise mt-9 flex flex-wrap items-center justify-center gap-3"
                        style="--rise: 3"
                    >
                        <a
                            :href="links.demo"
                            :class="[primaryButton, 'group gap-2']"
                        >
                            Try the live demo
                            <ArrowRight
                                class="size-4.5 transition-transform duration-(--xq-duration-base) ease-(--xq-ease-spring) group-hover:translate-x-0.5"
                                aria-hidden="true"
                            />
                        </a>
                        <Link
                            href="/introduction"
                            prefetch
                            :class="outlineButton"
                        >
                            Read the docs
                        </Link>
                    </div>

                    <HeroScene
                        class="home-rise mx-auto mt-10 w-full max-w-4xl"
                        style="--rise: 4"
                        aria-hidden="true"
                    />
                </section>

                <section
                    aria-labelledby="how-it-works"
                    class="scroll-mt-24 py-16 sm:py-24"
                >
                    <div class="mx-auto max-w-2xl text-center">
                        <p
                            class="inline-flex items-center gap-2 rounded-full border border-xq-border bg-xq-card/70 py-1 pr-3 pl-3.5 text-sm font-medium shadow-xs"
                        >
                            How it works
                            <span :class="badgeDot" aria-hidden="true"></span>
                        </p>
                        <h2
                            id="how-it-works"
                            class="mt-5 scroll-mt-24 text-4xl font-medium tracking-[-0.03em] text-balance sm:text-5xl"
                        >
                            One class in, a working screen out.
                        </h2>
                        <p
                            class="mt-4 text-lg leading-relaxed text-pretty text-xq-muted-foreground"
                        >
                            You say what a page holds. Xelqun takes it the rest
                            of the way, from the request to the pixels.
                        </p>
                    </div>

                    <ol class="mt-12 grid gap-12 md:grid-cols-3 md:gap-0">
                        <li
                            v-for="(step, index) in steps"
                            :key="step.title"
                            class="relative flex flex-col items-center px-4 text-center"
                        >
                            <component
                                :is="step.scene"
                                class="h-48 w-full max-w-72"
                                aria-hidden="true"
                            />
                            <span
                                v-if="index < steps.length - 1"
                                class="home-track absolute top-24 -right-6 hidden w-12 md:block"
                                aria-hidden="true"
                            ></span>
                            <span
                                class="mt-2 text-xs font-medium tracking-wide text-xq-muted-foreground uppercase"
                            >
                                Step {{ index + 1 }}
                            </span>
                            <h3 class="mt-1.5 text-lg font-semibold">
                                {{ step.title }}
                            </h3>
                            <p
                                class="mt-1.5 max-w-xs text-sm leading-relaxed text-pretty text-xq-muted-foreground"
                            >
                                {{ step.body }}
                            </p>
                        </li>
                    </ol>
                </section>

                <section
                    aria-labelledby="packages"
                    class="overflow-hidden rounded-[2rem] bg-[#0c0c0e] px-6 py-16 text-white ring-1 ring-white/10 sm:px-12 sm:py-20"
                >
                    <div class="grid items-center gap-10 lg:grid-cols-2">
                        <LayerStack
                            :tints="packages.map((item) => tints[item.label])"
                        />
                        <div>
                            <p
                                class="inline-flex items-center gap-2 rounded-full bg-white/8 py-1 pr-3 pl-3.5 text-sm font-medium"
                            >
                                Three packages
                                <span
                                    :class="badgeDot"
                                    aria-hidden="true"
                                ></span>
                            </p>
                            <h2
                                id="packages"
                                class="mt-5 text-4xl font-medium tracking-[-0.03em] text-balance sm:text-5xl"
                            >
                                Take one package, or the whole stack.
                            </h2>
                            <p
                                class="mt-4 text-lg leading-relaxed text-pretty text-white/65"
                            >
                                Forms works on its own. Tables builds on forms,
                                and panels brings both together into a complete
                                admin.
                            </p>
                            <ul
                                class="mt-8 divide-y divide-white/12 border-y border-white/12"
                            >
                                <li
                                    v-for="item in [...packages].reverse()"
                                    :key="item.url"
                                >
                                    <Link
                                        :href="item.url"
                                        prefetch
                                        class="group flex items-start gap-3.5 py-4 focus-visible:ring-2 focus-visible:ring-xq-ring focus-visible:outline-none"
                                    >
                                        <span
                                            class="mt-1.5 size-2.5 shrink-0 rotate-12 rounded-[3px]"
                                            :style="{
                                                backgroundColor:
                                                    tints[item.label],
                                            }"
                                            aria-hidden="true"
                                        ></span>
                                        <span class="min-w-0 flex-1">
                                            <span class="block font-medium">
                                                {{ item.label }}
                                            </span>
                                            <span
                                                class="block text-sm text-white/60"
                                            >
                                                {{ item.tagline }}
                                            </span>
                                        </span>
                                        <ArrowUpRight
                                            class="mt-1 size-4 shrink-0 text-white/50 transition-transform duration-(--xq-duration-base) ease-(--xq-ease-spring) group-hover:translate-x-0.5 group-hover:-translate-y-0.5 group-hover:text-white"
                                            aria-hidden="true"
                                        />
                                    </Link>
                                </li>
                            </ul>
                        </div>
                    </div>
                </section>

                <section
                    aria-labelledby="get-started"
                    class="flex flex-col items-center gap-6 py-20 text-center sm:flex-row sm:justify-between sm:py-24 sm:text-left"
                >
                    <h2
                        id="get-started"
                        class="text-3xl font-medium tracking-[-0.03em] text-balance sm:text-4xl"
                    >
                        Your first panel is one class away.
                    </h2>
                    <div class="flex shrink-0 flex-wrap justify-center gap-3">
                        <Link
                            href="/introduction/installation"
                            prefetch
                            :class="[primaryButton, 'group gap-2']"
                        >
                            Get started
                            <ArrowRight
                                class="size-4.5 transition-transform duration-(--xq-duration-base) ease-(--xq-ease-spring) group-hover:translate-x-0.5"
                                aria-hidden="true"
                            />
                        </Link>
                        <a :href="links.github" :class="outlineButton">
                            GitHub
                        </a>
                    </div>
                </section>
            </main>

            <footer
                class="flex flex-col items-center gap-4 border-t border-xq-border py-8 text-sm text-xq-muted-foreground sm:flex-row sm:justify-between"
            >
                <p class="flex items-center gap-2">
                    <XelqunMark class="size-5" />
                    Xelqun is open source under the MIT license.
                </p>
                <nav aria-label="Footer" class="flex flex-wrap gap-x-6 gap-y-2">
                    <Link
                        href="/introduction"
                        class="transition-colors duration-(--xq-duration-fast) hover:text-xq-foreground"
                    >
                        Docs
                    </Link>
                    <Link
                        v-for="item in packages"
                        :key="item.url"
                        :href="item.url"
                        class="transition-colors duration-(--xq-duration-fast) hover:text-xq-foreground"
                    >
                        {{ item.label }}
                    </Link>
                    <a
                        :href="links.github"
                        class="transition-colors duration-(--xq-duration-fast) hover:text-xq-foreground"
                    >
                        GitHub
                    </a>
                </nav>
            </footer>
        </div>

        <DocsSearch v-model:open="searchOpen" />
    </div>
</template>
