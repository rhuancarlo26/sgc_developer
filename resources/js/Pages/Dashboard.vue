<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import Navbar from './Dashboard/Navbar.vue';
</script>

<template>

    <Head title="Dashboard" />

    <AuthenticatedLayout>

        <Navbar />
        <div class="card card-body">

            <h3>Bem vindo, {{ $page.props.auth.user.name }}!</h3>
            <span class="text-white">{{ usePage().props.app_url }}</span>
        </div>
    </AuthenticatedLayout>
</template>
