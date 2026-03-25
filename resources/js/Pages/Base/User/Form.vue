<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import Breadcrumb from "@/Components/Breadcrumb.vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import { IconDeviceFloppy, IconTrash } from "@tabler/icons-vue";
import LinkConfirmation from "@/Components/LinkConfirmation.vue";
import { defineProps, watch } from 'vue';

const props = defineProps({
    roles: { type: Array },
    user: { type: Object },
    contratos: { type: Array }
});

const form = useForm({
    name: null,
    email: null,
    roles: props.user.roles ? props.user.roles.map(a => a.id) : [],
    ...props.user,
    contratos: props.user.contratos ? props.user.contratos.map(c => c.id) : [],
});

if (props.user.roles) {
    form.roles = props.user.roles.map(a => a.id);
}

watch(() => form.data(), (value) => {
    for (let i in value) {
        if (value[i] === '' || value[i]?.length > 1) {
            form.errors[i] = ''
        }
    }
}, { deep: true })

const saveUser = () => {

    form.transform((data) => Object.assign({ id: props.user.id }, data))

    if (props.user.id) {

        form.patch(route('cadastros.usuarios.atualizar', props.user.id));
        return;
    }

    form.post(route('cadastros.usuarios.criar'));
}

</script>

<template>

    <Head title="Perfil" />

    <AuthenticatedLayout>
        <template #header>
            <Breadcrumb :links="[
                { route: '#', label: 'Cadastros' },
                { route: route('cadastros.usuarios.listagem'), label: 'Usuários' },
                { route: route('cadastros.usuarios.formulario', route().params), label: user.name ?? 'Novo usuário' }
            ]" />
        </template>

        <form @submit.prevent="saveUser">

            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="my-0">Informações pessoais</h3>
                </div>
                <div class="card-body">
                    <div class="row row-gap-2">
                        <div class="col-lg-6">
                            <InputLabel value="Nome" for="nome" />
                            <input type="text" id="nome" class="form-control" v-model="form.name" />
                            <InputError :message="form.errors.name" class="mb-2" />
                        </div>
                        <div class="col-lg-6">
                            <InputLabel value="Email" for="email" />
                            <input type="email" id="email" class="form-control" v-model="form.email" />
                            <InputError :message="form.errors.email" class="mb-2" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="card mb-4">
                <div class="card-header">
                    <div>
                        <h3 class="my-0">Perfis de Acesso</h3>
                        <InputError :message="form.errors.roles" class="mt-2" />
                    </div>
                </div>
                <div class="card-body">
                    <!-- Perfis -->
                    <div class="mb-3">
                        <InputLabel for="perfil" value="Perfil de Acesso" />
                        <select class="form-select" id="perfil" v-model="form.roles" @change="handleRoleChange">
                            <option disabled value="">Selecione um perfil</option>
                            <option v-for="role in roles" :key="role.id" :value="[role.id]">
                                {{ role.name }}
                            </option>
                        </select>
                        <InputError :message="form.errors.roles" class="mt-2" />
                    </div>

                </div>
            </div>
            <div class="card mb-4" v-if="form.roles.some(id => [2, 3].includes(id))">
                <div class="card-header">
                    <h3 class="my-0">Contratos vinculados</h3>
                    <InputError :message="form.errors.contratos" class="mt-2" />
                </div>
                <div class="card-body">
                    <v-select v-model="form.contratos" :options="contratos" :reduce="contrato => contrato.id"
                        label="numero_contrato" multiple placeholder="Selecione os contratos" :close-on-select="false"
                        class="form-control">
                        <template #option="{ numero_contrato, contratada }">
                            <div>
                                <strong>{{ numero_contrato }}</strong> - {{ contratada }}
                            </div>
                        </template>
                        <template #selected-option="{ numero_contrato }">
                            <span>{{ numero_contrato }}</span>
                        </template>
                    </v-select>
                </div>

            </div>

            <!-- Ações -->
            <div class="card card-body">
                <div class="d-flex justify-content-end gap-2">

                    <LinkConfirmation v-if="user.id" v-slot="confirmation"
                        :options="{ text: 'Usuários removidos não podem ser restaurados' }">
                        <Link :onBefore="confirmation.show" :href="route('cadastros.usuarios.deletar', user.id)"
                            method="DELETE" as="button" type="button" class="btn btn-danger">
                        <IconTrash class="me-2" />
                        Deletar
                        </Link>
                    </LinkConfirmation>

                    <button type="submit" class="btn btn-primary" :disabled="form.processing">
                        <IconDeviceFloppy class="me-2" />
                        Salvar
                    </button>
                </div>
            </div>

        </form>


    </AuthenticatedLayout>
</template>
