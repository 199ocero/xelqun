import { computed, nextTick, onMounted, ref, watch } from 'vue';
import type { Ref, WatchSource } from 'vue';

/** Where an item sits in its list, counting what's scrolled out of view. */
function topWithin(item: HTMLElement, list: HTMLElement): number {
    return (
        item.getBoundingClientRect().top -
        list.getBoundingClientRect().top +
        list.scrollTop
    );
}

function pillClass(isGliding: boolean, color: string): string[] {
    return [
        'absolute inset-x-0 top-0 rounded-xq-sm pointer-events-none',
        color,
        isGliding
            ? 'transition-[transform,height,opacity] duration-(--xq-duration-base) ease-(--xq-ease-out)'
            : 'transition-opacity duration-(--xq-duration-fast)',
    ];
}

/**
 * The sidebar's two sliding pills, as in a panel: one behind the current page and
 * one under the pointer. The list needs `relative`, its items `data-docs-nav-item`.
 */
export function useGlidingPills(
    list: Ref<HTMLElement | undefined>,
    changes: WatchSource,
) {
    const active = ref({
        top: 0,
        height: 0,
        isVisible: false,
        isGliding: false,
    });
    const hover = ref({
        top: 0,
        height: 0,
        isVisible: false,
        isGliding: false,
    });

    let activeItem: HTMLElement | null | undefined;

    function measureActive(): void {
        const current = list.value?.querySelector<HTMLElement>(
            '[data-docs-nav-item][aria-current="page"]',
        );
        // In a closed section it has no size, so the pill has nowhere to sit.
        const item = current?.offsetHeight ? current : null;

        active.value = {
            top: item && list.value ? topWithin(item, list.value) : 0,
            height: item?.offsetHeight ?? 0,
            isVisible: Boolean(item),
            isGliding: active.value.isVisible && Boolean(item),
        };

        // Only for a new page. Opening another section shouldn't move the list.
        if (item !== activeItem) {
            item?.scrollIntoView({ block: 'nearest' });
        }

        activeItem = item;
    }

    function glideTo(event: Event): void {
        const item = (event.target as HTMLElement).closest<HTMLElement>(
            '[data-docs-nav-item]',
        );

        if (item) {
            hover.value = {
                top: topWithin(item, event.currentTarget as HTMLElement),
                height: item.offsetHeight,
                isVisible: true,
                isGliding: hover.value.isVisible,
            };
        }
    }

    function hideGlide(): void {
        hover.value = { ...hover.value, isVisible: false, isGliding: false };
    }

    onMounted(measureActive);
    // A tick later, because v-show applies after a post-flush watcher runs.
    watch(changes, () => void nextTick(measureActive), { flush: 'post' });

    const style = (pill: typeof active.value) => ({
        transform: `translateY(${pill.top}px)`,
        height: `${pill.height}px`,
        opacity: pill.isVisible ? 1 : 0,
    });

    return {
        activeClass: computed(() =>
            pillClass(active.value.isGliding, 'bg-xq-nav-active'),
        ),
        activeStyle: computed(() => style(active.value)),
        glideClass: computed(() =>
            pillClass(hover.value.isGliding, 'bg-xq-nav-hover'),
        ),
        glideStyle: computed(() => style(hover.value)),
        glideTo,
        hideGlide,
    };
}
