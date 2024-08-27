<script setup>
import {ref} from "vue";
import Modal from "@/Components/Modal.vue";

const modalRef = ref();
const parecer = ref(null);
const abrirModal = async (item) => {
    parecer.value = item;
    modalRef.value.getBsModal().show();
}

defineExpose({abrirModal});
</script>

<template>
    <form @submit.prevent="save">
        <Modal ref="modalRef" title="Visualizar Área de Supressão" modal-dialog-class="modal-xl">
            <template #body>
                <div v-if="parecer" class="card card-body">
                    <div class="row">
                        <div class="col-12">
                            <p>Programa: <span class="fw-bold">{{ parecer.programa }}</span></p>
                        </div>
                        <div class="col-12">
                            <p>Tipo: <span class="fw-bold">{{ parecer.tipo }}</span></p>
                        </div>
                        <div class="col-12">
                            <p>Status:
                                <span class="fw-bold">
                                    <span v-if="parecer.fk_status === 1" class="badge text-bg-primary">Em confecção</span>
                                    <span v-if="parecer.fk_status === 2" class="badge text-bg-warning">Em análise</span>
                                    <span v-if="parecer.fk_status === 3" class="badge text-bg-primary">Aprovado</span>
                                    <span v-if="parecer.fk_status === 4" class="badge text-bg-danger">Pendente</span>
                                </span>
                            </p>
                        </div>
                        <div class="col-12">
                            <p>Parecer: <span class="fw-bold">{{ parecer.parecer }}</span></p>
                        </div>
                        <div class="col-12">
                            <p>Data do parecer: <span class="fw-bold">{{ parecer.data_parecer }}</span></p>
                        </div>
                    </div>
                </div>
            </template>
            <template #footer>
                <button @click="modalRef.getBsModal().hide()" type="button" class="btn btn-secondary">Fechar</button>
            </template>
        </Modal>
    </form>

</template>
