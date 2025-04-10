<script setup>

defineProps({
    routeName: { type: String },
    param: { type: [Number, Object] },
    title: { type: String },
    icon: { type: Function },
    activeOnRoutePrefix: { type: String },
    typeButton: { type: String },
    titleButton: { type: String }
});
</script>

<template>
    <button type="button" :class="`btn btn-${typeButton} me-1`" v-if="!routeName || can(routeName)" :title="titleButton">
        <component v-if="icon" :is="icon" />
        {{ title }}
    </button>
</template>
