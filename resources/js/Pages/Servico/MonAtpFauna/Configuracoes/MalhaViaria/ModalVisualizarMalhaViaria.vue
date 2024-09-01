<script setup>
import Modal from "@/Components/Modal.vue";
import {ref} from "vue";

const modalRef = ref({});
const malhaViaria = ref({});
const abrirModal = (item) => {
    malhaViaria.value = item;
    modalRef.value.getBsModal().show();
}

defineExpose({abrirModal});
</script>

<template>
    <Modal ref="modalRef" title="Visualizar malha viária" >
        <template #body>
            <div class="row">
                <div class="col-md-12">
                    <label for="obs">Nome:</label>
                    <strong>{{ malhaViaria.nome_licenca }}</strong>
                </div>
                <div class="col-md-12">
                    <label for="data_campanha_inicial">Empreendimento:</label>
                    <strong>{{ malhaViaria.empreendimento }}</strong>
                </div>
                <div class="col-md-12">
                    <label for="obs">UF:</label>
                    <strong>{{ malhaViaria.nome_estado }}</strong>
                </div>
                <div class="col-md-12">
                    <label for="obs">Rodovia:</label>
                    <strong>{{ malhaViaria.rodovia }}</strong>
                </div>
                <div class="col-md-12">
                    <label for="periodo">KM inicial:</label>
                    <strong>{{ malhaViaria.km_fim }}</strong>
                </div>
                <div class="col-md-12">
                    <label for="obs">KM final:</label>
                    <strong>{{ malhaViaria.km_inicio }}</strong>
                </div>
                <div class="col-md-12">
                    <label for="obs">Extensão:</label>
                    <strong>{{ malhaViaria.extensao }}</strong>
                </div>
            </div>
        </template>
        <template #footer>
            <button @click="modalRef.getBsModal().hide()" type="button" class="btn btn-secondary">Fechar</button>
        </template>
    </Modal>
</template>
