<!-- resources\js\Pages\Servico\ControleDeOcorrencias\Execucao\Ocorrencia\VisualizarOcorrenciaModal.vue -->
<template>
    <Modal ref="modalVisualizarOcorrencia" title="Visualizar Ocorrência" modal-dialog-class="modal-xl">
        <template #body>
            <table class="table table-bordered">
                <tbody>
                    <tr>
                        <td><strong>ID Ocorrência:</strong></td>
                        <td>{{ ocorrencia?.nome_id }}</td>
                    </tr>
                    <tr>
                        <td><strong>Intensidade Ocorrência:</strong></td>
                        <td>{{ ocorrencia?.intensidade }}</td>
                    </tr>
                    <tr>
                        <td><strong>Data da Ocorrência:</strong></td>
                        <td>{{ ocorrencia?.data_ocorrenciaF }}</td>
                    </tr>
                    <tr>
                        <td><strong>Data Fim:</strong></td>
                        <td>{{ ocorrencia?.data_fimF }}</td>
                    </tr>
                    <tr>
                        <td><strong>Ocorrência Anterior:</strong></td>
                        <td>{{ ocorrencia?.ocorrencia_anterior }}</td>
                    </tr>
                    <tr>
                        <td><strong>Prazo Correção:</strong></td>
                        <td>{{ ocorrencia?.dias_restantes }}</td>
                    </tr>
                    <tr>
                        <td><strong>Lote:</strong></td>
                        <td>{{ ocorrencia?.nome_lote }}</td>
                    </tr>
                    <tr>
                        <td><strong>Construtora:</strong></td>
                        <td>{{ ocorrencia?.empresa }}</td>
                    </tr>
                    <tr>
                        <td><strong>Status Aprovação:</strong></td>
                        <td>{{ ocorrencia?.status }}</td>
                    </tr>
                    <tr>
                        <td><strong>Envio:</strong></td>
                        <td>{{ ocorrencia?.envio_empresa }}</td>
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
    ocorrencia: { type: Object, default: () => null }
});

const modalVisualizarOcorrencia = ref(null);

const abrirModal = () => {
    if (modalVisualizarOcorrencia.value && modalVisualizarOcorrencia.value.getBsModal) {
        const bsModal = modalVisualizarOcorrencia.value.getBsModal();
        if (bsModal) bsModal.show();
    }
};

const close = () => {
    if (modalVisualizarOcorrencia.value && modalVisualizarOcorrencia.value.getBsModal) {
        const bsModal = modalVisualizarOcorrencia.value.getBsModal();
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
