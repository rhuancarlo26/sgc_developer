<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { useForm, Link, Head, router } from "@inertiajs/vue3";
import Table from '@/Components/Table.vue';
import { dateTimeFormat } from '@/Utils/DateTimeUtils.js'
import Breadcrumb from "@/Components/Breadcrumb.vue";
import ModelSearchForm from "@/Components/ModelSearchForm.vue";
import { IconCirclePlus, IconSearch, IconEraser, IconFilter } from '@tabler/icons-vue';

defineProps({
    users: Object
})

</script>

<template>

    <Head title="Cadastros > Usuários" />

    <AuthenticatedLayout>

        <template #header>
            <Breadcrumb :links="[
                { route: '#', label: 'Cadastros' },
                { route: route('cadastros.usuarios.listagem'), label: 'Usuários' },
            ]" />
        </template>

        <div class="card card-body">
            <!-- Page title actions -->
            <!-- Pesquisa -->
            <ModelSearchForm :search-columns="{
                'name': 'Nome',
                'email': 'Email',
                'roles.name': 'Perfil',
                'created_at': 'Cadastrado em',
                'updated_at': 'Atualizado em',
            }" />

            <div class="col-auto ms-auto d-print-none mb-2">
                <div class="btn-list">
                    <!-- Cadastrar novo usuário -->
                    <Link style="height: 44px;" class="btn btn-success" :href="route('cadastros.usuarios.formulario')">
                    <IconCirclePlus />
                    </Link>
                </div>
            </div>

            <!-- Listagem-->
            <Table :columns="['Name', 'Email', 'Perfis', 'Cadastrado em', 'Atualizado em']" :records="users"
                table-class="table-hover">
                <template #body="{ item }">
                    <tr class="cursor-pointer" @click="router.get(route('cadastros.usuarios.formulario', item.id))">
                        <td>{{ item?.name }}</td>
                        <td>{{ item?.email }}</td>
                        <td>{{item?.roles.map(r => r?.name).join(', ')}}</td>
                        <td>{{ dateTimeFormat(item.created_at, { dateStyle: 'short', timeStyle: 'short' }) ?? '-' }}
                        </td>
                        <td>{{ dateTimeFormat(item.updated_at, { dateStyle: 'short', timeStyle: 'short' }) ?? '-' }}
                        </td>
                    </tr>
                </template>
            </Table>
        </div>

    </AuthenticatedLayout>

</template>
