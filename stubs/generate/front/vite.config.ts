import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import { kalionJs } from '@kalel1500/kalion-js/dist/plugins/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.ts'],
            refresh: false,
        }),
        tailwindcss(),
        kalionJs()
    ]
});
