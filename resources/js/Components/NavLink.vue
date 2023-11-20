<script setup>
import {Link} from "@inertiajs/vue3";

defineProps({
    routeName: {type: String},
    title: {type: String},
    icon: {type: Function}
});
</script>

<template>
    <li class="nav-item" :class="{active: route().current(routeName)}" v-if="can(routeName)">
        <Link class="nav-link" :href="route(routeName)">
            <span class="nav-link-icon d-md-none d-lg-inline-block">
                <component :is="icon"/>
            </span>
            <span class="nav-link-title">{{ title }}</span>
        </Link>
    </li>
</template>
