import '../css/app.css';

import { createInertiaApp } from '@inertiajs/vue3';
import { createApp, h } from 'vue';
import { ZiggyVue } from 'ziggy-js';

const appName = 'ClientManager';

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    // Seulement les pages de base - pas de glob récursif
    resolve: (name) => {
        if (name === 'TestSimple') {
            return import('./pages/TestSimple.vue');
        }
        if (name === 'Welcome') {
            return import('./pages/Welcome.vue');
        }
        if (name === 'Dashboard/Index') {
            return import('./pages/Dashboard/Index.vue');
        }
        throw new Error(`Page not found: ${name}`);
    },
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .mount(el);
    },
    progress: {
        color: '#4F46E5',
    },
});