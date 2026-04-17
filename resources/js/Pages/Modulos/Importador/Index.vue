<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router, usePage } from "@inertiajs/vue3";
import { IconCirclePlus, IconDots, IconEye, IconTrash, IconDownload, IconAlertTriangle } from '@tabler/icons-vue';
import Breadcrumb from "@/Components/Breadcrumb.vue";
import ModelSearchForm from "@/Components/ModelSearchFormAllColumns.vue";
import Table from '@/Components/Table.vue';
import NavLink from "@/Components/NavLink.vue";
import { dateTimeFormat } from '@/Utils/DateTimeUtils';

import ModalErros from "./Components/ModalErros.vue"
import { ref } from "vue";

const props = defineProps({
    modulos: Object,
    importadores: Object
})

const ModalErrosRef = ref(null)
const abrirModalErros = (erros) => {
    ModalErrosRef.value.abrirModal(erros)
}

</script>

<template>

  <Head title="Modulos" />

  <AuthenticatedLayout>

    <template #header>
        <div class="w-100 d-flex justify-content-between">
            <Breadcrumb class="align-self-center" :links="[
                { route: route('modulos.importador.index'), label: `Importador` }
            ]" />
            <NavLink route-name="modulos.importador.formulario" title="Nova Importação" :icon="IconCirclePlus"
                class="btn btn-info me-2"/>
        </div>
    </template>


    <div class="card card-body">
        <!-- Pesquisa -->
        <ModelSearchForm :columns="[
            'nome',
        ]" />

        <!-- Listagem-->
        <Table :columns="['Modulo', 'Mes/Ano Referencia', 'Status', 'Revisão', 'Atualizado em', 'Ações']" :records="importadores"
            table-class="table-hover">
            <template #body="{ item }">
                <tr class="cursor-pointer">
                    
                    <td class="text-center">{{ item.modulo?.nome }}</td>
                    <td class="text-center">{{ item.mes_ano_referencia }}</td>
                    <td class="text-center">{{ item.status }}</td>
                    <td class="text-center">revisao</td>
                    <td class="text-center">{{ dateTimeFormat(item.updated_at) }}</td>

                    <td class="text-center">                        
                        <div class="d-flex gap-2 justify-content-center">
                            <button v-if="item.load" type="button" class="d-flex gap-2 btn btn-sm btn-primary">
                                Importando 
                                <div class="spinner-border spinner-border-sm text-light" role="status" style="border-width: 3px">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </button>
                            <template v-else>
                                <button v-if="item.desc_erros" type="button" @click="abrirModalErros(item.desc_erros)" class="btn btn-sm btn-warning">
                                    <IconAlertTriangle/>
                                </button>
                                <Link :href="route('modulos.importador.formulario', [item.id])" type="button" class="btn btn-sm btn-info">
                                    <IconEye/>
                                </Link>
                                <button type="button" class="btn btn-sm btn-danger">
                                    <IconTrash/>
                                </button>
                            </template>
                        </div>
                    </td>
                </tr>
            </template>
        </Table>

    </div>

    <ModalErros ref="ModalErrosRef" />

  </AuthenticatedLayout>
</template>
