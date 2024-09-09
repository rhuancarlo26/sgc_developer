<!-- resources\js\Pages\Servico\ControleDeOcorrencias\Execucao\ControleRNC\VisualizarRNCModal.vue -->
<template>
    <Modal ref="modalVisualizarRNC" title="Visualizar RNC" modal-dialog-class="modal-xl">
        <template #body>
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#dadosOcorrencia" role="tab">
                        Local
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#vistoria" role="tab">
                        Descrição
                    </a>
                </li>
            </ul>
            <div class="tab-content">
                <!-- Dados da Ocorrencia -->
                <div class="tab-pane fade show active" id="dadosOcorrencia" role="tabpanel">
                    <!-- LOCAL -->
                    <h2 class="my-4">LOCAL</h2>
                    <div class="row">
                        <div class="col-md-4 mb-4">
                            <label>BR:</label> <strong>{{ rnc?.br }}</strong>
                        </div>
                        <div class="col-md-4 mb-4">
                            <label>UF:</label> <strong>{{ rnc?.uf }}</strong>
                        </div>
                        <div class="col-md-4 mb-4">
                            <label>Data da Ocorrência:</label> <strong>{{ rnc?.data_ocorrencia }}</strong>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-4">
                            <label>KM:</label> <strong>{{ rnc?.km }}</strong>
                        </div>
                        <div class="col-md-4 mb-4">
                            <label>Estaca:</label> <strong>{{ rnc?.estaca }}</strong>
                        </div>
                        <div class="col-md-4 mb-4">
                            <label>Lado:</label> <strong>{{ rnc?.lado }}</strong>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-4">
                            <label>Latitude:</label> <strong>{{ rnc?.latitude }}</strong>
                        </div>
                        <div class="col-md-4 mb-4">
                            <label>Longitude:</label> <strong>{{ rnc?.longitude }}</strong>
                        </div>
                        <div class="col-md-12 mb-4">
                            <label>Lote:</label> <strong>{{ rnc?.lote }}</strong>
                        </div>
                    </div>
                    <div class="row">
                    </div>
                    <div class="row">
                        <div class="col-md-8 mb-4">
                            <label>Empresa / Consórcio:</label> <strong>{{ rnc?.empresa }}</strong>
                        </div>
                        <div class="col-md-4 mb-4">
                            <label>Contrato de Obra:</label> <strong>{{ rnc?.contrato_obra }}</strong>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mb-4">
                            <label>Indícios de Responsabilidade da Construtora:</label> <strong>{{
                                rnc?.responsabilidade_construtora }}</strong>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-4">
                            <label>RNC Direto:</label> <strong>{{ rnc?.rnc_direto }}</strong>
                        </div>
                        <div class="col-md-4 mb-4">
                            <label>Intensidade de Ocorrência:</label> <strong>{{ rnc?.intensidade }}</strong>
                        </div>
                        <div class="col-md-4 mb-4">
                            <label>Tipo de Ocorrência:</label> <strong>{{ rnc?.tipo }}</strong>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label>Status da Ocorrência:</label> <strong>{{ rnc?.status }}</strong>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label>Ocorrências anteriores:</label> <strong class="badge badge-warning m-1">{{
                                rnc?.ocorrencias_anteriores }}</strong>
                        </div>
                    </div>

                    <!-- DESCRIÇÃO -->
                    <h2 class="my-4">DESCRIÇÃO</h2>
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label>Local da Ocorrência:</label> <strong>{{ rnc?.local }}</strong>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label>Classificação da Ocorrência:</label> <strong>{{ rnc?.classificacao }}</strong>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mb-4">
                            <label>Descrição da Ocorrência:</label> <strong>{{ rnc?.descricao }}</strong>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-4">
                            <label>Área total da Ocorrência (m²):</label> <strong>{{ rnc?.area_total }}</strong>
                        </div>
                        <div class="col-md-4 mb-4">
                            <label>Prazo para Correção (dias): </label> <strong>{{ rnc?.prazo }}</strong>
                        </div>
                    </div>

                    <!-- SUGESTÕES DE AÇÕES CORRETIVAS -->
                    <h2 class="my-4">SUGESTÕES DE AÇÕES CORRETIVAS</h2>
                    <div class="row">
                        <div class="col-md-12 mb-4">
                            <strong>{{ rnc?.sugestoes_acoes }}</strong>
                        </div>
                    </div>

                    <!-- NORMA / FUNDAMENTO LEGAL -->
                    <h2 class="my-4">NORMA / FUNDAMENTO LEGAL</h2>
                    <div class="row">
                        <div class="col-md-12 mb-4">
                            <strong>{{ rnc?.norma_fundamento }}</strong>
                        </div>
                    </div>

                    <!-- REGISTRO FOTOGRÁFICO -->
                    <h2 class="my-4">REGISTRO FOTOGRÁFICO</h2>
                    <div class="row">
                        <div class="col-md-12 mb-4">
                            <table class="table table-bordered table-striped table-hover js-basic-example dataTable">
                                <thead>
                                    <tr>
                                        <th>Arquivo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="d-flex flex-wrap">
                                            <div class="col-md-4 mb-2" v-for="foto in rnc?.fotos" :key="foto">
                                                <img :src="foto" width="100%" alt="">
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- OBSERVAÇÕES -->
                    <h2 class="my-4">OBSERVAÇÕES</h2>
                    <div class="row">
                        <div class="col-md-12 mb-4">
                            <strong>{{ rnc?.observacoes }}</strong>
                        </div>
                    </div>
                </div>
                <!-- Vistoria -->
                <div class="tab-pane fade" id="vistoria" role="tabpanel">
                    <div class="row">
                        <div class="col-md-4 mb-4">
                            <label>ID Ocorrência: </label> <strong>{{ rnc?.id_ocorrencia }}</strong>
                        </div>
                        <div class="col-md-4 mb-4">
                            <label>Data da Ocorrência: </label> <strong>{{ rnc?.data_ocorrencia }}</strong>
                        </div>
                        <div class="col-md-4 mb-4">
                            <label>ID Vistoria: </label> <strong>{{ rnc?.id_vistoria }}</strong>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-4">
                            <label>Data da Vistoria: </label> <strong>{{ rnc?.data_vistoria }}</strong>
                        </div>
                        <div class="col-md-4 mb-4">
                            <label>Local da Vistoria: </label> <strong>{{ rnc?.local_vistoria }}</strong>
                        </div>
                        <div class="col-md-4 mb-4">
                            <label>Vistoriador: </label> <strong>{{ rnc?.vistoriador }}</strong>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mb-4">
                            <label>Observações: </label> <strong>{{ rnc?.observacoes }}</strong>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mb-4">
                            <table class="table table-bordered table-striped table-hover js-basic-example dataTable">
                                <thead>
                                    <tr>
                                        <th>Arquivo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="d-flex flex-wrap">
                                            <div class="col-md-4 mb-2" v-for="foto in rnc?.fotos" :key="foto">
                                                <img :src="foto" width="100%" alt="">
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
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

const modalVisualizarRNC = ref(null);

const abrirModal = () => {
    if (modalVisualizarRNC.value && modalVisualizarRNC.value.getBsModal) {
        const bsModal = modalVisualizarRNC.value.getBsModal();
        if (bsModal) bsModal.show();
    }
};

const close = () => {
    if (modalVisualizarRNC.value && modalVisualizarRNC.value.getBsModal) {
        const bsModal = modalVisualizarRNC.value.getBsModal();
        if (bsModal) bsModal.hide();
    }
};

const fecharModal = () => {
    close();
};

watch(() => props.rnc, (newVal) => {
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