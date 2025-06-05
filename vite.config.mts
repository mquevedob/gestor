import vue from "@vitejs/plugin-vue";
import {defineConfig} from "vite";
import path from 'path';

export default defineConfig({
    plugins: [vue()],
    resolve: {
        alias: {
            '@': path.resolve(__dirname, './resources/js'), // Existing alias for your project's scripts
        },
    },
    server: {
        hmr: {
            host: 'localhost',
            protocol: 'ws'
        }
    },
    build: {
        rollupOptions: {
            input: 'resources/scripts/main.ts',
        },
        copyPublicDir: false,
        assetsDir: 'assets',
        outDir: 'public/build',
        manifest: 'manifest.json',
    },
    base: '/build/',

})
