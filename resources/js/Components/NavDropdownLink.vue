<script setup>
import { Link } from "@inertiajs/vue3";

defineProps({
    routeName: { type: String },
    routeParam: { type: String },
    title: { type: String },
    activeOnRoutePrefix: { type: String }
})

</script>

<template>
    <Link class="dropdown-item" v-if="can(routeName)" :class="{ active: route().current(activeOnRoutePrefix) }"
        :href="route(routeName, routeParam)">
    {{ title }}
    </Link>
</template>
