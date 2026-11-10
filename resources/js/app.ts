import { createInertiaApp } from '@inertiajs/vue3';
import { createXelqunForms } from '@xelqun/forms';
import { panelsPlugin } from '@xelqun/panels';
import { tablesPlugin } from '@xelqun/tables';
import { MessageSquare, Newspaper } from 'lucide-vue-next';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

void createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    withApp: (app) => {
        app.use(
            createXelqunForms({
                plugins: [panelsPlugin(), tablesPlugin()],
                icons: {
                    'message-square': MessageSquare,
                    newspaper: Newspaper,
                },
                components: {
                    'app/stock': () =>
                        import('./components/columns/StockCell.vue'),
                    'app/rating': () =>
                        import('./components/fields/RatingField.vue'),
                    'demo/sidebar-card': () =>
                        import('./components/demo/SidebarCard.vue'),
                    'demo/appearance-switcher': () =>
                        import('./components/demo/AppearanceSwitcher.vue'),
                    'demo/login-hint': () =>
                        import('./components/demo/LoginHint.vue'),
                },
            }),
        );
        app.directive('focus', {
            mounted: (el: HTMLElement, shouldFocus) => {
                if (shouldFocus.value !== false) {
                    el.focus();
                }
            },
        });
    },
    progress: {
        color: '#0071e3',
    },
});
