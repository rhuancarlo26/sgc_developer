<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import {Head, Link, useForm} from '@inertiajs/vue3';
import InputLabel from "@/Components/InputLabel.vue";

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Cadastro"/>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="name" value="Nome"/>

                <input type="text"
                       id="name"
                       class="form-control"
                       v-model="form.name"
                       required
                       autofocus
                       placeholder="Seu nome"
                       autocomplete="nome">

                <InputError class="mt-2" :message="form.errors.name"/>
            </div>

            <div class="mt-4">
                <InputLabel for="email" value="Email"/>

                <input type="email"
                       id="email"
                       class="form-control"
                       v-model="form.email"
                       required
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
                       required
                       placeholder="Sua senha"
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
                       required
                       placeholder="Sua senha novamente"
                       autocomplete="new-password"
                />

                <InputError class="mt-2" :message="form.errors.password_confirmation"/>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-4">
                <Link
                    :href="route('login')"
                    class="text-sm text-gray-600"
                >
                    Já tem cadastro?
                </Link>

                <button class="ml-4 btn btn-primary" :disabled="form.processing">
                    Cadastrar
                </button>
            </div>
        </form>
    </GuestLayout>
</template>
