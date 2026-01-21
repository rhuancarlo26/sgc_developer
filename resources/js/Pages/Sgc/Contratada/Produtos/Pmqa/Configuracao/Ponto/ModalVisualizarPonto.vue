<script setup>
import Modal from "@/Components/Modal.vue";
import { ref } from "vue";

const modalVisualizarPonto = ref();
const ponto = ref({});

const abrirModal = (item) => {
  ponto.value = item ?? {};
  modalVisualizarPonto.value.getBsModal().show();
};

defineExpose({ abrirModal });
</script>

<template>
  <Modal
    ref="modalVisualizarPonto"
    :title="`Nome: ${ponto?.nome_ponto_coleta ?? '-'}`"
    modal-dialog-class="modal-xl"
  >
    <template #body>
      <div class="row g-3">
        <!-- COLUNA 1 -->
        <div class="col-12 col-md-4">
          <div class="mb-3">
            <strong>Nome: </strong>{{ ponto.nome_ponto_coleta ?? "-" }}
          </div>
          <div class="mb-3">
            <strong>Classificação: </strong>{{ ponto.classificacao ?? "-" }}
          </div>
          <div class="mb-3">
            <strong>UF: </strong>{{ ponto.UF ?? "-" }}
          </div>
          <div class="mb-3">
            <strong>Km rodovia: </strong>{{ ponto.km_rodovia ?? "-" }}
          </div>
          <div class="mb-0">
            <strong>Observações: </strong>{{ ponto.observacoes ?? "-" }}
          </div>
        </div>

        <!-- COLUNA 2 -->
        <div class="col-12 col-md-4">
          <div class="mb-3">
            <strong>Latitude: </strong>{{ ponto.lat_x ?? "-" }}
          </div>
          <div class="mb-3">
            <strong>Classe: </strong>{{ ponto.classe ?? "-" }}
          </div>
          <div class="mb-3">
            <strong>Município: </strong>{{ ponto.municipio ?? "-" }}
          </div>
          <div class="mb-0">
            <strong>Estaca: </strong>{{ ponto.estaca ?? "-" }}
          </div>
        </div>

        <!-- COLUNA 3 -->
        <div class="col-12 col-md-4">
          <div class="mb-3">
            <strong>Longitude: </strong>{{ ponto.long_y ?? "-" }}
          </div>
          <div class="mb-3">
            <strong>Tipo de ambiente: </strong>{{ ponto.tipo_ambiente ?? "-" }}
          </div>
          <div class="mb-0">
            <strong>Bacia hidrográfica: </strong>{{ ponto.bacia_hidrografica ?? "-" }}
          </div>
        </div>
      </div>
    </template>

    <template #footer />
  </Modal>
</template>
