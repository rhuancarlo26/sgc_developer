<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import Table from '@/Components/Table.vue';
import Breadcrumb from "@/Components/Breadcrumb.vue";
import ModelSearchForm from "@/Components/ModelSearchForm.vue";
import Modal from "@/Components/Modal.vue";
import { ref } from "vue";
import Map from "@/Components/Map.vue";
import { onMounted } from "vue";
import axios from "axios";
import { computed } from "vue";
import { IconEye } from "@tabler/icons-vue";
import { dateTimeFormat } from "@/Utils/DateTimeUtils";

const props = defineProps({
    contratos: Object,
    tipo: Object
})

const modalMapa = ref();
const modalVisualizarContrato = ref();
const mapaTrecho = ref();
const mapaVisualizarTrecho = ref();
let coordenadas = ref();
let contratoClicado = ref({});

onMounted(() => {
    getGeoJson();
});

const abrirMapa = () => {
    modalMapa.value.getBsModal().show();

    modalMapa.value
        .getElement()
        .addEventListener('shown.bs.modal', () => mapaTrecho.value.renderMapa());

    setTimeout(() => {
        mapaTrecho.value.setLinestrings(trechos.value, true);
    }, 500);
}

const abrirVisualizarContrato = (item) => {
    contratoClicado.value = { ...item };

    modalVisualizarContrato.value.getBsModal().show();

    modalVisualizarContrato.value
        .getElement()
        .addEventListener('shown.bs.modal', () => mapaVisualizarTrecho.value.renderMapa());

    setTimeout(() => {
        mapaVisualizarTrecho.value.setLinestrings(trechosVisualizacao.value, true);
    }, 500);
}

const trechos = computed(() => {
    let geojson = [];

    coordenadas.forEach(contrato => {
        contrato.trechos.forEach(trecho => {
            geojson.push([trecho.coordenada, modalTechoMap(contrato, trecho), trecho]);
        });
    });

    return geojson;
})

const trechosVisualizacao = computed(() => {
    let geojson = [];

    contratoClicado.value.trechos.forEach(trecho => {
        geojson.push([trecho.coordenada, modalTechoMap(contratoClicado.value, trecho), trecho]);
    });

    return geojson;
})

const getGeoJson = () => {
    axios.post(route('contratos.gestao.get_geo_json')).then(r => {
        coordenadas = r.data;
    });
};

const modalTechoMap = (contrato, trecho) => {
    return `
  <h3>Dados do Trecho</h3>
  <span><strong>Empresa: </strong> ${contrato.contratada}</span><br>
  <span><strong>Contrato: </strong> ${contrato.numero_contrato}</span><br>
  <span><strong>UF: </strong> ${trecho.uf.uf}</span><br>
  <span><strong>BR: </strong> ${trecho.rodovia.rodovia}</span><br>
  <span><strong>Km Inicial: </strong> ${trecho.km_inicial}</span><br>
  <span><strong>Km Final: </strong> ${trecho.km_final}</span><br>
  <span><strong>Tipo: </strong> ${trecho.trecho_tipo}</span>
  `;
}

</script>

