<template>
    <Head title="Gestão de Licenças" />

    <AuthenticatedLayout>

        <template #header>
            <div class="w-100 d-flex justify-content-between">
                <Breadcrumb class="align-self-center" :links="[{ route: '#', label: 'Gestão de Licenças' }]" />
                <div class="container-buttons">
                    <Link class="btn btn-success me-2" :href="route('licenca.create')">
                    Cadastrar Licenças
                    </Link>
                    <Link class="btn btn-success me-2" :href="route('contratos.gestao.create')">
                    Licenças Arquivos
                    </Link>
                    <button type="button" class="btn btn-success">
                        Mapa das Licenças
                    </button>
                </div>
            </div>
        </template>

        <div class="card card-body space-y-3">

            <!-- Pesquisa-->
            <ModelSearchForm :search-columns="{
                'numero_contrato': 'N° do Contrato',
                'cnpj': 'CNPJ',
                'contratada': 'Contratada',
                'processo_sei': 'Processo SEI',
                'situacao': 'Situação'
            }" />

            <!-- Listagem-->
            <Table
                :columns="['Modal', 'Tipo', 'Nº Licença', 'Empreendimento', 'Emissor', 'Data da Emissão', 'Status', 'Vencimento', 'Processo DNIT', 'Ação']"
                :records="licencas" table-class="table-hover" :excelRoute="route('contratos.gestao.excel_export')">
                <template #body="{ item }">
                    <tr class="cursor-pointer" @click="router.get(route('contratos.gestao.create', item.id))">
                        <!-- Modal -->
                        <td>
                            <IconCar v-if="item.modal == 1" />
                            <IconShip v-if="item.modal == 2" />
                            <IconTrain v-if="item.modal == 3" />
                        </td>
                        <!-- Tipo -->
                        <td>
                            {{ item.tipo.sigla }} - {{ item.tipo.nome }}
                        </td>
                        <!-- Nº Licença -->
                        <td>
                            {{ item.numero_licenca }}
                        </td>
                        <!-- Empreendimento -->
                        <td>
                            {{ item.empreendimento }}
                        </td>
                        <!-- Emissor -->
                        <td>
                            {{ item.emissor }}
                        </td>
                        <!-- Data da Emissão -->
                        <td>
                            {{ dateTimeFormat(item.data_emissao) }}
                        </td>
                        <!-- Status -->
                        <td>
                            <a href="javascript:void(0)">
                                <span class="badge text-bg-success">
                                    Vigente
                                </span>
                                <span class="badge text-bg-warning">
                                    Em Análise
                                </span>
                                <span class="badge text-bg-danger">
                                    Vencida
                                </span>
                                <span class="badge text-bg-info">
                                    Não Vigente
                                </span>
                            </a>
                        </td>
                        <!-- Vencimento -->
                        <td>
                            {{ dateTimeFormat(item.vencimento) }}
                        </td>
                        <!-- Processo DNIT -->
                        <td>
                            {{ item.processo_dnit }}
                        </td>
                        <!-- Ação -->
                        <td @click.stop>
                            <span class="dropdown">
                                <button class="btn dropdown-toggle align-text-top" data-bs-boundary="viewport"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    <IconDots />
                                </button>
                                <div class="dropdown-menu dropdown-menu-end" style="">
                                    <a class="dropdown-item" href="#">
                                        Visualizar
                                    </a>
                                    <a class="dropdown-item" :href="route('licenca.condicionante.index', item.id)">
                                        Condicionante
                                    </a>
                                    <a @click="abrirModalRequerimento(item)" class="dropdown-item"
                                        href="javascript:void(0)">
                                        Adicionar requerimento
                                    </a>
                                </div>
                            </span>

                        </td>
                    </tr>
                </template>
            </Table>
        </div>

        <Modal ref="refModalRequerimento" />
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import Table from '@/Components/Table.vue';
import Breadcrumb from "@/Components/Breadcrumb.vue";
import ModelSearchForm from "@/Components/ModelSearchForm.vue";
import { IconDots, IconShip, IconCar, IconTrain } from "@tabler/icons-vue";
import { dateTimeFormat } from "@/Utils/DateTimeUtils";
import Modal from "./Requerimento/ModalRequerimento.vue";
import { ref } from "vue";

const props = defineProps({
    licencas: Object
})

const refModalRequerimento = ref(null);

const abrirModalRequerimento = (item) => {
    refModalRequerimento.value.abrirModal(item);
}

</script>

<style scoped></style>