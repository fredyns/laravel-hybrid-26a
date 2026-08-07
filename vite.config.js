import { globSync } from 'node:fs';
import {
    defineConfig
} from 'vite';
import laravel, { refreshPaths } from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import tailwindcss from "@tailwindcss/vite";

// Colocated feature assets (see .ai/rules/Vertical-Slice-Architecture.md §13).
// Requires Node >= 22 for fs.globSync.
const featureAssets = globSync(['app/Features/**/script.js', 'app/Features/**/style.css'])
    .map((path) => path.replaceAll('\\', '/'));

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/passkeys.js',
                ...featureAssets,
            ],
            refresh: [
                ...refreshPaths,
                'app/Features/**',
            ],
            fonts: [
                bunny('Instrument Sans', {
                    weights: [400, 500, 600],
                }),
            ],
        }),
        tailwindcss(),
    ],
    server: {
        cors: true,
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
