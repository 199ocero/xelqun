<script setup lang="ts">
import IsoBox from '@/components/home/IsoBox.vue';

/**
 * A request rides through validation and authorization, then lands in the
 * database. Each `at` is when the cube reaches that stop in the 4s cycle.
 */
const gates = [
    { x: 50, label: 'Validate', labelAt: [18, -12], at: '1.05s' },
    { x: 105, label: 'Authorize', labelAt: [66, 16], at: '2.4s' },
];

/** The database: three discs, centered at (115, 98) on screen. */
const disc = (index: number): string => {
    const bottom = 98 - index * 11;
    const top = bottom - 9;

    return `M 99 ${top} V ${bottom} A 16 8 0 0 0 131 ${bottom} V ${top} Z`;
};
</script>

<template>
    <svg viewBox="-50 -26 190 140" fill="none" class="iso-scene">
        <IsoBox :x="0" :y="20" :w="150" :d="24" :h="3" />

        <template v-for="gate in gates" :key="gate.x">
            <IsoBox :x="gate.x" :y="14" :z="3" :w="6" :d="6" :h="30" />
        </template>

        <g class="pipe-token">
            <IsoBox
                class="iso-primary"
                :x="4"
                :y="26"
                :z="3"
                :w="12"
                :d="12"
                :h="12"
            />
        </g>

        <template v-for="gate in gates" :key="gate.label">
            <IsoBox :x="gate.x" :y="44" :z="3" :w="6" :d="6" :h="30" />
            <IsoBox :x="gate.x" :y="14" :z="33" :w="6" :d="36" :h="5" />
            <IsoBox
                class="iso-primary pipe-lit"
                :style="{ animationDelay: gate.at }"
                :x="gate.x"
                :y="14"
                :z="33"
                :w="6"
                :d="36"
                :h="5"
            />
            <text
                :x="gate.labelAt[0]"
                :y="gate.labelAt[1]"
                text-anchor="middle"
                class="fill-xq-muted-foreground text-[9px] font-medium"
            >
                {{ gate.label }}
            </text>
        </template>

        <g v-for="index in [0, 1, 2]" :key="index">
            <path :d="disc(index)" class="iso-face-right" />
            <ellipse
                cx="115"
                :cy="98 - index * 11 - 9"
                rx="16"
                ry="8"
                class="iso-face-top"
            />
        </g>
        <ellipse
            cx="115"
            cy="67"
            rx="16"
            ry="8"
            class="pipe-lit fill-xq-primary"
            style="animation-delay: 3.4s"
        />
        <text
            x="115"
            y="52"
            text-anchor="middle"
            class="fill-xq-muted-foreground text-[9px] font-medium"
        >
            Save
        </text>
    </svg>
</template>
