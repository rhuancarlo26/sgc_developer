<script setup>
import Modal from "@/Components/Modal.vue";
import { ref } from "vue";

const detalhe = ref({});
const modalDetalhes = ref(null);

const abrirModal = (item) => {
  detalhe.value = item;

  modalDetalhes.value.getBsModal().show();
}

defineExpose({ abrirModal });
</script>

<template>
  <Modal ref="modalDetalhes" :title="detalhe.tipo?.nome" modal-dialog-class="modal-xl">
    <template #body>

    </template>
  </Modal>
</template>