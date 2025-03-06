<script setup>
import Map from "@/Components/Map.vue";
import Modal from "@/Components/Modal.vue";
import axios from "axios";
import { computed, ref } from "vue";

const modalMapa = ref(null);
const mapaTrecho = ref(null);
let coordenadas = ref(null);

const abrirModal = async () => {
  if (!coordenadas.length) {
    await getGeoJson();
  }

  modalMapa.value.getBsModal().show();

  modalMapa.value
    .getElement()
    .addEventListener('shown.bs.modal', () => mapaTrecho.value.renderMapa());

  setTimeout(() => {
    mapaTrecho.value.setLinestrings(trechos.value, true);
  }, 500);
}

const getGeoJson = async () => {
  await axios.post(route('contratos.gestao.get_geo_json')).then(r => {
    coordenadas = r.data;
  });
};

const trechos = computed(() => {
  let geojson = [];

  coordenadas.forEach(contrato => {
    contrato.trechos.forEach(trecho => {
      geojson.push([trecho.coordenada, modalTechoMap(contrato, trecho), trecho]);
    });
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
  <Modal ref="modalMapa" title="Mapa dos trechos de todos os contratos" modal-dialog-class="modal-xl">
    <template #body>
      <Map ref="mapaTrecho" height="500px" :manual-render="true" />
    </template>
  </Modal>
</template>