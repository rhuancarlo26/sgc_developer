<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import {Link, useForm, usePage} from '@inertiajs/vue3';

const props = defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
    user: {
        type: Object, required: false
    }
});

const user = usePage().props.auth.user;

const form = useForm({
    name: user.name,
    email: user.email,
});
</script>

<template>
    <div class="card">

        <header class="card-header">
            <div>
                <h2 class="my-0">Informações de perfil</h2>
                <small>
                    Atualize informações de perfil e endereço de Email
                </small>
            </div>
        </header>

        <form @submit.prevent="form.patch(route('profile.update'))">
            <div class="card-body space-y-5">

                <div class="row">
                    <div class="col-lg-6">
                        <InputLabel for="name" value="Nome"/>
                        <input
                            id="name"
                            type="text"
                            class="form-control"
                            v-model="form.name"
                            required
                            autofocus
                            placeholder="Seu nome"
                            autocomplete="name"
                        />
                        <InputError class="mt-2" :message="form.errors.name"/>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-6">
                        <InputLabel for="email" value="Email"/>
                        <input
                            id="email"
                            type="email"
                            class="form-control"
                            v-model="form.email"
                            required
                            placeholder="seu@email.com"
                            autocomplete="username"
                        />
                        <InputError class="mt-2" :message="form.errors.email"/>
                    </div>
                </div>

                <div v-if="mustVerifyEmail && user.email_verified_at === null">
                    <p class="small mt-2 text-gray-800">
                        Seu email ainda não foi verificado
                        <Link
                            :href="route('verification.send')"
                            method="post"
                            as="button"
                            class="btn-link link-secondary text-decoration-underline"
                        >
                            Clique aqui para reenviar o email de verificação
                        </Link>
                    </p>

                    <div
                        v-show="status === 'verification-link-sent'"
                        class="mt-2 strong text-success">
                        Email de verificação reenviado
                    </div>
                </div>
            </div>

            <div class="card-footer">
                <div class="d-flex align-items-center">
                    <button class="btn btn-primary" :disabled="form.processing">Atualizar</button>
                    <span v-if="form.recentlySuccessful" class="ms-3 text-success fw-light">Atualizado!</span>
                </div>
            </div>

        </form>


    </div>
</template>
