<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import Table from '@/Components/Table.vue';
import Breadcrumb from "@/Components/Breadcrumb.vue";
import ModelSearchForm from "@/Components/ModelSearchFormAllColumns.vue";
import { ref } from "vue";
import { IconSettings } from "@tabler/icons-vue";
import { IconDots } from "@tabler/icons-vue";
import ModalVisualizarContrato from "./ModalVisualizarContrato.vue";
import ModalVisualizarTrecho from "./ModalVisualizarTrecho.vue";

const props = defineProps({
    contratos: Object,
    tipo: Object
})

const modalVisualizarTrecho = ref();
const modalVisualizarContrato = ref();

const abrirVisualizarTrecho = () => {
    modalVisualizarTrecho.value.abrirModal();
}

const abrirVisualizarContrato = (contrato) => {
    modalVisualizarContrato.value.abrirModal(contrato);
}

const formatarCnpj = (cnpj) => {
    return cnpj.replace(/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})$/, '$1.$2.$3/$4-$5');
}

</script>

<template>

    <Head :title="`Gestão de Contratos - ${tipo.nome}`" />

    <AuthenticatedLayout>

        <template #header>
            <div class="w-100 d-flex justify-content-between">
                <Breadcrumb class="align-self-center" :links="[
                    { route: '#', label: `Gestão de Contratos - ${tipo.nome}` }
                ]" />
                <div>
                    <Link class="btn btn-info me-2" :href="route('contratos.gestao.create', tipo.id)">
                    Importar contrato
                    </Link>
                    <!-- Contratos -->
                    <button type="button" class="btn btn-icon btn-info dropdown-toggle p-2" data-bs-boundary="viewport"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <IconSettings />
                    </button>
                    <div class="dropdown-menu dropdown-menu-end">
                        <a @click="abrirVisualizarTrecho()" class="dropdown-item" href="javascript:void(0)">
                            Mapa dos contratos
                        </a>
                        <a class="dropdown-item" :href="route('contratos.gestao.excel_export')">
                            Exportar excel
                        </a>
                    </div>

                </div>
            </div>
        </template>

        <div class="card card-body space-y-3">

            <!-- Pesquisa-->
            <ModelSearchForm :columns="[
                'numero_contrato',
                'cnpj',
                'contratada',
                'processo_sei',
                'situacao',
                'trechos.uf.uf',
                'trechos.rodovia.rodovia'
            ]" />

            <!-- Listagem-->
            <Table :columns="['UF', 'BR', 'N° do Contrato', 'CNPJ', 'Contratada', 'Processo SEI', 'Situação', 'Ação']"
                :records="contratos" table-class="table-hover">
                <template #body="{ item }">
                    <tr>
                        <td class="w-8">
                            <p v-if="item.ufs">
                                <span v-for="uf in item.ufs.split(',')" :key="uf"
                                    class="badge bg-warning text-white m-1">{{
                                        uf
                                    }}</span>
                            </p>
                        </td>
                        <td class="w-8">
                            <p v-if="item.brs">
                                <span v-for="br in item.brs.split(',')" :key="br"
                                    class="badge bg-warning text-white m-1">{{
                                        br
                                    }}</span>
                            </p>
                        </td>
                        <td>{{ item.numero_contrato }}</td>
                        <td>{{ formatarCnpj(item.cnpj) }}</td>
                        <td>{{ item.contratada }}</td>
                        <td>{{ item.processo_sei }}</td>
                        <td>{{ item.situacao }}</td>
                        <td>
                            <!-- Contratos -->
                            <button type="button" class="btn btn-icon btn-info dropdown-toggle p-2"
                                data-bs-boundary="viewport" data-bs-toggle="dropdown" aria-expanded="false">
                                <IconDots />
                            </button>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a class="dropdown-item" :href="route('contratos.contratada.index', item.id)">
                                    Entrar
                                </a>
                                <a @click="abrirVisualizarContrato(item)" class="dropdown-item"
                                    href="javascript:void(0)">
                                    Visualizar
                                </a>
                                <a class="dropdown-item"
                                    :href="route('contratos.gestao.create', [item.tipo_id, item.id])">
                                    Editar
                                </a>
                                <a class="dropdown-item" :href="route('contratos.gestao.delete', item.id)">
                                    Excluir
                                </a>
                            </div>
                        </td>
                    </tr>
                </template>
            </Table>
        </div>

        <ModalVisualizarTrecho ref="modalVisualizarTrecho" />
        <ModalVisualizarContrato ref="modalVisualizarContrato" />

    </AuthenticatedLayout>
</template>
