<script setup>
import {ref} from "vue";
import Modal from "@/Components/Modal.vue";
import {dateTimeFormat} from "@/Utils/DateTimeUtils.js";

const modalRef = ref();
const patio = ref(null);
const abrirModal = (item) => {
    patio.value = item;
    modalRef.value.getBsModal().show();
}

defineExpose({abrirModal});
</script>

<template>
    <Modal ref="modalRef" title="Visualizar Pátio Estocagem" modal-dialog-class="modal-xl">
        <template #body>
            <div v-if="patio">
                <p>ID Código: <span class="fw-bold">{{ patio.chave }}</span></p>
                <p>Data de cadastro: <span class="fw-bold">{{ dateTimeFormat(patio.created_at) }}</span></p>
                <p>N° ASV: <span class="fw-bold">{{ patio.licenca.numero_licenca }}</span></p>
                <p>Tipo de Pátio: <span class="fw-bold">{{ patio.tipo.nome }}</span></p>
                <p v-if="patio.observacao" class="mb-0">Observação:</p>
                <p class="fw-bold">{{ patio.observacao }}</p>
            </div>
        </template>
        <template #footer>
            <button @click="modalRef.getBsModal().hide()" type="button" class="btn btn-secondary">Fechar</button>
        </template>
    </Modal>
</template>
