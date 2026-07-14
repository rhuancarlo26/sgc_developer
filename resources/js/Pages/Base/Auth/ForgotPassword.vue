<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import {Head, useForm} from '@inertiajs/vue3';
import InputLabel from "@/Components/InputLabel.vue";

defineProps({
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <GuestLayout>
        <Head title="Esqueci minha senha"/>

        <div class="mb-4 text-gray-600">
            Esqueceu sua senha? Sem problemas. Basta nos informar seu endereço de e-mail, e enviaremos um e-mail com um
            link para redefinição de senha
            que permitirá que você escolha uma nova.
        </div>

        <div v-if="status" class="mb-4 text-success">
            <strong> {{ status }} </strong>
        </div>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="email" value="Email" />
                <input type="email"
                       id="email"
                       class="form-control"
                       v-model="form.email"
                       required
                       autofocus
                       placeholder="seu@email.com"
                       autocomplete="username">

                <InputError class="mt-2" :message="form.errors.email"/>
            </div>

            <div class="d-flex align-items-center justify-content-end mt-4">
                <button class="btn btn-primary" :disabled="form.processing">
                    Enviar link para redefinição de senha
                </button>
            </div>
        </form>
    </GuestLayout>
</template>
