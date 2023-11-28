<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {Head, useForm, useRemember} from "@inertiajs/vue3";
import Table from '@/Components/Table.vue';
import {dateTimeFormat} from '@/Utils/DateTimeUtils.js'
import {router, Link} from '@inertiajs/vue3'
import {IconSearch, IconEraser, IconPlus} from '@tabler/icons-vue';
import {onMounted} from "vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";

defineProps({
    roles: Object
})

let searchForm = useForm({searchColumn: "", searchValue: null});

onMounted(() => {
    searchForm.searchColumn = route().params.searchColumn ?? "";
    searchForm.searchValue = route().params.searchValue;
})

const search = () => {
    searchForm.get(route('cadastros.perfis.listagem'));
}


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
            <form @submit.prevent="search" class="row align-items-center">
                <div class="col-lg-4 mb-2">
                    <select class="form-select" v-model="searchForm.searchColumn">
                        <option value="" disabled>Pesquisar por</option>
                        <option value="name">Nome</option>
                        <option value="created_at">Cadastrado em</option>
                    </select>
                </div>
                <div class="col-lg-4 mb-2">
                    <input v-model="searchForm.searchValue"
                           placeholder="..."
                           :type="searchForm.searchColumn === 'created_at' ? 'date' : 'text'" class="form-control">
                </div>
                <div class="col-lg-4 mb-2 text-end">
                    <button
                        type="button"
                        @click="searchForm.reset()"
                        class="btn btn-secondary"
                        title="Limpar Filtros">
                        <IconEraser/>
                    </button>
                    <button class="btn btn-primary mx-2" title="Pesquisar" :disabled="searchForm.processing">
                        <IconSearch/>
                    </button>
                    <Link class="btn btn-success" :href="route('cadastros.perfis.formulario')">
                        <IconPlus class="me-2"/>
                        Novo Perfil
                    </Link>
                </div>
            </form>

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
