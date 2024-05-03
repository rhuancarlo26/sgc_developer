<script setup>
import Map from "@/Components/Map.vue";
import Modal from "@/Components/Modal.vue";
import { dateTimeFormat } from "@/Utils/DateTimeUtils";
import { router } from "@inertiajs/vue3";
import axios from "axios";
import { nextTick } from "vue";
import { computed } from "vue";
import { ref } from "vue";

let licenca = ref({});
let coordenadas = ref([]);
const modalVisualizar = ref();
const mapaVisualizar = ref();

const abrirModal = (item) => {
  licenca.value = item;

  getCoordenada();

  modalVisualizar.value.getBsModal().show();

  modalVisualizar.value
    .getElement()
    .addEventListener('shown.bs.modal', () => mapaVisualizar.value.renderMapa());

  setTimeout(() => {
    mapaVisualizar.value.setLinestrings(trechos.value, true);
  }, 500);
}

const trechos = computed(() => {
  let geojson = [];

  coordenadas.value.forEach(trecho => {
    geojson.push([trecho.coordenada, modalTrechoMapa(trecho), trecho]);
  });

  return geojson;
})

const modalTrechoMapa = (trecho) => {
  return `
    <h3>Dados do Trecho</h3>
    <span><strong>UF: </strong> ${trecho.uf.uf}</span><br>
    <span><strong>BR: </strong> ${trecho.rodovia.rodovia}</span><br>
    <span><strong>Km Inicial: </strong> ${trecho.km_inicial}</span><br>
    <span><strong>Km Final: </strong> ${trecho.km_final}</span><br>
    <span><strong>Tipo: </strong> ${trecho.trecho_tipo}</span>
  `;
}

const getCoordenada = () => {
  axios.post(route('licenca.trecho.get_coordenada_trecho'), { licenca_id: licenca.value.id }).then(r => {
    coordenadas.value = r.data;
  });
}

defineExpose({ abrirModal });
</script>

<template>
  <Modal ref="modalVisualizar" title="Visualizar" modal-dialog-class="modal-xl">
    <template #body>
      <div class="row mb-4">
        <div class="col">
          <span><strong>Licença: </strong>{{ `${licenca?.tipo?.sigla} - ${licenca?.tipo?.nome}` }}</span>
        </div>
      </div>
      <div class="row mb-4">
        <div class="col">
          <span><strong>N° Licença: </strong>{{ licenca.numero_licenca }}</span>
        </div>
        <div class="col">
          <span><strong>Status: </strong>{{ licenca.status }}</span>
        </div>
      </div>
      <div class="row mb-4">
        <div class="col">
          <span><strong>Emissor: </strong>{{ licenca.emissor }}</span>
        </div>
        <div class="col">
          <span><strong>Empreendimento: </strong>{{ licenca.empreendimento }}</span>
        </div>
      </div>
      <div class="row mb-4">
        <div class="col">
          <span><strong>Data Emissão: </strong>{{ dateTimeFormat(licenca.data_emissao ?? null, {
            dateStyle: 'short'
          }) }}</span>
        </div>
        <div class="col">
          <span><strong>Data vencimento: </strong>{{ dateTimeFormat(licenca.vencimento ?? null, {
            dateStyle: 'short'
          }) }}</span>
        </div>
      </div>
      <div class="row mb-4">
        <div class="col">
          <span><strong>Processo DNIT: </strong>{{ licenca.processo_dnit }}</span>
        </div>
      </div>
      <div>
        <Map ref="mapaVisualizar" :manual-render="true" :height="'350px'" />
      </div>
    </template>
    <template #footer>
    </template>
  </Modal>
</template>
