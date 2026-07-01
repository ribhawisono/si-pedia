import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
    build: {
        rollupOptions: {
            output: {
                manualChunks: undefined, // single chunk for KP project simplicity
            },
        },
        minify: 'esbuild',
        cssMinify: true,
        sourcemap: false,
    },
    server: {
        hmr: { overlay: false },
    },
});
