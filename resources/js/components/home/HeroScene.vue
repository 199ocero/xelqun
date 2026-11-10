<script setup lang="ts">
import IsoBox from '@/components/home/IsoBox.vue';
import { isoFloor } from '@/lib/iso';

/**
 * The whole story in one picture: a PHP class and Eloquent feed the Xelqun core,
 * and a finished piece flies over to the admin panel. The cube lands at 3.4s of
 * its 4s cycle, which is when the panel flashes and the toast appears.
 */
const LANDS_AT = '3.4s';

/** The class on the editor, as colored bars that type themselves out. */
const lines = [
    { y: 69, indent: 0, parts: [10, 28], tones: ['kw', 'muted'] },
    { y: 76, indent: 6, parts: [14, 20], tones: ['kw', 'fn'] },
    { y: 83, indent: 12, parts: [8, 30], tones: ['kw', 'muted'] },
    { y: 90, indent: 12, parts: [18, 14], tones: ['fn', 'str'] },
    { y: 97, indent: 12, parts: [18, 10], tones: ['fn', 'str'] },
    { y: 104, indent: 6, parts: [5], tones: ['muted'] },
];

const partX = (line: (typeof lines)[number], index: number): number =>
    -122 +
    line.indent +
    line.parts.slice(0, index).reduce((total, width) => total + width + 2, 0);

/** Floor paths into the core and out to the panel, each with a pulse running along it. */
const wires = [
    { path: 'M -50 70 H -25 V 45', delay: '0s' },
    { path: 'M 30 82 V 45', delay: '-1.2s' },
    { path: 'M 45 -15 H 58 V -60 H 70', delay: '-0.6s' },
];

/** One disc of the database, seen from the front. */
const disc = (index: number): { side: string; top: number } => {
    const bottom = 62 - index * 10;
    const top = bottom - 8;

    return {
        side: `M -70 ${top} V ${bottom} A 14 7 0 0 0 -42 ${bottom} V ${top} Z`,
        top,
    };
};

const bars = [10, 18, 13, 22, 16, 20];

const labels = [
    { text: 'ProductResource.php', x: -105, y: -42, width: 84, delay: '0s' },
    { text: '/admin/products', x: 178, y: -50, width: 68, delay: '-1.6s' },
    { text: 'Eloquent', x: -100, y: 52, width: 42, delay: '-3.2s' },
];
</script>

