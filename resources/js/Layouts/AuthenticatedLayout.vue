<script setup>
import '@tabler/core/dist/css/tabler.min.css'
import '@tabler/core/dist/js/tabler.min.js';

import PageFooter from "./Partials/PageFooter.vue";
import Alert from "@/Components/Alert.vue";
import Menu from "./Partials/Menu.vue";
import TopBar from "./Partials/TopBar.vue";
import {usePage} from '@inertiajs/vue3'
import {ref, watch, nextTick} from "vue";

const page = usePage();

const flash = ref({});

defineProps({
    containerType: {type: String, default: 'container-fluid'}
})

let timeoutId = null;

const clearFlashAlertAfter = (seconds) => {
    timeoutId = setTimeout(
        () => flash.value = {},
        seconds * 1000
    );
}

watch(
    () => page.props,
    (pageProps) => {

        flash.value = {};

        nextTick(() => {
            flash.value = pageProps.flash;

            if (flash.value.message) {
                clearTimeout(timeoutId);
                clearFlashAlertAfter(5);
            }
        })


    },
    {immediate: true}
);



</script>

<template>
    <div class="page">

        <TopBar/>
        <Menu/>

        <div class="page-wrapper">
            <Alert
                v-if="flash.message"
                :type="flash.message.type"
                :content="flash.message.content"
                @closeButtonClicked="flash = {}"
            />

            <!-- Meta para o token CSRF -->
            <meta name="csrf-token" :content="usePage().props.csrf_token" />

            <!-- Page Header-->
            <div class="page-header d-print-none" v-if="$slots.header">
                <div class="container-xl">
                    <div class="row g-2 align-items-center">
                        <div class="col">
                            <div class="card card-body">
                                <slot name="header"/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Page body -->
            <div class="page-body">
                <div class="container-xl">
                    <slot  />
                </div>
            </div>

        </div>
    </div>

</template>

<style scoped>
.page {
    min-height: 100vh;
}

</style>
