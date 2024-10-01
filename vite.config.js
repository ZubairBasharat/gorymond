import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import vue from "@vitejs/plugin-vue";
import svgLoader from "vite-svg-loader";

export default defineConfig({
    plugins: [
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
        svgLoader(),

        laravel({
            input: [
                "resources/scss/app.scss",
                "resources/css/app.css",
                "resources/css/tippy-goraymond.css",
                "resources/js/app.js",
            ],
            refresh: true,
        }),
    ],

    server: {
        logLevel: "debug", // Or 'debug' for even more output
    },
});
