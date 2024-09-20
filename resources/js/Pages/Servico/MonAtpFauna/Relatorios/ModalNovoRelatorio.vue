<!-- resources\js\Pages\Servico\MonAtpFauna\Relatorios\ModalNovoRelatorio.vue -->
<template>
    <div class="modal fade" ref="modalNovoRelatorio" tabindex="-1" aria-labelledby="modalNovoRelatorioLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalNovoRelatorioLabel">
                        Novo Resultado
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="form-fauna-relatorio" action="javascript:void(0);" novalidate="novalidate" class="simple-example">
                        <div class="form-row">
                            <div class="col-md-8 mb-4">
                                <label for="nome_relatorio">Nome do relatório</label> 
                                <input type="text" id="nome_relatorio" name="nome_relatorio" class="form-control">
                            </div>
                            <div class="col-md-12 mb-4">
                                <label for="lista_campanhas" class="mb-2 mr-2">Selecionar resultado</label>
                                <table class="table table-bordered table-striped table-hover js-basic-example dataTable">
                                    <thead>
                                        <tr class="bg-light">
                                            <th class="col-md-3">Nome do Resultado</th>
                                            <th class="col-md-3">Campanhas</th>
                                            <th class="col-md-3">Data</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <input type="radio" id="2" class="mr-1" value="2"> 
                                                &nbsp;
                                                <label for="2">teste</label>
                                            </td>
                                            <td class="d-flex flex-wrap">
                                                <span class="badge badge-warning m-1">
                                                    1
                                                </span>
                                            </td>
                                            <td>
                                                08/10/2024
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <input type="radio" id="4" class="mr-1" value="4"> 
                                                &nbsp;
                                                <label for="4">teste</label>
                                            </td>
                                            <td class="d-flex flex-wrap">
                                                <span class="badge badge-warning m-1">
                                                    2
                                                </span>
                                            </td>
                                            <td>
                                                08/10/2024
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-12 mb-4">
                                <label for="sobre">Sobre o Relatório</label> 
                                <textarea id="sobre" rows="5" class="form-control"></textarea>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <NavButton @click="cadastrarNovoRelatorio" type-button="success" title="Cadastrar Resultado" />
                    <NavButton @click="fecharModal" type-button="secondary" title="Fechar" />
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from "vue";
import NavButton from "@/Components/NavButton.vue";

const modalNovoRelatorio = ref(null);
let modalInstance = null;

const abrirModal = () => {
    modalInstance.show();
};

const fecharModal = () => {
    modalInstance.hide();
};

const cadastrarNovoRelatorio = () => {
    console.log("cadastrarNovoRelatorio");
};

onMounted(() => {
    modalInstance = new bootstrap.Modal(modalNovoRelatorio.value);
});

onBeforeUnmount(() => {
    modalInstance.dispose();
});

defineExpose({ abrirModal, fecharModal });
</script>


<style scoped>
.tab-pane {
    margin-top: 30px;
}
</style>