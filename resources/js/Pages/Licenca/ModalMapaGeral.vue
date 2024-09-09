<script setup>
import Map from "@/Components/Map.vue";
import Modal from "@/Components/Modal.vue";
import axios from "axios";
import {computed} from "vue";
import {ref} from "vue";

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
        mapaVisualizar.value.setLinestrings(trechos.value, false);
    }, 500);
}

const trechos = computed(() => {
    let geojson = [];

    licencas.value.forEach(licenca => {
        if (licenca.geo_json) {
            geojson.push([licenca.geo_json]);
        }
    });

    return geojson;
})

const getCoordenada = () => {
    axios.post(route('licenca.get_all')).then(r => {
        licencas.value = r.data;
    });
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
