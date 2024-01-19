<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {Head, Link} from "@inertiajs/vue3";
import Table from '@/Components/Table.vue';
import {dateTimeFormat} from '@/Utils/DateTimeUtils.js'
import {router} from '@inertiajs/vue3'
import Breadcrumb from "@/Components/Breadcrumb.vue";
import ModelSearchForm from "@/Components/ModelSearchForm.vue";
import {IconPlus} from "@tabler/icons-vue";

defineProps({
    roles: Object
})

</script>

<template>

    <Head title="Cadastros > Perfis"/>

    <AuthenticatedLayout>

        <template #header>
            <div class="w-100 d-flex justify-content-between">
                <Breadcrumb :links="[
                    {route: '#', label: 'Cadastros'},
                    {route: route('cadastros.perfis.listagem'), label: 'Perfis'},
                ]"/>
            </div>
        </template>

        <div class="card card-body space-y-3">

            <!-- Pesquisa-->
            <ModelSearchForm :search-columns="{'name': 'Nome','created_at' : 'Cadastrado em'}">
                <template #action>
                    <Link class="btn btn-success" :href="route('cadastros.perfis.formulario')">
                        <IconPlus class="me-2"/>
                        Novo Perfil
                    </Link>
                </template>
            </ModelSearchForm>

            <!-- Listagem-->
            <Table :columns="['Nome', 'Cadastrado em']"
                   :only="['roles']"
                   :records="roles"
                   table-class="table-hover">
                <template #body="{item}">
                    <tr class="cursor-pointer" @click="router.get(route('cadastros.perfis.formulario', item.id))">
                        <td>{{ item.name }}</td>
                        <td>{{ dateTimeFormat(item.created_at, {dateStyle: 'short', timeStyle: 'short'}) }}</td>
                    </tr>
                </template>
            </Table>
        </div>

    </AuthenticatedLayout>

</template>
