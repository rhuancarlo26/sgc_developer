<!-- resources\js\Pages\Servico\MonAtpFauna\Resultado\ModalNovoResultado.vue -->
<template>
    <div class="modal fade" ref="modalNovoResultado" tabindex="-1" aria-labelledby="modalNovoResultadoLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalNovoResultadoLabel">
                        Novo Resultado
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-2">
                        <div class="col-6">
                            <label for="resultado_id">ID resultado</label> 
                            <input type="text" id="resultado_id" disabled="disabled" class="form-control">
                        </div>
                        <div class="col-6">
                            <label>Data de criação</label> 
                            <input type="datetime-local" disabled="disabled" class="form-control">
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-12">
                            <label>Nome do resultado</label> 
                            <input type="text" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <NavButton @click="cadastrarNovoResultado" type-button="success" title="Cadastrar Resultado" />
                    <NavButton @click="fecharModal" type-button="secondary" title="Fechar" />
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from "vue";
import NavButton from "@/Components/NavButton.vue";

const modalNovoResultado = ref(null);
let modalInstance = null;

const abrirModal = () => {
    modalInstance.show();
};

const fecharModal = () => {
    modalInstance.hide();
};

const cadastrarNovoResultado = () => {
    console.log("cadastrarNovoResultado");
};

onMounted(() => {
    modalInstance = new bootstrap.Modal(modalNovoResultado.value);
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