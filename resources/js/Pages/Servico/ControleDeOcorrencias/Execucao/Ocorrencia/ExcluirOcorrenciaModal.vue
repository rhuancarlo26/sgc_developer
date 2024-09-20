<!-- resources\js\Pages\Servico\ControleDeOcorrencias\Execucao\Ocorrencia\ExcluirOcorrenciaModal.vue -->
<template>
    <Modal ref="modalExcluirOcorrencia" title="Excluir Ocorrência" modal-dialog-class="modal-md">
        <template #body>
            <div class="p-3">
                <h3>Você tem certeza que deseja excluir esta ocorrência?</h3>
                <p> <strong>{{ ocorrencia?.nome_id }}</strong> </p>
            </div>
        </template>
        <template #footer>
            <NavButton @click="excluirOcorrencia" type-button="danger" title="Excluir" />
            <NavButton @click="fecharModal" type-button="secondary" title="Cancelar" />
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

const modalExcluirOcorrencia = ref(null);

const excluirOcorrencia = () => {
    // form.post(route('contratos.contratada.servicos.controledeocorrencias.ocorrencia.store', { contrato: props.contrato.id, servico: props.servico.id }), {
    //     onSuccess: () => fecharModal()
    // });

    console.log("excluirOcorrencia")
}

const abrirModal = () => {
    if (modalExcluirOcorrencia.value && modalExcluirOcorrencia.value.getBsModal) {
        const bsModal = modalExcluirOcorrencia.value.getBsModal();
        if (bsModal) bsModal.show();
    }
};

const close = () => {
    if (modalExcluirOcorrencia.value && modalExcluirOcorrencia.value.getBsModal) {
        const bsModal = modalExcluirOcorrencia.value.getBsModal();
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
