<template>
    <!-- LOGO ECOSSISTEMA DNIT -->
    <img :src="usePage().props.app_url + `/img/logo/logoecosistema12.png`" alt="Logo Ecossistema DNIT">
</template>
<script setup lang="ts">
import {usePage} from "@inertiajs/vue3";
</script>
