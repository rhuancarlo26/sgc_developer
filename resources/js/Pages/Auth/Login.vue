<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import {Head, Link, useForm} from '@inertiajs/vue3';
import {IconEye, IconEyeCancel} from '@tabler/icons-vue';
import {ref} from "vue";
import InputLabel from "@/Components/InputLabel.vue";

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    }
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const showPassword = ref(null);

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>

        <Head title="Log in"/>

        <div v-if="status" class="mb-4 text-success">
            <strong> {{ status }} </strong>
        </div>

        <form @submit.prevent="submit">

            <div class="mb-3">
                <InputLabel for="email" value="Email"/>
                <input v-model="form.email" id="email" type="email" class="form-control" placeholder="seu@email.com"
                       required autofocus autocomplete="username">
                <InputError class="mt-2" :message="form.errors.email"/>
            </div>

            <div class="mb-2">
                <InputLabel for="password" value="Senha"/>
                <div class="input-group input-group-flat">
                    <input v-model="form.password" :type="showPassword ? 'text' : 'password'" class="form-control"
                           placeholder="Sua senha" id="password"
                           required autocomplete="current-password">
                    <span class="input-group-text">
                            <button type="button" @click="showPassword = !showPassword" class="btn-link link-dark"
                                    data-bs-toggle="tooltip" aria-label="Mostrar senha"
                                    data-bs-original-title="Mostrar senha">
                              <IconEyeCancel v-if="showPassword"/> <IconEye v-else/>
                            </button>
                        </span>
                </div>
                <InputError class="mt-2" :message="form.errors.password"/>
            </div>

            <div class="mb-2">
                <label class="form-check">
                    <input v-model="form.remember" type="checkbox" class="form-check-input">
                    <span class="form-check-label">Lemmbrar credenciais</span>
                </label>
            </div>

            <div class="form-footer">
                <div class="d-flex justify-content-between align-items-center">
                    <Link v-if="canResetPassword" class="text-gray-600" :href="route('password.request')">
                        Esqueceu sua senha?
                    </Link>

                    <button type="submit" class="btn btn-primary" :disabled="form.processing">Log in</button>

                </div>

                <div class="hr-text">ou</div>

                <div>
                    <Link :href="route('register')" class="btn btn-info w-100" :disabled="form.processing">
                        Crie uma conta
                    </Link>
                </div>
            </div>

        </form>

    </GuestLayout>
</template>
