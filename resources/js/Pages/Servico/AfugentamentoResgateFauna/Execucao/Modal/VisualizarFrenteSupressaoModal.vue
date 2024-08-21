<script setup>
import Modal from "@/Components/Modal.vue";
import { dateTimeFormat } from "@/Utils/DateTimeUtils";
import { ref } from "vue";

const frente = ref({
    id: null,
    rodovia: null,
    uf_inicial: null,
    km_inicial: null,
    latitude_inicial: null,
    longitude_inicial: null,
    uf_final: null,
    km_final: null,
    latitude_final: null,
    longitude_final: null,
    data_inicial: null,
    data_final: null,
    processing: false,
})

const modalDetalhes = ref(null);

const abrirModal = (item) => {
    frente.value = item;

    modalDetalhes.value.getBsModal().show();
}

defineExpose({ abrirModal });
</script>

<template>
    <Modal ref="modalDetalhes" :title="'Frente de Supressão de Fauna'" modal-dialog-class="modal-xl">
        <template #body>
            <div class="row mb-4">
                <div class="col">
                    <span><strong>Rodovia: </strong>{{ frente.rodovia?.rodovia }}</span>
                </div>
                <div class="col">
                    <span><strong>UF Inicial: </strong>{{ frente.uf_inicial?.uf }}</span>
                </div>
            </div>
            <div class="row mb-4">
                <div class="col">
                    <span><strong>Km Inicial: </strong>{{ frente.km_inicial }}</span>
                </div>
                <div class="col">
                    <span><strong>Latitude Inicial: </strong>{{ frente.latitude_inicial }}</span>
                </div>
            </div>
            <div class="row mb-4">
                <div class="col">
                    <span><strong>Longitude Inicial: </strong>{{ frente.longitude_inicial }}</span>
                </div>
                <div class="col">
                    <span><strong>UF Final: </strong>{{ frente.uf_final?.uf }}</span>
                </div>
            </div>
            <div class="row mb-4">
                <div class="col">
                    <span><strong>Km Final: </strong>{{ frente.km_final }}</span>
                </div>
                <div class="col">
                    <span><strong>Latitude Final: </strong>{{ frente.latitude_final }}</span>
                </div>
            </div>
            <div class="row mb-4">
                <div class="col">
                    <span><strong>Longitude Final: </strong>{{ frente.longitude_final }}</span>
                </div>
                <div class="col">
                    <span><strong>Data Inicial: </strong>{{ dateTimeFormat(frente.data_inicial) }}</span>
                </div>
            </div>
            <div class="row mb-4">
                <div class="col">
                    <span><strong>Data Final: </strong>{{ dateTimeFormat(frente.data_final) }}</span>
                </div>
            </div>
        </template>
    </Modal>
</template>