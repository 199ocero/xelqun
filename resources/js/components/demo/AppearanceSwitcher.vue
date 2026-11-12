<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { Check, Monitor, Moon, Palette, Sun } from 'lucide-vue-next';
import {
    PopoverContent,
    PopoverPortal,
    PopoverRoot,
    PopoverTrigger,
} from 'reka-ui';
import { onMounted, ref } from 'vue';

type Theme = 'light' | 'dark' | 'system';

const THEME_KEY = 'xq-panel-theme';

const props = defineProps<{
    preset: string;
    presets: string[];
    primary: string | null;
    gray: string;
    primaries: Record<string, string>;
    grays: Record<string, string>;
    defaultPrimary: string;
    updateUrl: string;
}>();

const themes: { value: Theme; label: string; icon: typeof Sun }[] = [
    { value: 'light', label: 'Light', icon: Sun },
    { value: 'dark', label: 'Dark', icon: Moon },
    { value: 'system', label: 'System', icon: Monitor },
];

const theme = ref<Theme>('system');
const preset = ref(props.preset);
const primary = ref(props.primary);
const gray = ref(props.gray);

onMounted(() => {
    try {
        const saved = localStorage.getItem(THEME_KEY);

        theme.value = saved === 'light' || saved === 'dark' ? saved : 'system';
    } catch {
        // Storage can be blocked. The choice still holds for this visit.
    }
});

function applyTheme(next: Theme): void {
    theme.value = next;

    const change = (): void => {
        document.documentElement.classList.toggle(
            'dark',
            next === 'dark' ||
                (next === 'system' &&
                    window.matchMedia('(prefers-color-scheme: dark)').matches),
        );
    };

    try {
        if (next === 'system') {
            localStorage.removeItem(THEME_KEY);
        } else {
            localStorage.setItem(THEME_KEY, next);
        }
    } catch {
        // Storage can be blocked. The choice still holds for this visit.
    }

    if (
        'startViewTransition' in document &&
        !window.matchMedia('(prefers-reduced-motion: reduce)').matches
    ) {
        document.startViewTransition(change);
    } else {
        change();
    }
}

/** Colors come from the server, which keeps text readable on each of them. */
function save(): void {
    router.post(
        props.updateUrl,
        { preset: preset.value, primary: primary.value, gray: gray.value },
        { preserveScroll: true, preserveState: true },
    );
}

function choosePrimary(name: string | null): void {
    primary.value = name;
    save();
}

function choosePreset(name: string): void {
    preset.value = name;
    save();
}

function chooseGray(name: string): void {
    gray.value = name;
    save();
}

const swatchClass =
    'relative flex size-7 cursor-pointer items-center justify-center rounded-full transition-[scale] duration-(--xq-duration-base) ease-(--xq-ease-spring) hover:scale-110 focus-visible:ring-2 focus-visible:ring-xq-ring focus-visible:ring-offset-2 focus-visible:ring-offset-xq-popover focus-visible:outline-none';
</script>

