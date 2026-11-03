<script setup>
defineProps({
    situacao: { type: Object, required: true },
});
</script>

<template>
    <span class="badge text-nowrap" :class="`bg-${situacao.cor}-lt`">{{ situacao.label }}</span>
</template>
