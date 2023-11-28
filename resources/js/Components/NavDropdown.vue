<script setup>

defineProps({
    prefix: {type: String},
    icon: {type: [Function, Object]},
    title: {type: String}
})
</script>

<template>
    <li class="nav-item dropdown" :class="{active: route().current(prefix)}" v-if="canSeePrefix(prefix)">
        <a class="nav-link dropdown-toggle" href="#"
           data-bs-toggle="dropdown" data-bs-auto-close="outside" role="button"
           aria-expanded="false">
            <span class="nav-link-icon d-md-none d-lg-inline-block">
                <component :is="icon"/>
            </span>
            <span class="nav-link-title"> {{ title }} </span>
        </a>
        <div class="dropdown-menu" data-bs-popper="static">
            <slot/>
        </div>
    </li>
</template>
