<script setup>
import Map from "@/Components/Map.vue";
import Modal from "@/Components/Modal.vue";
import axios from "axios";
import { computed } from "vue";
import { ref } from "vue";

let coordenadas = ref([]);
const modalVisualizar = ref();
const mapaVisualizar = ref();

const abrirModal = () => {
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
  axios.post(route('licenca.trecho.get_coordenada_trecho')).then(r => {
    coordenadas.value = r.data;
  });
}

defineExpose({ abrirModal });
</script>

<template>
  <Modal ref="modalVisualizar" title="Visualizar" modal-dialog-class="modal-xl">
    <template #body>
      <div>
        <Map ref="mapaVisualizar" :manual-render="true" :height="'350px'" />
      </div>
    </template>
    <template #footer>
    </template>
  </Modal>
</template>
