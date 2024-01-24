<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import {Head, useForm} from '@inertiajs/vue3';
import InputLabel from "@/Components/InputLabel.vue";

const props = defineProps({
    email: {
        type: String,
        required: true,
    },
    token: {
        type: String,
        required: true,
    },
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.store'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Redefinir senha"/>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="email" value="Email"/>

                <input type="email"
                       id="email"
                       class="form-control"
                       v-model="form.email"
                       required
                       autofocus
                       placeholder="seu@email.com"
                       autocomplete="username"
                />

                <InputError class="mt-2" :message="form.errors.email"/>
            </div>

            <div class="mt-4">
                <InputLabel for="password" value="Senha"/>

                <input type="password"
                       id="password"
                       class="form-control"
                       v-model="form.password"
                       placeholder="Sua senha"
                       required
                       autocomplete="new-password"
                />

                <InputError class="mt-2" :message="form.errors.password"/>
            </div>

            <div class="mt-4">

                <InputLabel for="password_confirmation" value="Confirmação de Senha"/>
                <input type="password"
                           id="password_confirmation"
                           class="form-control"
                           v-model="form.password_confirmation"
                           placeholder="Sua senha novamente"
                           required
                           autocomplete="new-password"
                />

                <InputError class="mt-2" :message="form.errors.password_confirmation"/>
            </div>

            <div class="d-flex justify-content-end mt-4">
                <button class="btn btn-primary" :disabled="form.processing">
                    Redefinir senha
                </button>
            </div>
        </form>
    </GuestLayout>
</template>
