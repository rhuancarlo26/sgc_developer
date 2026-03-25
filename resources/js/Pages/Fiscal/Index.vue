<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import ModelSearchForm from "@/Components/ModelSearchFormAllColumns.vue";
import Table from "@/Components/Table.vue";
import { IconDots } from "@tabler/icons-vue";
import NavLink from "@/Components/NavLink.vue";
import { ref } from "vue";
import ModalVisualizarContratoFiscal from "./ModalVisualizarContratoFiscal.vue";

const props = defineProps({
    contratos: Object,
    tipo: Object
})

const formatarCnpj = (cnpj) => {
    return cnpj.replace(/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})$/, '$1.$2.$3/$4-$5');
}

const modalVisualizarContratoFiscal = ref();

const abrirVisualizarContratoFiscal = (contrato) => {
    modalVisualizarContratoFiscal.value.abrirModal(contrato);
}

</script>

<template>

    <Head title="Fiscal" />

    <AuthenticatedLayout>
        <template #header>
            <div class="w-100 d-flex justify-content-between">
                <Breadcrumb class="align-self-center" :links="[{ route: '#', label: 'Fiscal' }]" />
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

        </div>

        <!-- Listagem-->
        <Table :columns="['uf', 'br', 'nº contrato', 'cnpj', 'contratada', 'processo sei', 'situação', 'ação']"
            :records="contratos" table-class="table-hover">
            <template #body="{ item }">
                <tr>
                    <td class="w-8">
                        <p v-if="item.ufs">
                            <span v-for="uf in item.ufs.split(',')" :key="uf" class="badge bg-warning text-white m-1">
                                {{ uf }}
                            </span>
                        </p>
                    </td>
                    <td class="w-8">
                        <p v-if="item.brs">
                            <span v-for="br in item.brs.split(',')" :key="br" class="badge bg-warning text-white m-1">
                                {{ br }}
                            </span>
                        </p>
                    </td>
                    <td>{{ item.numero_contrato }}</td>
                    <td>{{ formatarCnpj(item.cnpj) }}</td>
                    <td>{{ item.contratada }}</td>
                    <td>{{ item.processo_sei }}</td>
                    <td>{{ item.situacao }}</td>
                    <td>
                        <button type="button" class="btn btn-icon dropdown-toggle p-2" data-bs-boundary="viewport"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <IconDots />
                        </button>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a @click="abrirVisualizarContratoFiscal(item)" class="dropdown-item"
                                href="javascript:void(0)">
                                Visualizar
                            </a>
                            <NavLink route-name="fiscal.dados.servicos.index" :param="{ contrato: item.id }" title="Entrar"
                                class="dropdown-item" />
                        </div>
                    </td>
                </tr>
            </template>
        </Table>

        <ModalVisualizarContratoFiscal ref="modalVisualizarContratoFiscal" />

    </AuthenticatedLayout>

</template>
