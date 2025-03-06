<script setup>
import Modal from "@/Components/Modal.vue";
import { ref } from "vue";

const parecer = ref({});
const modalParecer = ref();

const abrirModal = (itemParecer) => {
    parecer.value = itemParecer;
    modalParecer.value.getBsModal().show();
}

const close = () => {
    modalParecer.value.getBsModal().hide();
}

defineExpose({ abrirModal });
</script>

<template>
    <Modal ref="modalParecer" title="Visualizar pareceres" modal-dialog-class="modal-xl" modalClass="modal-blur">
        <template #body>
            <div class="row mb-4">
                <div class="col">
                    <span><strong>Programa: </strong>Programa de afugentamento e resgate de fauna</span>
                </div>
            </div>
            <div class="row mb-4">
                <div class="col">
                    <span><strong>Tipo: </strong>{{ parecer.tipo }}</span>
                </div>
                <div class="col">
                    <span><strong>Status: </strong></span>
                    <span v-if="parecer.fk_status === 1" class="shadow-none badge text-primary">
                        Em confecção
                    </span>
                    <span v-if="parecer.fk_status === 2" class="shadow-none badge text-warning">
                        Em análise
                    </span>
                    <span v-if="parecer.fk_status === 3" class="shadow-none badge text-info">
                        Aprovado
                    </span>
                    <span v-if="parecer.fk_status === 4" class="shadow-none badge text-danger">
                        Pendente
                    </span>
                </div>
            </div>
            <div class="row mb-4">
                <div class="col">
                    <span><strong>Parecer: </strong>{{ parecer.parecer }}</span>
                </div>
                <div class="col">
                    <span><strong>Data do parecer: </strong>{{ parecer.data_parecer }}</span>
                </div>
            </div>
        </template>
        <template #footer>
            <button type="button" class="btn btn-danger" @click="close">Fechar</button>
        </template>
    </Modal>
</template>