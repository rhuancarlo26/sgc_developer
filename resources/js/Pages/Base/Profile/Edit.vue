<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import {Head} from '@inertiajs/vue3';
import Breadcrumb from "@/Components/Breadcrumb.vue";

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});
</script>

<template>
    <Head title="Perfil"/>

    <AuthenticatedLayout>
        <template #header>
            <Breadcrumb :links="[
                    {route: '#', label: 'Perfil'},
                ]"/>
        </template>

        <UpdateProfileInformationForm
            :must-verify-email="mustVerifyEmail"
            :status="status"
            class="mb-4"
        />

        <UpdatePasswordForm class="mb-4"/>

        <DeleteUserForm/>

    </AuthenticatedLayout>
</template>
