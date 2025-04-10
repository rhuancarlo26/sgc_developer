<template>
    <Modal ref="modalRef" title="Visualizar Lote de Obra" modal-dialog-class="modal-xl">
        <template #body>
            <table class="table table-bordered">
                <tbody>
                    <tr>
                        <td><strong>ID Lote:</strong></td>
                        <td>{{ loteDeObra?.nome_id }}</td>
                    </tr>
                    <tr>
                        <td><strong>Nome do Lote:</strong></td>
                        <td>{{ loteDeObra?.nome }}</td>
                    </tr>
                    <tr>
                        <td><strong>BR:</strong></td>
                        <td>{{ loteDeObra?.rodovia }}</td>
                    </tr>
                    <tr>
                        <td><strong>UF:</strong></td>
                        <td>{{ loteDeObra?.uf }}</td>
                    </tr>
                    <tr>
                        <td><strong>KM Inicial:</strong></td>
                        <td>{{ loteDeObra?.km_inicial }}</td>
                    </tr>
                    <tr>
                        <td><strong>KM Final:</strong></td>
                        <td>{{ loteDeObra?.km_final }}</td>
                    </tr>
                    <tr>
                        <td><strong>Construtora/Consórcio:</strong></td>
                        <td>{{ loteDeObra?.empresa }}</td>
                    </tr>
                    <tr>
                        <td><strong>Nº Contrato:</strong></td>
                        <td>{{ loteDeObra?.num_contrato }}</td>
                    </tr>
                    <tr>
                        <td><strong>Situação:</strong></td>
                        <td>{{ loteDeObra?.situacao_contrato }}</td>
                    </tr>
                    <tr>
                        <td><strong>Supervisora de Obra:</strong></td>
                        <td>{{ loteDeObra?.supervisora_obras }}</td>
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
import { ref, watch } from 'vue';
import Modal from "@/Components/Modal.vue";
import NavButton from "@/Components/NavButton.vue";

const props = defineProps({
    loteDeObra: { type: Object, default: () => null }
});

const modalRef = ref(null);

const open = () => {
    if (modalRef.value) {
        const bsModal = modalRef.value.getBsModal();
        if (bsModal) bsModal.show();
    }
};

const close = () => {
    if (modalRef.value) {
        const bsModal = modalRef.value.getBsModal();
        if (bsModal) bsModal.hide();
    }
};

const fecharModal = () => {
    close();
};

watch(() => props.loteDeObra, (newVal) => {
    if (newVal) {
        open();
    }
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

.table-bordered td, .table-bordered th {
    border: 1px solid #dee2e6;
}
</style>
