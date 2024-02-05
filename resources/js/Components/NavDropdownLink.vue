<script setup>
import { Link } from "@inertiajs/vue3";
import { computed } from "vue";

const props = defineProps({
    routeName: { type: String },
    title: { type: String },
    activeOnRoutePrefix: { type: String },
    routeParam: { type: String, default: null },
    paramName: { type: String, default: null },
})

const isActive = computed(() => {

    const isCurrent = route().current(props.activeOnRoutePrefix);

    if (props.routeParam === null) {
        return isCurrent
    }

    return isCurrent && props.routeParam == route().params[props.paramName]

})
</script>

<template>
    <Link class="dropdown-item" v-if="can(routeName)" :class="{ active: isActive }" :href="route(routeName, routeParam)">
    {{ title }}
    </Link>
</template>
