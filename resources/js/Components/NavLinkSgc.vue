<script setup>
defineProps({
    routeName: { type: String },
    param: { type: Number },
    title: { type: String },
    icon: { type: Function },
    activeOnRoutePrefix: { type: String },
    disabled: { type: Boolean, default: false }, // Propriedade para desabilitar
    onClick: { type: Function, default: null } // Propriedade para a função de clique
});
</script>

<template>
    <li 
        class="nav-item" 
        :class="{ active: route().current(activeOnRoutePrefix ?? routeName), disabled: disabled }"
        v-if="!routeName || can(routeName)"
    >
        <a 
            class="dropdown-item" 
            href="javascript:void(0)" 
            @click="disabled ? $event.preventDefault() : onClick ? onClick() : router.visit(route(routeName, param))"
        >
            <span class="nav-link-icon d-md-none d-lg-inline-block" v-if="icon">
                <component :is="icon"/>
            </span>
            <span class="nav-link-title">{{ title }}</span>
        </a>
    </li>
</template>

<style>
.disabled .dropdown-item {
    pointer-events: none;
    opacity: 0.5;
}
</style>
