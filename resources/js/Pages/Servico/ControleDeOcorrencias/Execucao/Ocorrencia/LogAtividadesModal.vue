<template>
    <Modal ref="modalLogAtividades" title="Log de Atividades" modal-dialog-class="modal-xl">
        <template #body>
            <table class="table table-bordered">
                <tbody>
                    <tr>
                        <td>Log de Atividades</td>
                    </tr>
                </tbody>
            </table>
        </template>
        <template #footer>
            <NavButton @click="fecharModal" type-button="secondary" title="Fechar" />
        </template>
    </Modal>
</template>

<script setup>
import { ref, watch, defineExpose } from 'vue';
import Modal from "@/Components/Modal.vue";
import NavButton from "@/Components/NavButton.vue";

const props = defineProps({
    log: { type: Object, default: () => null }
});

const modalLogAtividades = ref(null);

const abrirModal = () => {
    if (modalLogAtividades.value && modalLogAtividades.value.getBsModal) {
        const bsModal = modalLogAtividades.value.getBsModal();
        if (bsModal) bsModal.show();
    }
};

const close = () => {
    if (modalLogAtividades.value && modalLogAtividades.value.getBsModal) {
        const bsModal = modalLogAtividades.value.getBsModal();
        if (bsModal) bsModal.hide();
    }
};

const fecharModal = () => {
    close();
};

watch(() => props.log, (newVal) => {
    if (newVal) {
        abrirModal();
    }
});

defineExpose({
    abrirModal,
    fecharModal
});
</script>

<style scoped>
.modal-xl {
    max-width: 80%;
}

.table {
    width: 100%;
    margin-bottom: 1rem;
    color: #212529;
}

.table-bordered {
    border: 1px solid #dee2e6;
}

.table-bordered td,
.table-bordered th {
    border: 1px solid #dee2e6;
}
</style>
