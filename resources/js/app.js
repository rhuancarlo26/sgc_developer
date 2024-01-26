import "./bootstrap";
import "../css/app.css";

import { createApp, h } from "vue";
import { createInertiaApp } from "@inertiajs/vue3";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import { ZiggyVue } from "../../vendor/tightenco/ziggy/dist/vue.m";

import { can, hasRole, canSeePrefix } from "./Utils/PermissionUtils.js";

const appName = import.meta.env.VITE_APP_NAME || "Laravel";

import "vue-toastification/dist/index.css";
import Toast from "vue-toastification";

// Leaflet JS & CSS
import leaflet from "leaflet";
import "leaflet/dist/leaflet.css";

createInertiaApp({
    title: (title) => `${appName}: ${title}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob("./Pages/**/*.vue")
        ),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(Toast)
            .use(ZiggyVue)
            .mixin({ data: () => ({ L: leaflet }) })
            .mixin({ methods: { can, hasRole, canSeePrefix } })
            .mount(el);
    },
    progress: {
        color: "#4B5563",
    },
});
