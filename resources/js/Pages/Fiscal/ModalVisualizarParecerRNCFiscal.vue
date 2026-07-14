<script setup>
import Modal from "@/Components/Modal.vue";
import { ref } from "vue";

const modalParecerRNC = ref(null);

const abrirModal = (item) => {
    modalParecerRNC.value.getBsModal().show();
}

defineExpose({ abrirModal });
</script>

<template>
    <Modal ref="modalParecerRNC" title="Parecer Fiscal" modal-dialog-class="modal-xl">
        <template #body>
            <div class="card">
                <div class="card-body">
                    <div class="mb-2">
                        <IconHome class="icon me-2 text-secondary" />
                        Status: <strong>
                            <span class="badge bg-yellow-lt">
                                Pendente
                            </span>
                            <span class="badge bg-blue-lt">
                                Aprovado
                            </span>
                            <span class="badge bg-red-lt">
                                Reprovado
                            </span>
                        </strong>
                    </div>
                </div>
            </div>
        </template>
    </Modal>


</template>