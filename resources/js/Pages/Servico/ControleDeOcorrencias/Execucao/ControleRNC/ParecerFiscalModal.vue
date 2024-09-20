<!-- resources\js\Pages\Servico\ControleDeOcorrencias\Execucao\Ocorrencia\ExcluirOcorrenciaModal.vue -->
<template>
    <Modal ref="ParecerFiscalModal" title="Parecer Fiscal" modal-dialog-class="modal-md">
        <template #body>
            <div class="p-3">
                <h3>{{ rnc?.status}}</h3>
            </div>
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
    rnc: { type: Object, default: () => null }
});

const ParecerFiscalModal = ref(null);

const abrirModal = () => {
    if (ParecerFiscalModal.value && ParecerFiscalModal.value.getBsModal) {
        const bsModal = ParecerFiscalModal.value.getBsModal();
        if (bsModal) bsModal.show();
    }
};

const close = () => {
    if (ParecerFiscalModal.value && ParecerFiscalModal.value.getBsModal) {
        const bsModal = ParecerFiscalModal.value.getBsModal();
        if (bsModal) bsModal.hide();
    }
};

const fecharModal = () => {
    close();
};

watch(() => props.ocorrencia, (newVal) => {
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
