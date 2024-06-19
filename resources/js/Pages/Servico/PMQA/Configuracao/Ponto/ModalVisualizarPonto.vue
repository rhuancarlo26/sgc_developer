<script setup>
import Modal from "@/Components/Modal.vue";
import { ref } from "vue";

const modalVisualizarPonto = ref();

let ponto = ref({});

const abrirModal = (item) => {
  ponto.value = item;

  modalVisualizarPonto.value.getBsModal().show();
}

defineExpose({ abrirModal });
</script>

<template>
  <Modal ref="modalVisualizarPonto" :title="'Nome: ' + ponto.nomepontocoleta" modal-dialog-class="modal-xl">
    <template #body>
      <div class="row mb-4">
        <span class="col"><strong>Nome: </strong>{{ ponto.nomepontocoleta }}</span>
        <span class="col"><strong>Latitude: </strong>{{ ponto.lat_x }}</span>
        <span class="col"><strong>Longitude: </strong>{{ ponto.long_y }}</span>
      </div>
      <div class="row mb-4">
        <span class="col"><strong>Classificação: </strong>{{ ponto.classificacao }}</span>
        <span class="col"><strong>Classe: </strong>{{ ponto.classe }}</span>
        <span class="col"><strong>Tipo de ambiente: </strong>{{ ponto.tipoambiente }}</span>
      </div>
      <div class="row mb-4">
        <span class="col"><strong>UF: </strong>{{ ponto.uf }}</span>
        <span class="col"><strong>Municipio: </strong>{{ ponto.municipio }}</span>
        <span class="col"><strong>Bacia hidrografica: </strong>{{ ponto.baciahidrografica }}</span>
      </div>
      <div class="row mb-4">
        <span class="col"><strong>Km rodovia: </strong>{{ ponto.km_rodovia }}</span>
        <span class="col"><strong>Estaca: </strong>{{ ponto.estaca }}</span>
      </div>
      <div class="row mb-4">
        <span class="col"><strong>Observações: </strong>{{ ponto.km_rodovia }}</span>
      </div>
    </template>
    <template #footer>
    </template>
  </Modal>
</template>
