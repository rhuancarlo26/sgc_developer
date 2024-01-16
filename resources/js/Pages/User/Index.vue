<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {Head, router} from "@inertiajs/vue3";
import Table from '@/Components/Table.vue';
import {dateTimeFormat} from '@/Utils/DateTimeUtils.js'
import Breadcrumb from "@/Components/Breadcrumb.vue";
import ModelSearchForm from "@/Components/ModelSearchForm.vue";

defineProps({
    users: Object
})

</script>

<template>

    <Head title="Cadastros > Usuários"/>

    <AuthenticatedLayout>

        <template #header>
            <Breadcrumb :links="[
                    {route: '#', label: 'Cadastros'},
                    {route: route('cadastros.usuarios.listagem'), label: 'Usuários'},
                ]"/>
        </template>

        <div class="card card-body space-y-3">

            <!-- Pesquisa -->
            <ModelSearchForm :search-columns="{
                'name': 'Nome',
                'email': 'Email',
                'roles.name': 'Perfil',
                'created_at' : 'Cadastrado em'
            }"/>

            <!-- Listagem-->
            <Table :columns="['Name', 'Email', 'Perfis', 'Cadastrado em']" :records="users" table-class="table-hover">
                <template #body="{item}">
                    <tr class="cursor-pointer" @click="router.get(route('cadastros.usuarios.formulario', item.id))">
                        <td>{{ item.name }}</td>
                        <td>{{ item.email }}</td>
                        <td>{{ item.roles.map(r => r.name).join(', ') }}</td>
                        <td>{{ dateTimeFormat(item.created_at, {dateStyle: 'short', timeStyle: 'short'}) }}</td>
                    </tr>
                </template>
            </Table>
        </div>

    </AuthenticatedLayout>

</template>
