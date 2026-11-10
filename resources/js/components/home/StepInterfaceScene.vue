<script setup lang="ts">
import IsoBox from '@/components/home/IsoBox.vue';
import { isoFloor } from '@/lib/iso';

const bars = [14, 24, 18, 30, 22, 27];
const rows = [64, 72, 80, 88];
</script>

<template>
    <svg viewBox="-92 -24 210 140" fill="none" class="iso-scene">
        <IsoBox :x="0" :y="0" :w="130" :d="100" :h="6" />
        <g :transform="isoFloor(6)">
            <circle
                v-for="dot in [7, 13, 19]"
                :key="dot"
                :cx="dot"
                cy="7"
                r="2"
                class="code-muted"
            />
        </g>

        <!-- The panel assembles itself: sidebar, then the chart, then the table. -->
        <g class="ui-layer" style="--i: 0">
            <IsoBox
                class="iso-tinted"
                :x="8"
                :y="14"
                :z="6"
                :w="26"
                :d="80"
                :h="3"
            />
            <g :transform="isoFloor(9)">
                <rect
                    v-for="item in [22, 31, 40, 49]"
                    :key="item"
                    x="13"
                    :y="item"
                    :width="item === 22 ? 16 : 12"
                    height="3.5"
                    rx="1.75"
                    :class="item === 22 ? 'code-kw' : 'code-muted'"
                />
            </g>
        </g>

        <g class="ui-layer" style="--i: 1">
            <IsoBox :x="42" :y="14" :z="6" :w="80" :d="38" :h="3" />
            <g :transform="isoFloor(9)">
                <rect
                    v-for="(height, index) in bars"
                    :key="index"
                    class="ui-bar code-kw"
                    :style="{ '--i': index }"
                    :x="52 + index * 11"
                    :y="46 - height"
                    width="7"
                    :height="height"
                    rx="1.5"
                />
            </g>
        </g>

        <g class="ui-layer" style="--i: 2">
            <IsoBox :x="42" :y="58" :z="6" :w="80" :d="36" :h="3" />
            <g :transform="isoFloor(9)">
                <g
                    v-for="(row, index) in rows"
                    :key="row"
                    class="ui-row"
                    :style="{ '--i': index }"
                >
                    <rect
                        x="50"
                        :y="row"
                        width="34"
                        height="3.5"
                        rx="1.75"
                        class="code-muted"
                    />
                    <rect
                        x="98"
                        :y="row"
                        width="16"
                        height="3.5"
                        rx="1.75"
                        :class="index === 0 ? 'code-str' : 'code-fn'"
                    />
                </g>
            </g>
        </g>
    </svg>
</template>
