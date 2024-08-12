<script setup>
import {ref} from "vue";
import Modal from "@/Components/Modal.vue";

const modalRef = ref();
const observacao = ref(null);
const abrirModal = (obs) => {
    observacao.value = obs;
    modalRef.value.getBsModal().show();
}

defineExpose({abrirModal});
</script>

<template>
    <Modal ref="modalRef" title="Observação" modal-dialog-class="modal-xl">
        <template #body>
            <div class="row row-gap-2">
                {{ observacao }}
            </div>
        </template>
        <template #footer>
            <button @click="modalRef.getBsModal().hide()" type="button" class="btn btn-secondary">Fechar</button>
        </template>
    </Modal>
</template>
