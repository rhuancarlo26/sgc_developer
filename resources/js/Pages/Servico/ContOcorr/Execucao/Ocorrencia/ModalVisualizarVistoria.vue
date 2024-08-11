<script setup>
import Modal from "@/Components/Modal.vue";
import {dateTimeFormat} from "@/Utils/DateTimeUtils";
import {usePage} from "@inertiajs/vue3";
import {ref} from "vue";

const modalVisualizarVistoria = ref();

let vistoria = ref({});

const abrirModal = (item) => {
    vistoria.value = {};

    if (item) {
        vistoria.value = item;
    }

    modalVisualizarVistoria.value.getBsModal().show();
}

defineExpose({abrirModal});
</script>

<template>
    <Modal ref="modalVisualizarVistoria" :title="'Ocorrência'" modal-dialog-class="modal-xl">
        <template #body>

        </template>
        <template #footer>
        </template>
    </Modal>
</template>
