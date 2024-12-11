<script setup>
import Map from "@/Components/Map.vue";
import Modal from "@/Components/Modal.vue";
import axios from "axios";
import { computed } from "vue";
import { ref } from "vue";

let licencas = ref([]);
const modaRef = ref();
const mapaRef = ref();
const amostraRef = ref({});

const abrirModal = (item) => {
  amostraRef.value = item;

  modaRef.value.getBsModal().show();

  modaRef.value
    .getElement()
    .addEventListener('shown.bs.modal', () => mapaRef.value.renderMapa());

  setTimeout(() => {
    mapaRef.value.setLinestrings([item.shape_file], false);
  }, 500);
}

defineExpose({ abrirModal });
</script>

<template>
  <Modal ref="modaRef" title="Visualizar" modal-dialog-class="modal-xl">
    <template #body>
      <div>
        <Map ref="mapaRef" :manual-render="true" :height="'350px'" />
      </div>
    </template>
    <template #footer>
    </template>
  </Modal>
</template>
