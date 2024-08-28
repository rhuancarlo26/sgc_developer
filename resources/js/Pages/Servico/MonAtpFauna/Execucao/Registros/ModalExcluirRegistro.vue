<!-- resources\js\Pages\Servico\MonAtpFauna\Execucao\Registros\ModalExcluirRegistro.vue -->
<template>
    <Modal ref="modalExcluirRegistro" title="Excluir Campanha" modal-dialog-class="modal-md">
        <template #body>
            <div class="p-3">
                <h3>Você tem certeza que deseja excluir este registro?</h3>
                <p> <strong>{{ campanha?.nome_id }}</strong> </p>
            </div>
        </template>
        <template #footer>
            <NavButton @click="excluirRegistro" type-button="danger" title="Excluir" />
            <NavButton @click="fecharModal" type-button="secondary" title="Cancelar" />
        </template>
    </Modal>
</template>

<script setup>
import { ref, watch, defineExpose } from 'vue';
import Modal from "@/Components/Modal.vue";
import NavButton from "@/Components/NavButton.vue";

const props = defineProps({
    campanha: { type: Object, default: () => null }
});

const modalExcluirRegistro = ref(null);

const excluirRegistro = () => {
    // form.post(route('contratos.contratada.servicos.controledeocorrencias.ocorrencia.store', { contrato: props.contrato.id, servico: props.servico.id }), {
    //     onSuccess: () => fecharModal()
    // });

    console.log("excluirRegistro")
}

const abrirModal = () => {
    if (modalExcluirRegistro.value && modalExcluirRegistro.value.getBsModal) {
        const bsModal = modalExcluirRegistro.value.getBsModal();
        if (bsModal) bsModal.show();
    }
};

const close = () => {
    if (modalExcluirRegistro.value && modalExcluirRegistro.value.getBsModal) {
        const bsModal = modalExcluirRegistro.value.getBsModal();
        if (bsModal) bsModal.hide();
    }
};

const fecharModal = () => {
    close();
};

watch(() => props.campanha, (newVal) => {
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
