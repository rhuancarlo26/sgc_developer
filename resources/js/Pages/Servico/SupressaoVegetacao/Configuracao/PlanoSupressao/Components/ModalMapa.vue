<script setup>
import Map from "@/Components/Map.vue";
import Modal from "@/Components/Modal.vue";
import {ref} from "vue";

let licencas = ref([]);
const modalVisualizar = ref();
const mapaVisualizar = ref();

const abrirModal = (geoJson) => {
    console.log(JSON.parse(geoJson))
    modalVisualizar.value.getBsModal().show();

    modalVisualizar.value
        .getElement()
        .addEventListener('shown.bs.modal', () => mapaVisualizar.value.renderMapa());

    setTimeout(() => {
        mapaVisualizar.value.setLinestrings([geoJson]);
    }, 500);
}
defineExpose({abrirModal});
</script>

<template>
    <Modal ref="modalVisualizar" title="Visualizar" modal-dialog-class="modal-xl">
        <template #body>
            <div>
                <Map ref="mapaVisualizar" :manual-render="true" :height="'350px'"/>
            </div>
        </template>
        <template #footer>
        </template>
    </Modal>
</template>
