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
    <li class="nav-item pastel-2" :class="{ active: route().current(activeOnRoutePrefix ?? routeName) }" v-if="can(routeName)">
        <Link class="nav-link" :href="route(routeName, param)" style="width: 100%;">
            <component v-if="icon" :is="icon" class="me-1" />
            <span class="nav-link-title">{{ title }}</span>
        </Link>
    </li>
</template>
<style scoped> 
    .dropdown-item {
        width: 176px;
        height: 36px;
        padding: 0 0 0 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }
</style>