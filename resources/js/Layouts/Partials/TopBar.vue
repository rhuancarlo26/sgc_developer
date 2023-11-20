<script setup>
import ThemeSwitcher from "./ThemeSwitcher.vue";
import {Link} from "@inertiajs/vue3";
import UserNotifications from "./UserNotifications.vue";
import UserActions from "./UserActions.vue";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
</script>

<template>
    <header class="navbar navbar-expand-md d-print-none">
        <div class="container-xl">

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu"
                    aria-controls="navbar-menu" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <h1 class="navbar-brand navbar-brand-autodark d-none-navbar-horizontal pe-0 pe-md-3">
                <Link :href="route('dashboard')">
                    <ApplicationLogo class="fill-current text-primary navbar-brand-image navbar-brand-autodark"/>
                </Link>
            </h1>

            <div class="navbar-nav flex-row order-md-last">
                <div class="d-none d-md-flex me-2">
                    <ThemeSwitcher/>
                    <!-- <UserNotifications/>-->
                </div>
                <UserActions/>
            </div>

        </div>
    </header>
</template>

