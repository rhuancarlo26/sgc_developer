<!-- resources\js\Pages\Servico\MonAtpFauna\Execucao\Campanhas\ModalNovaCampanha.vue -->
<template>
    <div class="modal fade" ref="modalNovaCampanha" tabindex="-1" aria-labelledby="modalNovaCampanhaLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalNovaCampanhaLabel">Nova Campanha</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="campanha_dados_gerais">
                        <div class="row mb-2">
                            <div class="col-2">
                                <label for="campanha_id">ID campanha</label>
                                <input type="text" id="campanha_id" disabled="disabled" class="form-control">
                            </div>
                            <div class="col-2">
                                <label for="campanha_rodovia">Rodovia</label>
                                <input type="text" id="campanha_rodovia" disabled="disabled" class="form-control">
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-2">
                                <label>UF inicial</label>
                                <select class="form-control">
                                    <option value="14">PA - Pará</option>
                                </select>
                            </div>
                            <div class="col-2">
                                <label>KM inicial</label>
                                <input type="text" class="form-control">
                            </div>
                            <div class="col-3">
                                <label>Latitude inicial</label>
                                <input type="text" class="form-control">
                            </div>
                            <div class="col-3">
                                <label>Longitude inicial</label>
                                <input type="text" class="form-control">
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-2">
                                <label>UF final</label>
                                <select class="form-control">
                                    <option value="14">PA - Pará</option>
                                </select>
                            </div>
                            <div class="col-2">
                                <label>KM final</label>
                                <input type="text" class="form-control">
                            </div>
                            <div class="col-3">
                                <label>Latitude final</label>
                                <input type="text" class="form-control">
                            </div>
                            <div class="col-3">
                                <label>Longitude final</label>
                                <input type="text" class="form-control">
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-4">
                                <label>Data inicial</label>
                                <input type="date" class="form-control">
                            </div>
                            <div class="col-4">
                                <label>Data final</label>
                                <input type="date" class="form-control">
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-12">
                                <label>Observação</label>
                                <textarea class="form-control"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <NavButton @click="cadastrarNovaCampanha" type-button="success" title="Cadastrar Registro" />
                    <NavButton @click="fecharModal" type-button="secondary" title="Fechar" />
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from "vue";
import NavButton from "@/Components/NavButton.vue";

const modalNovaCampanha = ref(null);
let modalInstance = null;

const abrirModal = () => {
  modalInstance.show();
};

const fecharModal = () => {
  modalInstance.hide();
};

const cadastrarNovaCampanha = () => {
  console.log("cadastrarNovaCampanha");
};

onMounted(() => {
  modalInstance = new bootstrap.Modal(modalNovaCampanha.value);
});

onBeforeUnmount(() => {
  modalInstance.dispose();
});

defineExpose({ abrirModal, fecharModal });
</script>