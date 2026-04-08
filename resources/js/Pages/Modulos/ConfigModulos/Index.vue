<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { IconCirclePlus, IconDots } from '@tabler/icons-vue';
import Breadcrumb from "@/Components/Breadcrumb.vue";
import ModelSearchForm from "@/Components/ModelSearchFormAllColumns.vue";
import Table from '@/Components/Table.vue';
import { format } from 'date-fns'
import { ptBR } from 'date-fns/locale'
import NavLink from "@/Components/NavLink.vue";

const props = defineProps({
  modulos: Object
})

const formatarData = (data) => {
    if (!data) return '-'
    return format(new Date(data), 'dd/MM/yyyy', { locale: ptBR })
}

</script>

<template>

  <Head title="Modulos" />

  <AuthenticatedLayout>

    <template #header>
        <div class="w-100 d-flex justify-content-between">
            <Breadcrumb class="align-self-center" :links="[
                { route: route('modulos.config-modulos.index'), label: `Módulos` }
            ]" />
            <NavLink route-name="modulos.config-modulos.formulario" title="Novo Módulo" :icon="IconCirclePlus"
                    class="btn btn-info me-2"/>
        </div>
    </template>


    <div class="card card-body">
            <!-- Pesquisa -->
            <ModelSearchForm :columns="[
                'name',
                'email',
                'roles.name',
                'created_at',
                'updated_at',
            ]" />

            <!-- Listagem-->
            <Table :columns="['Nome', 'Campos', 'Planilha Modelo', 'Criado em', 'Ações']" :records="modulos"
                table-class="table-hover">
                <template #body="{ item }">
                    <tr class="cursor-pointer">
                        <td>{{ item.nome }}</td>
                        <td>{{ item.campos }}</td>
                        <td>{{ item.planilha_modelo }}</td>
                        <td>{{ formatarData(item.created_at) }}</td>
                        <td class="text-center">
                            <button type="button" class="btn btn-icon btn-info dropdown-toggle p-2"
                                    data-bs-boundary="viewport" data-bs-toggle="dropdown" aria-expanded="false">
                                <IconDots/>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a class="dropdown-item" href="javascript:void(0)">
                                    Visualizar
                                </a>
                            </div>
                        </td>
                    </tr>
                </template>
            </Table>
        </div>

  </AuthenticatedLayout>
</template>
