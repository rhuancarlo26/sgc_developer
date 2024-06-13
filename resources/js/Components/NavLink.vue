<script setup>
import { Link } from "@inertiajs/vue3";

defineProps({
    routeName: { type: String },
    param: { type: [Number, Object] },
    title: { type: String },
    icon: { type: Function },
    activeOnRoutePrefix: { type: String }
});
</script>

<template>
    <li class="nav-item" :class="{ active: route().current(activeOnRoutePrefix ?? routeName) }" v-if="can(routeName)">
        <Link class="nav-link" :href="route(routeName, param)">
        <component v-if="icon" :is="icon" class="me-1" />
        <span class="nav-link-title">{{ title }}</span>
        </Link>
    </li>
</template>
