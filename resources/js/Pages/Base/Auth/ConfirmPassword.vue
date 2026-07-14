<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import {Head, useForm} from '@inertiajs/vue3';
import InputLabel from "@/Components/InputLabel.vue";

const form = useForm({
    password: '',
});

const submit = () => {
    form.post(route('password.confirm'), {
        onFinish: () => form.reset(),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Confirm Password"/>

        <div class="mb-4 text-gray-600">
            Essa é uma área protegida do sistema. Por favor, confirme sua senha antes de continuar.
        </div>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="password" value="Senha"/>
                <input type="password"
                       id="password"
                       class="form-control"
                       v-model="form.password"
                       required
                       placeholder="Sua senha"
                       autocomplete="current-password"
                       autofocus
                />
                <InputError class="mt-2" :message="form.errors.password"/>
            </div>

            <div class="d-flex justify-content-end mt-4">
                <button class="btn btn-primary" :disabled="form.processing">
                    Confirmar
                </button>
            </div>
        </form>
    </GuestLayout>
</template>
