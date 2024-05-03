<script setup>
import Map from "@/Components/Map.vue";
import Modal from "@/Components/Modal.vue";
import { dateTimeFormat } from "@/Utils/DateTimeUtils";
import { computed } from "vue";
import { ref } from "vue";

const contrato = ref({});
const modalDetalhes = ref(null);
const mapaVisualizarTrecho = ref(null);

const abrirModal = (item) => {
  contrato.value = item;

  modalDetalhes.value.getBsModal().show();

  modalDetalhes.value
    .getElement()
    .addEventListener('shown.bs.modal', () => mapaVisualizarTrecho.value.renderMapa());

  setTimeout(() => {
    mapaVisualizarTrecho.value.setLinestrings(trechosVisualizacao.value, true);
  }, 500);
}

const trechosVisualizacao = computed(() => {
  let geojson = [];

  contrato.value.trechos.forEach(trecho => {
    geojson.push([trecho.coordenada, modalTechoMap(contrato.value, trecho), trecho]);
  });

  return geojson;
})

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

defineExpose({ abrirModal });
</script>

<template>
  <Modal ref="modalDetalhes" :title="'N° do Contrato: ' + contrato.numero_contrato" modal-dialog-class="modal-xl">
    <template #body>
      <div class="card">
        <div class="card-header">
          <h3 class="my-0">Dados Básicos do Contrato</h3>
        </div>
        <div class="card-body">
          <div class="row mb-4">
            <span class="col"><strong>Empresa: </strong>{{ contrato.contratada }}</span>
            <span class="col"><strong>CNPJ: </strong>{{ contrato.cnpj }}</span>
            <span class="col"><strong>Número do Contrato: </strong>{{ contrato.numero_contrato
              }}</span>
          </div>
          <div class="row mb-4">
            <span class="col"><strong>Objeto do Contrato: </strong>{{ contrato.objeto }}</span>
          </div>
          <div class="row mb-4">
            <span class="col"><strong>Número do Processo (DNIT): </strong>{{ contrato.processo_sei
              }}</span>
            <span class="col"><strong>Início da Vigência: </strong>{{
    dateTimeFormat(contrato.data_inicio_vigencia ?? null, {
      dateStyle: 'short',
      timeStyle: 'short'
    })
  }}</span>
            <span class="col"><strong>Término da Vigência: </strong>{{
      dateTimeFormat(contrato.data_termino_vigencia ?? null, {
        dateStyle: 'short',
        timeStyle: 'short'
      })
    }}</span>
            <span class="col"><strong>Situação: </strong>{{ contrato.situacao }}</span>
          </div>
        </div>

        <div class="card-header">
          <h3 class="my-0">Licitação</h3>
        </div>
        <div class="card-body">
          <div class="row">
            <span class="col"><strong>Edital: </strong>{{ contrato.edital }}</span>
            <span class="col"><strong>Tipo de Licitação: </strong>{{ contrato.tipo_licitacao
              }}</span>
            <span class="col"><strong>Modalidade: </strong>{{ contrato.modalidade }}</span>
          </div>
        </div>

        <div class="card-header">
          <h3 class="my-0">Fiscalização</h3>
        </div>
        <div class="card-body">
          <div class="row">
            <span class="col"><strong>Unidade Gestora: </strong>{{ contrato.unidade_gestora }}</span>
            <span class="col"><strong>Fiscal do Contrato: </strong>{{ contrato.fiscal_contrato
              }}</span>
          </div>
        </div>

        <div class="card-header">
          <h3 class="my-0">Valores</h3>
        </div>
        <div class="card-body">
          <div class="row">
            <span class="col"><strong>SNV: </strong>{{ contrato.snv }}</span>
            <span class="col"><strong>Preço Inicial: </strong>{{ contrato.preco_inicial }}</span>
            <span class="col"><strong>Preço Aditivos: </strong>{{ contrato.total_aditivo }}</span>
            <span class="col"><strong>Preço Reajuste: </strong>{{ contrato.total_reajuste }}</span>
            <span class="col"><strong>Total: </strong>{{ contrato.total }}</span>
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
                <tr v-for="trecho in contrato.trechos" :key="trecho.id" class="cursor-pointer">
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
</template>