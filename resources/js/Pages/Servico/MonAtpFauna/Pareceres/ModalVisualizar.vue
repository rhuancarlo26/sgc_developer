<script setup>

import {ref} from "vue";
import Modal from "@/Components/Modal.vue";

const modalRef = ref();
const parecer = ref(null)
const abrirModal = async (item = null) => {
    parecer.value = item;
    modalRef.value.getBsModal().show();
}

defineExpose({abrirModal});

</script>

<template>
    <Modal ref="modalRef" title="Resultado" modal-dialog-class="modal-xl">
        <template #body>
            <div class="card">
                <div class="m-sm-4">
                    <div v-if="parecer" class="row">
                        <div class="col-12 d-flex gap-2">
                            <label for="Nome">Programa:</label>
                            <strong>Programa de atropelamento de fauna</strong>
                        </div>
                        <div class="col-12 d-flex gap-2">
                            <label for="Tipo">Tipo:</label>
                            <strong>{{ parecer.tipo }}</strong>
                        </div>
                        <div class="col-12 d-flex gap-2">
                            <label for="NumeroInterno">Status:</label>
                            <span v-if="parecer.fk_status === 2"
                                  class="shadow-none badge badge-warning">Em análise</span>
                            <span v-if="parecer.fk_status === 3"
                                  class="shadow-none badge badge-primary">Aprovado</span>
                            <span v-if="parecer.fk_status === 4"
                                  class="shadow-none badge badge-danger">Pendente</span>
                        </div>
                        <div class="col-12 d-flex gap-2">
                            <label for="NumeroInterno">Parecer:</label>
                            <strong>{{ parecer.parecer }}</strong>
                        </div>
                        <div class="col-12 d-flex gap-2">
                            <label for="NumeroInterno">Data do parecer:</label>
                            <strong>{{ parecer.data_parecer }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </template>
        <template #footer>
            <button @click="modalRef.getBsModal().hide()" type="button" class="btn btn-secondary">Fechar</button>
        </template>
    </Modal>
</template>