<template>
    <Head title="Gestão de Contratos" />

    <AuthenticatedLayout>

        <template #header>
            <div class="w-100 d-flex justify-content-between">
                <Breadcrumb class="align-self-center" :links="[
                    { route: '#', label: 'Gestão de Contratos' }
                ]" />
                <div>
                    <Link class="btn btn-success me-2" :href="route('contratos.gestao.create', tipo.id)">
                    Importar Contrato
                    </Link>
                    <button @click="abrirMapa()" type="button" class="btn btn-success">Mapa dos Contratos</button>

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
            <Table :columns="['UF', 'BR', 'N° do Contrato', 'CNPJ', 'Contratada', 'Processo SEI', 'Situação', 'Ação']"
                :records="contratos" table-class="table-hover" :excelRoute="route('contratos.gestao.excel_export')">
                <template #body="{ item }">
                    <tr class="cursor-pointer"
                        @click="router.get(route('contratos.gestao.create', [item.tipo_id, item.id]))">
                        <td class="w-8"><span v-for="uf in item.ufs" :key="uf" class="badge bg-warning text-white m-1">{{ uf
                        }}</span>
                        </td>
                        <td class="w-8"><span v-for="br in item.brs" :key="br" class="badge bg-warning text-white m-1">{{ br
                        }}</span>
                        </td>
                        <td>{{ item.numero_contrato }}</td>
                        <td>{{ item.cnpj }}</td>
                        <td>{{ item.contratada }}</td>
                        <td>{{ item.processo_sei }}</td>
                        <td>{{ item.situacao }}</td>
                        <td @click.stop>
                            <button @click="abrirVisualizarContrato(item)" class="btn btn-icon btn-primary">
                                <IconEye />
                            </button>
                        </td>
                    </tr>
                </template>
            </Table>
        </div>

        <Modal ref="modalMapa" title="Mapa agluma coisa" modal-dialog-class="modal-xl">
            <template #body>
                <Map ref="mapaTrecho" height="300px" :manual-render="true" />
            </template>
        </Modal>

        <Modal ref="modalVisualizarContrato" :title="'N° do Contrato: ' + contratoClicado.numero_contrato"
            modal-dialog-class="modal-xl">
            <template #body>
                <div class="card">
                    <div class="card-header">
                        <h3 class="my-0">Dados Básicos do Contrato</h3>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <span class="col"><strong>Empresa: </strong>{{ contratoClicado.contratada }}</span>
                            <span class="col"><strong>CNPJ: </strong>{{ contratoClicado.cnpj }}</span>
                            <span class="col"><strong>Número do Contrato: </strong>{{ contratoClicado.numero_contrato
                            }}</span>
                        </div>
                        <div class="row mb-4">
                            <span class="col"><strong>Objeto do Contrato: </strong>{{ contratoClicado.objeto }}</span>
                        </div>
                        <div class="row mb-4">
                            <span class="col"><strong>Número do Processo (DNIT): </strong>{{ contratoClicado.processo_sei
                            }}</span>
                            <span class="col"><strong>Início da Vigência: </strong>{{
                                dateTimeFormat(contratoClicado.data_inicio_vigencia ?? null, {
                                    dateStyle: 'short',
                                    timeStyle: 'short'
                                })
                            }}</span>
                            <span class="col"><strong>Término da Vigência: </strong>{{
                                dateTimeFormat(contratoClicado.data_termino_vigencia ?? null, {
                                    dateStyle: 'short',
                                    timeStyle: 'short'
                                })
                            }}</span>
                            <span class="col"><strong>Situação: </strong>{{ contratoClicado.situacao?.nome }}</span>
                        </div>
                    </div>

                    <div class="card-header">
                        <h3 class="my-0">Licitação</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <span class="col"><strong>Edital: </strong>{{ contratoClicado.edital }}</span>
                            <span class="col"><strong>Tipo de Licitação: </strong>{{ contratoClicado.tipo_licitacao
                            }}</span>
                            <span class="col"><strong>Modalidade: </strong>{{ contratoClicado.modalidade }}</span>
                        </div>
                    </div>

                    <div class="card-header">
                        <h3 class="my-0">Fiscalização</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <span class="col"><strong>Unidade Gestora: </strong>{{ contratoClicado.unidade_gestora }}</span>
                            <span class="col"><strong>Fiscal do Contrato: </strong>{{ contratoClicado.fiscal_contrato
                            }}</span>
                        </div>
                    </div>

                    <div class="card-header">
                        <h3 class="my-0">Valores</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <span class="col"><strong>SNV: </strong>{{ contratoClicado.snv }}</span>
                            <span class="col"><strong>Preço Inicial: </strong>{{ contratoClicado.preco_inicial }}</span>
                            <span class="col"><strong>Preço Aditivos: </strong>{{ contratoClicado.total_aditivo }}</span>
                            <span class="col"><strong>Preço Reajuste: </strong>{{ contratoClicado.total_reajuste }}</span>
                            <span class="col"><strong>Total: </strong>{{ contratoClicado.total }}</span>
                        </div>
                    </div>

                    <div class="card-header">
                        <h3 class="my-0">Trechos do Contrato</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive mb-4">
                            <table class="table card-table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>UF</th>
                                        <th>BR</th>
                                        <th>Km Inicial</th>
                                        <th>Km Final</th>
                                        <th>Tipo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="trecho in contratoClicado.trechos" :key="trecho.id" class="cursor-pointer">
                                        <td>{{ trecho.uf?.uf }}</td>
                                        <td>{{ trecho.rodovia?.rodovia }}</td>
                                        <td>{{ trecho.km_inicial }}</td>
                                        <td>{{ trecho.km_final }}</td>
                                        <td>{{ trecho.trecho_tipo }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="card-header">
                        <h3 class="my-0">Mapa</h3>
                    </div>
                    <div class="card-body">
                        <Map ref="mapaVisualizarTrecho" height="300px" :manual-render="true" />
                    </div>
                </div>
            </template>
        </Modal>

    </AuthenticatedLayout>
</template>
