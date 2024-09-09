<!-- resources\js\Pages\Servico\MonAtpFauna\Execucao\Campanhas\ModalEditarCampanha.vue -->
<template>
    <div class="modal fade" ref="modalEditarCampanha" tabindex="-1" aria-labelledby="modalEditarCampanhaLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditarCampanhaLabel">Editar Campanha</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <ul id="myTab" role="tablist" class="nav nav-tabs mb-3">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#campanha_dados_gerais" role="tab">
                                CAMPANHAS
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#campanha_abios" role="tab">
                                ABIO
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#rets_campanha" role="tab">
                                RET's
                            </a>
                        </li>
                    </ul>
                    <div id="myTabContent" class="tab-content">
                        <div class="tab-pane fade show active" id="campanha_dados_gerais" role="tabpanel">
                            <div class="row mb-2">
                                <div class="col-2">
                                    <label for="campanha_id">ID campanha</label>
                                    <input type="text" id="campanha_id" disabled="disabled" class="form-control" />
                                </div>
                                <div class="col-2">
                                    <label for="campanha_rodovia">Rodovia</label>
                                    <input type="text" id="campanha_rodovia" disabled="disabled" class="form-control" />
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-2">
                                    <label>UF inicial</label>
                                    <select class="form-control">
                                        <option value="">-- Selecione --</option>
                                    </select>
                                </div>
                                <div class="col-2">
                                    <label>KM inicial</label>
                                    <input type="text" class="form-control" />
                                </div>
                                <div class="col-3">
                                    <label>Latitude inicial</label>
                                    <input type="text" class="form-control" />
                                </div>
                                <div class="col-3">
                                    <label>Longitude inicial</label>
                                    <input type="text" class="form-control" />
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-2">
                                    <label>UF final</label>
                                    <select class="form-control">
                                        <option value="">-- Selecione --</option>
                                    </select>
                                </div>
                                <div class="col-2">
                                    <label>KM final</label>
                                    <input type="text" class="form-control" />
                                </div>
                                <div class="col-3">
                                    <label>Latitude final</label>
                                    <input type="text" class="form-control" />
                                </div>
                                <div class="col-3">
                                    <label>Longitude final</label>
                                    <input type="text" class="form-control" />
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-4">
                                    <label>Data inicial</label>
                                    <input type="date" class="form-control" />
                                </div>
                                <div class="col-4">
                                    <label>Data final</label>
                                    <input type="date" class="form-control" />
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-12">
                                    <label>Observação</label>
                                    <textarea class="form-control"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="campanha_abios" role="tabpanel">
                            <div class="row mb-2">
                                <div class="col-3 mb-2">
                                    <label>ABIO vinculadas</label>
                                    <select class="form-control">
                                        <option value="">-- Selecione --</option>
                                    </select>
                                </div>
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
                                                <th>Ação</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="rets_campanha" role="tabpanel">
                            <div class="col-3 mb-2">
                                <label>RET's vinculadas</label>
                                <select class="form-control">
                                    <option value="">-- Selecione --</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <table class="table table-hover non-hover" style="width: 100% !important;">
                                    <thead>
                                        <tr role="row">
                                            <th>Nome do Arquivo</th>
                                            <th>Data de inclusão</th>
                                            <th>Ação</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <NavButton @click="editarCampanha" type-button="success" title="Editar Campanha" />
                    <NavButton @click="fecharModal" type-button="secondary" title="Fechar" />
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from "vue";
import NavButton from "@/Components/NavButton.vue";

const modalEditarCampanha = ref(null);
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
    modalInstance = new bootstrap.Modal(modalEditarCampanha.value);
});

onBeforeUnmount(() => {
    modalInstance.dispose();
});

defineExpose({ abrirModal, fecharModal });
</script>
