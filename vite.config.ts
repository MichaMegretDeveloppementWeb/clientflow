import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import { defineConfig } from 'vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/js/app.ts'],
            refresh: true,
        }),
        tailwindcss(),
        vue(),
    ],
    optimizeDeps: {
        include: ['vue', '@inertiajs/vue3', 'ziggy-js'],
        force: true
    },
    server: {
        hmr: {
            overlay: false
        }
    },
    build: {
        emptyOutDir: true,
        manifest: 'manifest.json'
    }
});
