<script setup>
import { Link } from "@inertiajs/vue3";

defineProps({
    routeName: { type: String },
    param: { type: Number },
    title: { type: String },
    icon: { type: Function },
    activeOnRoutePrefix: { type: String }
});
</script>

<template>
    <li class="nav-item" :class="{ active: route().current(activeOnRoutePrefix ?? routeName) }" v-if="can(routeName)">
        <Link class="nav-link" :href="route(routeName, param)">
        <span class="nav-link-icon d-md-none d-lg-inline-block" v-if="icon">
            <component :is="icon" />
        </span>
        <span class="nav-link-title">{{ title }}</span>
        </Link>
    </li>
</template>
