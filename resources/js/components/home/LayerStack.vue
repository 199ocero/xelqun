<script setup lang="ts">
import XelqunMark from '@/components/docs/XelqunMark.vue';

/** One color per package, from the bottom layer up. */
defineProps<{ tints: string[] }>();
</script>

<template>
    <div aria-hidden="true" class="pointer-events-none relative h-72 sm:h-80">
        <div
            class="absolute top-[60%] left-1/2 size-[240px] -translate-1/2 scale-[0.8] sm:scale-100"
        >
            <div class="iso-plane relative size-full">
                <div
                    v-for="(tint, index) in tints"
                    :key="tint"
                    class="iso-tile iso-layer inset-0 flex items-center justify-center"
                    :style="{ '--tint': tint, '--lift': `${index * 44}px` }"
                >
                    <XelqunMark
                        v-if="index === tints.length - 1"
                        class="size-20"
                    />
                </div>
            </div>
        </div>
    </div>
</template>
