<script setup>
import {computed} from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import {Head, Link, useForm} from '@inertiajs/vue3';

const props = defineProps({
    status: {
        type: String,
    },
});

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};

const verificationLinkSent = computed(() => props.status === 'verification-link-sent');
</script>

<template>
    <GuestLayout>
        <Head title="Email Verification"/>

        <div class="mb-4 text-gray-600">
            Obrigado por se cadastrar! Antes de continuar, você poderia confirmar seu cadastro clicando no link que
            acabamos de enviar no seu email? Se você não recebeu o email, teremos prazer em lhe enviar outro.
        </div>

        <div class="mb-4 fw-medium text-success" v-if="verificationLinkSent">
            Um novo link de confirmação foi enviado ao email informado durante o cadastro.
        </div>

        <form @submit.prevent="submit">
            <div class="mt-4 d-flex align-items-center justify-content-between">
                <button class="btn btn-primary" :disabled="form.processing">
                    Reenviar Email de Confirmação
                </button>
                <Link
                    :href="route('logout')"
                    method="post"
                    class="text-gray-600">Log Out
                </Link
                >
            </div>
        </form>
    </GuestLayout>
</template>
