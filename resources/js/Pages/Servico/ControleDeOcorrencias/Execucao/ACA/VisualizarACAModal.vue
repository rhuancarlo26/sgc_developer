<!-- resources\js\Pages\Servico\ControleDeOcorrencias\Execucao\ControleRNC\VisualizarRNCModal.vue -->
<template>
    <Modal ref="visualizarACAModel" title="Visualizar Atestado de Conformidade Ambiental" modal-dialog-class="modal-xl">
        <template #body>
            <div>
                ID ACA: {{ aca?.id_aca }}
                <br>
                <br>
                A Empresa {{ aca?.construtora_aca }}, contrato {{ aca?.contrato }}, cujo objeto é teste atesta que a
                empresa/consórcio {{ aca?.construtora_aca }}, contrato 00 00264/2019,
                Lote {{ aca?.lote_aca }}, apresentou a seguinte relação de Registros de Não Conformidades (RNC)
                atendidos, conforme consta na tabela a seguir:
            </div>

            <hr>

            <table class="table table-bordered table-striped table-hover js-basic-example dataTable">
                <thead>
                    <tr class="text-center">
                        <th class="dt-no-sorting text-wrap px-2">ID Ocorrência</th>
                        <th class="dt-no-sorting text-wrap px-2">Intensidade Ocorrência</th>
                        <th class="dt-no-sorting text-wrap px-2">Data da Ocorrência</th>
                        <th class="dt-no-sorting text-wrap px-2">Data Fim</th>
                        <th class="dt-no-sorting text-wrap px-2">Ocorrência Anterior</th>
                        <th class="dt-no-sorting text-wrap px-2">Prazo Correção</th>
                        <th class="dt-no-sorting px-2">Lote</th>
                        <th class="dt-no-sorting px-2">Empresa / Consórcio</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="text-center">
                        <td> {{ aca?.nome_id }} </td>
                        <td> {{ aca?.intensidade }} </td>
                        <td> {{ aca?.data_ocorrenciaF }} </td>
                        <td> {{ aca?.data_fimF }} </td>
                        <td> {{ aca?.ocorrencia_anterior }} </td>
                        <td> {{ aca?.dias_restantes }} </td>
                        <td> {{ aca?.nome_lote }} </td>
                        <td> {{ aca?.empresa }} </td>
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
    aca: { type: Object, default: () => null }
});

const visualizarACAModel = ref(null);

const abrirModal = () => {
    if (visualizarACAModel.value && visualizarACAModel.value.getBsModal) {
        const bsModal = visualizarACAModel.value.getBsModal();
        if (bsModal) bsModal.show();
    }
};

const close = () => {
    if (visualizarACAModel.value && visualizarACAModel.value.getBsModal) {
        const bsModal = visualizarACAModel.value.getBsModal();
        if (bsModal) bsModal.hide();
    }
};

const fecharModal = () => {
    close();
};

watch(() => props.aca, (newVal) => {
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