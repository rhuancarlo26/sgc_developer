<template>

    <Head title="Gestão de Licenças" />

    <AuthenticatedLayout>

        <template #header>
            <div class="w-100 d-flex justify-content-between">
                <Breadcrumb class="align-self-center" :links="[{ route: '#', label: 'Gestão de Licenças' }]" />
                <div class="container-buttons">
                    <Link class="btn btn-info me-2" :href="route('licenca.create')">
                    Cadastrar Licenças
                    </Link>
                    <button type="button" class="btn btn-icon btn-info dropdown-toggle p-2" data-bs-boundary="viewport"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <IconSettings />
                    </button>
                    <div class="dropdown-menu dropdown-menu-end">
                        <a class="dropdown-item" :href="route('licenca.arquivo', 1)">
                            Licenças arquivadas
                        </a>
                        <a class="dropdown-item" :href="route('licenca.index')">
                            Licenças
                        </a>
                        <a class="dropdown-item" @click="abrirMapaGeral()">
                            Mapa das licenças
                        </a>
                    </div>
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
                'situacao': 'Situação',
                'trechos.uf.uf': 'UF',
                'trechos.rodovia.rodovia': 'Rodovia'
            }" />

            <!-- Listagem-->
            <Table
                :columns="['Modal', 'Tipo', 'Nº Licença', 'Empreendimento', 'Emissor', 'Data da emissão', 'Status', 'Vencimento', 'Processo DNIT', 'Açao']"
                :records="licencas" table-class="table-hover">
                <template #body="{ item }">
                    <tr>
                        <td class="w-8">
                            <IconCar v-if="item.modal == 1" />
                            <IconShip v-if="item.modal == 2" />
                            <IconTrain v-if="item.modal == 3" />
                        </td>
                        <td>
                            {{ item.tipo.sigla }} - {{ item.tipo.nome }}
                        </td>
                        <td>
                            {{ item.numero_licenca }}
                        </td>
                        <td>
                            {{ item.empreendimento }}
                        </td>
                        <td>
                            {{ item.emissor }}
                        </td>
                        <td>
                            {{ dateTimeFormat(item.data_emissao) }}
                        </td>
                        <td>
                            <a href="javascript:void(0)">
                                <span v-if="item.requerimentos.length" class="badge text-bg-primary">
                                    Em Análise
                                </span>
                                <span v-else-if="dtAlerta(item.vencimento) <= 0" class="badge text-bg-danger">
                                    Vencida
                                </span>
                                <span v-else-if="item.vencimento !== '' && item.vencimento !== null"
                                    class="badge text-bg-success">
                                    Vigente
                                </span>
                            </a>
                        </td>
                        <td>
                            <a href="javascript:void(0)">
                                <span v-if="dtAlerta(item.vencimento) <= 0" class="badge text-bg-danger">
                                    {{ dateTimeFormat(item.vencimento) }}
                                </span>
                                <span v-else-if="item.vencimento !== '' && item.vencimento !== null"
                                    class="badge text-bg-success">
                                    {{ dateTimeFormat(item.vencimento) }}
                                </span>
                            </a>
                        </td>
                        <td>
                            {{ item.processo_dnit }}
                        </td>
                        <td @click.stop>
                            <span class="dropdown">
                                <button type="button" class="btn btn-icon btn-info dropdown-toggle p-2"
                                    data-bs-boundary="viewport" data-bs-toggle="dropdown" aria-expanded="false">
                                    <IconDots />
                                </button>
                                <div class="dropdown-menu dropdown-menu-end" style="">
                                    <a class="dropdown-item" :href="route('licenca.create', item.id)">
                                        Editar
                                    </a>
                                    <a @click="abrirModalVisualizar(item)" class="dropdown-item"
                                        href="javascript:void(0)">
                                        Visualizar
                                    </a>
                                    <a v-if="item.documento?.id" class="dropdown-item" target="_blank"
                                        :href="route('licenca.documento.visualizar', item.documento.id)">
                                        Visualizar PDF
                                    </a>
                                    <a class="dropdown-item" :href="route('licenca.condicionante.index', item.id)">
                                        Condicionante
                                    </a>
                                    <a @click="abrirModalRequerimento(item)" class="dropdown-item"
                                        href="javascript:void(0)">
                                        Adicionar requerimento
                                    </a>
                                    <a v-if="!item.arquivado" @click="gerenciarArquivo(item, index, true)"
                                        class="dropdown-item" href="javascript:void(0)">
                                        Arquivar licença
                                    </a>
                                    <a v-else @click="gerenciarArquivo(item, index, false)" class="dropdown-item"
                                        href="javascript:void(0)">
                                        Desarquivar
                                    </a>
                                </div>
                            </span>
                        </td>
                    </tr>
                </template>
            </Table>
        </div>

        <ModalMapaGeral ref="refModalMapaGeral" />
        <ModalVisualizar ref="refModalVisualizar" />
        <ModalRequerimento ref="refModalRequerimento" />
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import Table from "@/Components/Table.vue";
import { ref } from "vue";
import { IconCar, IconDots, IconSettings, IconShip, IconTrain } from "@tabler/icons-vue";
import { dateTimeFormat } from "@/Utils/DateTimeUtils.js";
import ModelSearchForm from "@/Components/ModelSearchForm.vue";
import ModalMapaGeral from "@/Pages/Licenca/ModalMapaGeral.vue";
import ModalVisualizar from "@/Pages/Licenca/ModalVisualizar.vue";
import ModalRequerimento from "./Requerimento/ModalRequerimento.vue";

const props = defineProps({
    licencas: Object
})

const refModalMapaGeral = ref(null);
const refModalVisualizar = ref(null);
const refModalRequerimento = ref(null);

const abrirMapaGeral = () => {
    refModalMapaGeral.value.abrirModal();
}

const dtAlerta = (data) => {
    if (data) {
        const data_licenca = new Date(data);
        const hoje = new Date();
        const dia = 1000 * 60 * 60 * 24;

        const resultado = (data_licenca - hoje) / dia;

        return Math.round(resultado);
    }
}

const abrirModalVisualizar = (item) => {
    refModalVisualizar.value.abrirModal(item);
}

const abrirModalRequerimento = (item) => {
    refModalRequerimento.value.abrirModal(item);
}

const gerenciarArquivo = (licenca, index, status) => {
    router.patch(route('licenca.gerenciar-licenca', licenca), { id: licenca.id, arquivado: status }, {
        preserveScroll: true,
        onSuccess: () => {
            licencas.value.data.splice(index, 1)
        }
    })
}

</script>
