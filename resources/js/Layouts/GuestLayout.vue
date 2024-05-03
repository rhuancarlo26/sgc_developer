<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import {Link} from '@inertiajs/vue3';
import '@tabler/core/dist/css/tabler.min.css'
import '@tabler/core/dist/js/tabler.min.js';
</script>

<template>
    <div class="d-flex flex-column guest container-auth">
        <div class="page page-center">
            <div class="container container-tight py-4">
                <div class="text-center">
                    <Link :href="$page.props.app_url">
                        <ApplicationLogo class="w-100 fill-current navbar-brand navbar-brand-autodark"/>
                    </Link>
                </div>

                <div
                    class="w-100 mt-6 card card-body shadow-md rounded">
                    <slot/>
                </div>

            </div>
        </div>
    </div>
</template>

<style scoped>
.container-auth {
    background-image: url('/img/background/bg_login.jpg');
    background-repeat: no-repeat;
    background-size: cover;
    height: 100vh;
}
.page {
    min-height: 90vh !important
}

</style>
