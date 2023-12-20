<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {Head, useForm} from "@inertiajs/vue3";
import Table from '@/Components/Table.vue';
import {dateTimeFormat} from '@/Utils/DateTimeUtils.js'
import {router} from '@inertiajs/vue3'
import {IconSearch, IconEraser} from '@tabler/icons-vue';
import {onMounted} from "vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";


defineProps({
    users: Object
})


let searchForm = useForm({searchColumn: "", searchValue: null});

onMounted(() => {
    searchForm.searchColumn = route().params.searchColumn ?? "";
    searchForm.searchValue = route().params.searchValue;
})

const search = () => {
    searchForm.get(route('cadastros.usuarios.listagem'));
}


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

            <!-- Pesquisa-->
            <form @submit.prevent="search" class="row align-items-center">
                <div class="col-lg-4 mb-2">
                    <select class="form-select" v-model="searchForm.searchColumn">
                        <option value="" disabled>Pesquisar por</option>
                        <option value="name">Nome</option>
                        <option value="email">Email</option>
                        <option value="roles.name">Perfil</option>
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
                    <button class="btn btn-primary ms-2" title="Pesquisar" :disabled="searchForm.processing">
                        <IconSearch/>
                    </button>
                </div>
            </form>

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
