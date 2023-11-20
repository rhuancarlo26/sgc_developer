<script setup>
defineProps({
    message: {
        type: String,
    },
});
</script>

<template>
    <div v-show="message">
        <small class="text-danger">
            {{ message }}
        </small>
    </div>
</template>
