<script setup>
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import Modal from "@/Components/Modal.vue";
import Map from "@/Components/Map.vue";
import NavButton from "@/Components/NavButton.vue";
import Table from "@/Components/Table.vue";
import { useForm } from "@inertiajs/vue3";
import { IconDeviceFloppy } from "@tabler/icons-vue";
import { computed } from "vue";
import { ref } from "vue";

const modalShapefile = ref({});
const mapaVisualizar = ref({});
const empreendimento = ref({});

const abrirModal = (item) => {
  if (item) {
    empreendimento.value = item
  }

  modalShapefile.value.getBsModal().show();

  modalShapefile.value
    .getElement()
    .addEventListener('shown.bs.modal', () => mapaVisualizar.value.renderMapa());

  setTimeout(() => {
    mapaVisualizar.value.setLinestrings(trechos.value, true);
  }, 500);
};

const trechos = computed(() => {
  let geojson = [];
  if (empreendimento.value.licenca?.shapefile) {
    geojson.push([empreendimento.value.licenca?.shapefile?.coordenada, modalShapefileMapa(empreendimento.value.licenca), empreendimento.value.licenca]);
  }

  return geojson;
});

const modalShapefileMapa = (licenca) => {
  return `
    <h3>Teste 25000</h3>
  `;
};

defineExpose({ abrirModal });
</script>

<template>
  <Modal ref="modalShapefile" title="Nova campanha" modal-dialog-class="modal-xl">
    <template #body>
      <div class="card-body">
        <Map ref="mapaVisualizar" :manual-render="true" :height="'350px'" />
      </div>
    </template>
    <template #footer>
    </template>
  </Modal>
</template>
