<script setup>

defineProps({
    routeName: { type: String },
    param: {type: Number},
    title: {type: String},
    icon: {type: Function},
    activeOnRoutePrefix: {type: String},
});
</script>

<template>
    <li class="nav-item" :class="{ active: route().current(activeOnRoutePrefix ?? routeName) }" v-if="!routeName || can(routeName)">
        <a class="dropdown-item" href="javascript:void(0)">
            <span class="nav-link-icon d-md-none d-lg-inline-block" v-if="icon">
                <component :is="icon"/>
            </span>
            <span class="nav-link-title">{{ title }}</span>
        </a>
    </li>
</template>