<template>
    <Teleport to="body">
        <PopoverRoot>
            <PopoverTrigger
                aria-label="Customize appearance"
                data-demo-appearance
                class="fixed right-6 bottom-6 z-40 flex size-11 cursor-pointer items-center justify-center rounded-full border border-xq-border bg-xq-card text-xq-foreground shadow-lg transition-[scale] duration-(--xq-duration-base) ease-(--xq-ease-spring) hover:scale-105 focus-visible:ring-2 focus-visible:ring-xq-ring focus-visible:outline-none active:scale-95"
            >
                <Palette class="size-5" aria-hidden="true" />
            </PopoverTrigger>
            <PopoverPortal>
                <PopoverContent
                    side="top"
                    align="end"
                    :side-offset="10"
                    class="z-50 grid w-72 origin-(--reka-popper-transform-origin) gap-4 rounded-xq-xl border border-xq-border bg-xq-popover p-4 text-sm text-xq-popover-foreground shadow-xl data-[state=closed]:animate-xq-pop-out data-[state=open]:animate-xq-pop-in"
                    data-demo-appearance-panel
                >
                    <div>
                        <p class="font-medium">Appearance</p>
                        <p class="text-xs text-xq-muted-foreground">
                            Only you see this, for this visit.
                        </p>
                    </div>

                    <section class="grid gap-2">
                        <h3
                            class="text-xs font-medium text-xq-muted-foreground"
                        >
                            Theme
                        </h3>
                        <div
                            role="radiogroup"
                            aria-label="Theme"
                            class="grid grid-cols-3 gap-0.5 rounded-xq-md bg-xq-nav-hover p-0.5"
                        >
                            <button
                                v-for="option in themes"
                                :key="option.value"
                                type="button"
                                role="radio"
                                :aria-checked="theme === option.value"
                                :class="[
                                    'flex cursor-pointer items-center justify-center gap-1.5 rounded-xq-sm py-1.5 text-xs font-medium transition-[background-color,color] duration-(--xq-duration-fast)',
                                    theme === option.value
                                        ? 'bg-xq-card text-xq-foreground shadow-xs'
                                        : 'text-xq-muted-foreground hover:text-xq-foreground',
                                ]"
                                @click="applyTheme(option.value)"
                            >
                                <component
                                    :is="option.icon"
                                    class="size-3.5"
                                    aria-hidden="true"
                                />
                                {{ option.label }}
                            </button>
                        </div>
                    </section>

                    <section class="grid gap-2">
                        <h3
                            class="text-xs font-medium text-xq-muted-foreground"
                        >
                            Style
                        </h3>
                        <div
                            role="radiogroup"
                            aria-label="Style"
                            class="grid grid-cols-4 gap-0.5 rounded-xq-md bg-xq-nav-hover p-0.5"
                        >
                            <button
                                v-for="option in presets"
                                :key="option"
                                type="button"
                                role="radio"
                                :aria-checked="preset === option"
                                :class="[
                                    'cursor-pointer rounded-xq-sm py-1.5 text-xs font-medium capitalize transition-[background-color,color] duration-(--xq-duration-fast)',
                                    preset === option
                                        ? 'bg-xq-card text-xq-foreground shadow-xs'
                                        : 'text-xq-muted-foreground hover:text-xq-foreground',
                                ]"
                                @click="choosePreset(option)"
                            >
                                {{ option }}
                            </button>
                        </div>
                    </section>

                    <section class="grid gap-2">
                        <h3
                            class="text-xs font-medium text-xq-muted-foreground"
                        >
                            Primary
                        </h3>
                        <div
                            role="radiogroup"
                            aria-label="Primary"
                            class="flex flex-wrap gap-1.5"
                        >
                            <button
                                type="button"
                                role="radio"
                                aria-label="Default"
                                title="Default"
                                :aria-checked="primary === null"
                                :class="swatchClass"
                                :style="{ backgroundColor: defaultPrimary }"
                                @click="choosePrimary(null)"
                            >
                                <Check
                                    v-if="primary === null"
                                    class="size-3.5 text-white"
                                    aria-hidden="true"
                                />
                            </button>
                            <button
                                v-for="(color, name) in primaries"
                                :key="name"
                                type="button"
                                role="radio"
                                :aria-label="String(name)"
                                :title="String(name)"
                                :aria-checked="primary === name"
                                :class="swatchClass"
                                :style="{ backgroundColor: color }"
                                @click="choosePrimary(String(name))"
                            >
                                <Check
                                    v-if="primary === name"
                                    class="size-3.5 text-white"
                                    aria-hidden="true"
                                />
                            </button>
                        </div>
                    </section>

                    <section class="grid gap-2">
                        <h3
                            class="flex justify-between text-xs font-medium text-xq-muted-foreground"
                        >
                            Secondary
                            <span class="font-normal">{{ gray }}</span>
                        </h3>
                        <div
                            role="radiogroup"
                            aria-label="Secondary"
                            class="flex flex-wrap gap-1.5"
                        >
                            <button
                                v-for="(color, name) in grays"
                                :key="name"
                                type="button"
                                role="radio"
                                :aria-label="String(name)"
                                :title="String(name)"
                                :aria-checked="gray === name"
                                :class="swatchClass"
                                :style="{ backgroundColor: color }"
                                @click="chooseGray(String(name))"
                            >
                                <Check
                                    v-if="gray === name"
                                    class="size-3.5 text-white"
                                    aria-hidden="true"
                                />
                            </button>
                        </div>
                    </section>
                </PopoverContent>
            </PopoverPortal>
        </PopoverRoot>
    </Teleport>
</template>
