<!-- resources\js\Pages\Servico\MonAtpFauna\Execucao\Campanhas\ModalVisualizarCampanha.vue -->
<template>
    <div class="modal fade" ref="modalVisualizarCampanha" tabindex="-1" aria-labelledby="modalVisualizarCampanhaLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalVisualizarCampanhaLabel">
                        Visualizar Campanha
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <ul id="myTab" role="tablist" class="nav nav-tabs mb-3">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#campanhas" role="tab">
                                CAMPANHAS
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#abio" role="tab">
                                ABIO
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#rets" role="tab">
                                RET's
                            </a>
                        </li>
                    </ul>
                    <div id="myTabContent" class="tab-content">
                        <div class="tab-pane fade show active" id="campanhas" role="tabpanel">
                            <div class="row mb-3">
                                <div class="col-3">
                                    <strong for="campanha_rodovia">Rodovia: </strong>
                                    <label>000</label>
                                </div>
                                <div class="col-3">
                                    <strong>UF inicial: </strong>
                                    <label>UF - Unidade Federativa</label>
                                </div>
                                <div class="col-3">
                                    <strong>KM inicial: </strong>
                                    <label>00</label>
                                </div>
                                <div class="col-3">
                                    <strong>Latitude inicial: </strong>
                                    <label>390533</label>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-3">
                                    <strong>Longitude inicial: </strong>
                                    <label>9041395</label>
                                </div>
                                <div class="col-3">
                                    <strong>Data inicial: </strong>
                                    <label>01/01/2001</label>
                                </div>
                                <div class="col-3">
                                    <strong>UF final: </strong>
                                    <label>UF - Unidade Federativa</label>
                                </div>
                                <div class="col-3">
                                    <strong>KM final: </strong>
                                    <label>986</label>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-3">
                                    <strong>Latitude final: </strong>
                                    <label>697431</label>
                                </div>
                                <div class="col-3">
                                    <strong>Longitude final: </strong>
                                    <label>9491456</label>
                                </div>
                                <div class="col-3">
                                    <strong>Data final: </strong>
                                    <label>01/01/2001</label>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-12">
                                    <strong>Observação: </strong>
                                    <br>
                                    <label>
                                        Apenas um teste para averiguar se o sistema está funcionando adequadamente ou
                                        necessita de correções
                                        relacionadas ao banco de dados ou layout da página. 28/08/2023
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="abio" role="tabpanel">
                        <div class="row mb-2">
                            <div class="col-12">
                                <table class="table table-hover non-hover" style="width: 100% !important;">
                                    <thead>
                                        <tr role="row">
                                            <th>Nº Licença</th>
                                            <th>Empreendimento</th>
                                            <th>Emissor</th>
                                            <th>Data de emissão</th>
                                            <th>Vencimento</th>
                                            <th>Responsável</th>
                                            <th>Processo DNIT</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>

                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="rets" role="tabpanel">
                        <div class="row mb-2">
                            <div class="col-12">
                                <table class="table table-hover non-hover" style="width: 100% !important;">
                                    <thead>
                                        <tr role="row">
                                            <th>Nome do Arquivo</th>
                                            <th>Data de inclusão</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>

                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <NavButton @click="fecharModal" type-button="secondary" title="Fechar" />
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from "vue";
import NavButton from "@/Components/NavButton.vue";

const modalVisualizarCampanha = ref(null);
let modalInstance = null;

const abrirModal = () => {
    modalInstance.show();
};

const fecharModal = () => {
    modalInstance.hide();
};

const editarCampanha = () => {
    console.log("editarCampanha");
};

onMounted(() => {
    modalInstance = new bootstrap.Modal(modalVisualizarCampanha.value);
});

onBeforeUnmount(() => {
    modalInstance.dispose();
});

defineExpose({ abrirModal, fecharModal });
</script>