import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import { viteStaticCopy } from "vite-plugin-static-copy";
import reactRefresh from "@vitejs/plugin-react-refresh";

export default defineConfig({
    build: {
        manifest: true,
        rtl: true,
        outDir: "public/build/",
        cssCodeSplit: true,
        rollupOptions: {
            output: {
                assetFileNames: (css) => {
                    if (css.name.split(".").pop() == "css") {
                        return "css/[name].[hash].min.css";
                    } else {
                        return "icons/[name].[hash]";
                    }
                },
                entryFileNames: "js/[name].[hash].js",
            },
        },
    },
    plugins: [
        laravel({
            input: [
                "resources/scss/app.scss",
                "resources/js/app.js",
                "resources/scss/main.scss",
                "resources/js/components/CreditCard.jsx",
                "resources/js/components/DatePicker.jsx",
            ],
            refresh: true,
        }),
        viteStaticCopy({
            targets: [
                {
                    src: "resources/fonts",
                    dest: "",
                },
                {
                    src: "resources/images",
                    dest: "",
                },
                {
                    src: "resources/js",
                    dest: "",
                },
            ],
        }),
        reactRefresh(),
    ],
});
