<script setup lang="ts">
import { cn } from '@xelqun/forms';
import type { TableCell, TableColumn } from '@xelqun/tables';

defineProps<{ column: TableColumn; cell: TableCell }>();
</script>

<template>
    <span class="flex items-center gap-2" data-stock-meter>
        <span
            class="h-1.5 w-16 overflow-hidden rounded-full bg-xq-muted"
            aria-hidden="true"
        >
            <span
                :class="
                    cn(
                        'block h-full rounded-full',
                        cell.percent < 15
                            ? 'bg-xq-destructive'
                            : 'bg-xq-success',
                    )
                "
                :style="{ width: `${cell.percent}%` }"
            />
        </span>
        {{ cell.state }}
    </span>
</template>
