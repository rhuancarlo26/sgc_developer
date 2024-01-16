import './bootstrap';
import '../css/app.css';

import {createApp, h} from 'vue';
import {createInertiaApp} from '@inertiajs/vue3';
import {resolvePageComponent} from 'laravel-vite-plugin/inertia-helpers';
import {ZiggyVue} from '../../vendor/tightenco/ziggy/dist/vue.m';

import {can, hasRole, canSeePrefix} from './Utils/PermissionUtils.js';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

import "vue-toastification/dist/index.css";
import Toast from "vue-toastification";

createInertiaApp({
    title: (title) => `${appName}: ${title}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({el, App, props, plugin}) {
        return createApp({render: () => h(App, props)})
            .use(plugin)
            .use(Toast)
            .use(ZiggyVue)
            .mixin({methods: {can, hasRole, canSeePrefix}})
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
