<script setup lang="ts">
import { computed } from 'vue';
import { isoBox } from '@/lib/iso';

const props = withDefaults(
    defineProps<{
        x: number;
        y: number;
        z?: number;
        w: number;
        d: number;
        h: number;
    }>(),
    { z: 0 },
);

const faces = computed(() =>
    isoBox(props.x, props.y, props.z, props.w, props.d, props.h),
);
</script>

<template>
    <g class="iso-box">
        <polygon :points="faces.left" class="iso-face-left" />
        <polygon :points="faces.right" class="iso-face-right" />
        <polygon :points="faces.top" class="iso-face-top" />
    </g>
</template>
