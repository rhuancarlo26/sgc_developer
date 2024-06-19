import "./bootstrap";
import "../css/app.css";

import { createApp, h } from "vue";
import { createInertiaApp } from "@inertiajs/vue3";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import { ZiggyVue } from "../../vendor/tightenco/ziggy/dist/vue.m";

import { can, hasRole, canSeePrefix } from "./Utils/PermissionUtils.js";

const appName = import.meta.env.VITE_APP_NAME || "Laravel";

// Maska
import { vMaska } from "maska";

import "vue-toastification/dist/index.css";
import Toast from "vue-toastification";

// Leaflet JS & CSS
import leaflet from "leaflet";
import "leaflet/dist/leaflet.css";

// Import V Select
import vSelect from "vue-select";
import "vue-select/dist/vue-select.css";

createInertiaApp({
    title: (title) => `${title}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob("./Pages/**/*.vue")
        ),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(Toast)
            .directive("maska", vMaska)
            .component("v-select", vSelect)
            .use(ZiggyVue)
            .mixin({ data: () => ({ L: leaflet }) })
            .mixin({ methods: { can, hasRole, canSeePrefix } })
            .mount(el);
    },
    progress: {
        color: "#4B5563",
    },
});