<template>
    <svg viewBox="-215 -114 475 192" fill="none" class="iso-scene">
        <g :transform="isoFloor(0)">
            <rect
                class="php-shadow"
                x="-160"
                y="22"
                width="24"
                height="24"
                rx="5"
            />
            <g v-for="wire in wires" :key="wire.path">
                <path
                    :d="wire.path"
                    class="stroke-xq-input"
                    stroke-width="1.5"
                />
                <path
                    :d="wire.path"
                    pathLength="100"
                    class="iso-pulse stroke-xq-primary"
                    :style="{ animationDelay: wire.delay }"
                />
            </g>
        </g>

        <!-- The PHP class -->
        <IsoBox :x="-130" :y="55" :w="80" :d="60" :h="8" />
        <g :transform="isoFloor(8)">
            <circle
                v-for="dot in [-125, -120, -115]"
                :key="dot"
                :cx="dot"
                cy="60"
                r="1.6"
                class="code-muted"
            />
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
                    height="3.5"
                    rx="1.75"
                    :class="`code-${line.tones[part]}`"
                />
            </g>
            <rect
                x="-109"
                y="103"
                width="1.4"
                height="5.5"
                class="code-caret code-kw"
            />
        </g>

        <g class="php-badge">
            <IsoBox
                class="iso-primary"
                :x="-160"
                :y="22"
                :z="26"
                :w="24"
                :d="24"
                :h="6"
            />
            <g :transform="isoFloor(32)">
                <text
                    x="-148"
                    y="34"
                    text-anchor="middle"
                    dominant-baseline="central"
                    class="fill-white text-[9px] font-bold"
                >
                    php
                </text>
            </g>
        </g>

        <!-- The Xelqun core -->
        <IsoBox :x="-45" :y="-45" :w="90" :d="90" :h="6" />
        <g :transform="isoFloor(6)">
            <circle
                r="40"
                class="hero-orbit stroke-xq-primary"
                stroke-width="1"
            />
            <circle
                r="40"
                pathLength="100"
                class="hero-orbit-dot stroke-xq-primary"
            />
        </g>
        <IsoBox :x="-28" :y="-28" :z="6" :w="56" :d="56" :h="22" />
        <g :transform="isoFloor(28)">
            <rect
                class="core-ripple fill-xq-primary"
                x="-16"
                y="-16"
                width="32"
                height="32"
                rx="8"
            />
            <g transform="translate(-16 -16)">
                <rect width="32" height="32" rx="8" class="fill-xq-primary" />
                <path
                    d="M10 10.5 22 21.5M22 10.5 10 21.5"
                    class="stroke-xq-primary-foreground"
                    stroke-width="3"
                    stroke-linecap="round"
                />
                <circle
                    cx="22.5"
                    cy="10"
                    r="2.25"
                    class="fill-xq-primary-foreground"
                />
            </g>
        </g>

        <!-- Eloquent -->
        <g v-for="index in [0, 1, 2]" :key="index">
            <path :d="disc(index).side" class="iso-face-right" />
            <ellipse
                cx="-56"
                :cy="disc(index).top"
                rx="14"
                ry="7"
                class="iso-face-top"
            />
        </g>

        <!-- The admin panel -->
        <IsoBox :x="70" :y="-120" :w="100" :d="80" :h="6" />
        <g :transform="isoFloor(6)">
            <circle
                v-for="dot in [76, 81, 86]"
                :key="dot"
                :cx="dot"
                cy="-115"
                r="1.6"
                class="code-muted"
            />
        </g>

        <IsoBox
            class="iso-tinted"
            :x="76"
            :y="-110"
            :z="6"
            :w="20"
            :d="64"
            :h="2.5"
        />
        <g :transform="isoFloor(8.5)">
            <rect
                v-for="item in [-104, -97, -90, -83]"
                :key="item"
                x="80"
                :y="item"
                :width="item === -104 ? 12 : 9"
                height="3"
                rx="1.5"
                :class="item === -104 ? 'code-kw' : 'code-muted'"
            />
        </g>

        <IsoBox :x="102" :y="-110" :z="6" :w="62" :d="32" :h="2.5" />
        <IsoBox
            class="iso-primary hero-flash"
            :style="{ animationDelay: LANDS_AT }"
            :x="102"
            :y="-110"
            :z="6"
            :w="62"
            :d="32"
            :h="2.5"
        />
        <g :transform="isoFloor(8.5)">
            <rect
                v-for="(height, index) in bars"
                :key="index"
                class="ui-bar code-kw"
                :style="{ '--i': index }"
                :x="108 + index * 9"
                :y="-83 - height"
                width="5"
                :height="height"
                rx="1.2"
            />
        </g>

        <IsoBox :x="102" :y="-72" :z="6" :w="30" :d="26" :h="2.5" />
        <g :transform="isoFloor(8.5)">
            <rect
                x="106"
                y="-67"
                width="22"
                height="4"
                rx="1.5"
                class="code-muted"
            />
            <rect
                x="106"
                y="-60"
                width="15"
                height="4"
                rx="1.5"
                class="code-muted"
            />
            <rect
                x="106"
                y="-53"
                width="11"
                height="5"
                rx="2.5"
                class="hero-toggle"
            />
            <circle
                cx="108.5"
                cy="-50.5"
                r="1.8"
                class="hero-knob fill-white"
            />
        </g>

        <IsoBox :x="136" :y="-72" :z="6" :w="28" :d="26" :h="2.5" />
        <g :transform="isoFloor(8.5)">
            <circle
                cx="150"
                cy="-59"
                r="7"
                class="stroke-xq-border"
                stroke-width="2.5"
            />
            <circle
                cx="150"
                cy="-59"
                r="7"
                pathLength="100"
                class="hero-donut stroke-xq-primary"
                stroke-width="2.5"
            />
        </g>

        <!-- The toast that says it saved -->
        <g class="hero-toast" :style="{ animationDelay: LANDS_AT }">
            <IsoBox :x="95" :y="-30" :z="30" :w="50" :d="16" :h="3" />
            <g :transform="isoFloor(33)">
                <circle cx="102" cy="-22" r="3" class="fill-xq-success" />
                <rect
                    x="108"
                    y="-25"
                    width="28"
                    height="3"
                    rx="1.5"
                    class="code-muted"
                />
                <rect
                    x="108"
                    y="-20"
                    width="18"
                    height="3"
                    rx="1.5"
                    class="code-muted"
                />
            </g>
        </g>

        <g class="hero-fly">
            <IsoBox
                class="iso-primary"
                :x="-5"
                :y="-5"
                :z="28"
                :w="10"
                :d="10"
                :h="10"
            />
        </g>

        <g
            v-for="label in labels"
            :key="label.text"
            class="hero-label"
            :style="{ animationDelay: label.delay }"
        >
            <rect
                :x="label.x - label.width / 2"
                :y="label.y - 7"
                :width="label.width"
                height="14"
                rx="7"
                class="iso-face-top"
            />
            <text
                :x="label.x"
                :y="label.y"
                text-anchor="middle"
                dominant-baseline="central"
                class="fill-xq-foreground font-mono text-[7px] font-medium"
            >
                {{ label.text }}
            </text>
        </g>
    </svg>
</template>
