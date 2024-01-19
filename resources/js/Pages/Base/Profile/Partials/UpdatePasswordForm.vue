<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import {useForm} from '@inertiajs/vue3';
import {ref} from 'vue';

const passwordInput = ref(null);
const currentPasswordInput = ref(null);

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const updatePassword = () => {
    form.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onError: () => {
            if (form.errors.password) {
                form.reset('password', 'password_confirmation');
                passwordInput.value.focus();
            }
            if (form.errors.current_password) {
                form.reset('current_password');
                currentPasswordInput.value.focus();
            }
        },
    });
};
</script>

<template>
    <div class="card">

        <header class="card-header">
            <div>
                <h2 class="my-0">Atualizar senha</h2>
                <small class="small">
                    Certifique-se que sua conta esteja usando uma senha longa, e aleatória para permanecer segura
                </small>
            </div>
        </header>

        <form @submit.prevent="updatePassword" >

            <div class="card-body space-y-5">

                <div class="row">
                    <div class="col-lg-6">
                        <InputLabel for="current_password" value="Senha atual"/>

                        <input
                            id="current_password"
                            ref="currentPasswordInput"
                            v-model="form.current_password"
                            type="password"
                            class="form-control"
                            placeholder="Sua senha atual"
                            autocomplete="current-password"
                        />

                        <InputError :message="form.errors.current_password" class="mt-2"/>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-6">
                        <InputLabel for="password" value="Nova senha"/>

                        <input
                            id="password"
                            ref="passwordInput"
                            v-model="form.password"
                            type="password"
                            class="form-control"
                            placeholder="Sua nova senha"
                            autocomplete="new-password"
                        />

                        <InputError :message="form.errors.password" class="mt-2"/>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-6">
                        <InputLabel for="password_confirmation" value="Confirmação da nova senha"/>

                        <input
                            id="password_confirmation"
                            v-model="form.password_confirmation"
                            type="password"
                            class="form-control"
                            placeholder="Sua nova senha novamente"
                            autocomplete="new-password"
                        />

                        <InputError :message="form.errors.password_confirmation" class="mt-2"/>
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
