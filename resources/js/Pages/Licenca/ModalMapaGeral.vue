<script setup>
import Map from "@/Components/Map.vue";
import Modal from "@/Components/Modal.vue";
import axios from "axios";
import { computed } from "vue";
import { ref } from "vue";

let licencas = ref([]);
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

  licencas.value.forEach(licenca => {
    if (licenca.shapefile) {
      geojson.push([licenca.shapefile.coordenada, modalShapefileMapa(licenca), licenca]);
    }

    licenca.segmentos.forEach(segmento => {
      geojson.push([segmento.coordenada, modalSegmentoMapa(segmento), segmento]);
    });
  });

  return geojson;
})

const modalShapefileMapa = (licenca) => {
  return `
    <h3>Teste 25000</h3>
  `;
}

const modalSegmentoMapa = (segmento) => {
  return `
    <h3>Dados do Segmento</h3>
    <span><strong>Uf inicial: </strong> ${segmento.uf_inicial?.uf}</span><br>
    <span><strong>Uf final: </strong> ${segmento.uf_final?.uf}</span><br>
    <span><strong>BR: </strong> ${segmento.rodovia}</span><br>
    <span><strong>Km Inicial: </strong> ${segmento.km_inicial}</span><br>
    <span><strong>Km Final: </strong> ${segmento.km_final}</span><br>
    <span><strong>Tipo: </strong> ${segmento.trecho_tipo}</span>
  `;
}

const getCoordenada = () => {
  axios.post(route('licenca.get_all')).then(r => {
    licencas.value = r.data;
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
