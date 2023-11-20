<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="m-0">Dashboard</h2>
        </template>

        <div class="card card-body">
            <h3>Bem vindo, {{ $page.props.auth.user.name}}!</h3>
        </div>
    </AuthenticatedLayout>
</template>
