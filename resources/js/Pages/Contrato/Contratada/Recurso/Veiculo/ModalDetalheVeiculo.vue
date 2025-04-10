<script setup>
import Modal from "@/Components/Modal.vue";
import { router, useForm } from "@inertiajs/vue3";
import { IconSearch } from "@tabler/icons-vue";
import axios from "axios";
import { ref } from "vue";
import { useToast } from "vue-toastification";

const veiculo = ref({
  codigo: null,
  observacao: null
});

const modalDetalhes = ref(null);

const abrirModal = (item) => {
  veiculo.value = item;

  modalDetalhes.value.getBsModal().show();
}

defineExpose({ abrirModal });
</script>

<template>
  <Modal ref="modalDetalhes" :title="veiculo.codigo?.codigo + ' - ' + veiculo.codigo?.descricao"
    modal-dialog-class="modal-xl">
    <template #body>
      <div class="mb-4">
        <div class="row">
          <div class="col">
            <span><strong>Código: </strong>{{ veiculo.codigo?.codigo }}</span>
          </div>
        </div>
        <div class="row">
          <div class="col">
            <span><strong>Descrição: </strong>{{ veiculo.codigo?.descricao }}</span>
          </div>
        </div>
        <div class="row">
          <div class="col">
            <span><strong>Observação: </strong>{{ veiculo.observacao }}</span>
          </div>
        </div>
      </div>
      <div class="row">
        <strong class="px-3">Foto(s) do veiculos</strong>
        <div class="d-flex flex-wrap">
          <div v-for="documento in veiculo.documentos" :key="documento.id" class="col-xl-6 py-2">
            <img :src="route('home') + documento.caminho" :title="documento.nome" :alt="documento.nome" class="w-100" />
          </div>
        </div>
      </div>
    </template>
    <template #footer>

    </template>
  </Modal>
</template>
