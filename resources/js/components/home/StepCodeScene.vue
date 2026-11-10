<script setup lang="ts">
import IsoBox from '@/components/home/IsoBox.vue';
import { isoFloor } from '@/lib/iso';

/** A few lines of a PHP class, as colored bars. Each one types itself out in turn. */
const lines = [
    { y: 22, indent: 0, parts: [14, 38], tones: ['kw', 'muted'] },
    { y: 31, indent: 8, parts: [18, 26], tones: ['kw', 'fn'] },
    { y: 40, indent: 16, parts: [10, 40], tones: ['kw', 'muted'] },
    { y: 49, indent: 16, parts: [24, 20], tones: ['fn', 'str'] },
    { y: 58, indent: 16, parts: [24, 14], tones: ['fn', 'str'] },
    { y: 67, indent: 8, parts: [6], tones: ['muted'] },
];

const partX = (line: (typeof lines)[number], index: number): number =>
    10 +
    line.indent +
    line.parts.slice(0, index).reduce((total, width) => total + width + 3, 0);
</script>

<template>
    <svg viewBox="-82 -32 225 140" fill="none" class="iso-scene">
        <g :transform="isoFloor(0)">
            <rect
                class="php-shadow"
                x="82"
                y="-38"
                width="30"
                height="30"
                rx="6"
            />
        </g>

        <IsoBox :x="0" :y="0" :w="110" :d="80" :h="8" />
        <g :transform="isoFloor(8)">
            <circle
                v-for="dot in [8, 14, 20]"
                :key="dot"
                :cx="dot"
                cy="8"
                r="2"
                class="code-muted"
            />
            <rect x="0" y="13" width="110" height="0.8" class="code-muted" />
            <g
                v-for="(line, index) in lines"
                :key="line.y"
                class="code-line"
                :style="{ '--i': index }"
            >
                <rect
                    v-for="(width, part) in line.parts"
                    :key="part"
                    :x="partX(line, part)"
                    :y="line.y"
                    :width="width"
                    height="4"
                    rx="2"
                    :class="`code-${line.tones[part]}`"
                />
            </g>
            <rect
                x="27"
                y="66"
                width="1.6"
                height="6"
                class="code-caret code-kw"
            />
        </g>

        <g class="php-badge">
            <IsoBox
                class="iso-primary"
                :x="82"
                :y="-38"
                :z="26"
                :w="30"
                :d="30"
                :h="7"
            />
            <g :transform="isoFloor(33)">
                <text
                    x="97"
                    y="-23"
                    text-anchor="middle"
                    dominant-baseline="central"
                    class="fill-white text-[11px] font-bold"
                >
                    php
                </text>
            </g>
        </g>
    </svg>
</template>
