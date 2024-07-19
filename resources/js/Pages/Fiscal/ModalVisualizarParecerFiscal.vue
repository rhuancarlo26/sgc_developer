<script setup>
import { IconCode, IconMessage, IconBriefcase, IconHome, IconCalendar } from "@tabler/icons-vue";
import Modal from "@/Components/Modal.vue";
import { ref } from "vue";

const parecer = ref(null);
const modalParecer = ref(null);

const abrirModal = (item) => {
    parecer.value = item;
    modalParecer.value.getBsModal().show();
}

defineExpose({ abrirModal });
</script>

<template>
    <Modal ref="modalParecer" title="Parecer" modal-dialog-class="modal-xl">
        <template #body>
            <div class="">
                <div class="card">
                    <div class="card-body">
                        <div class="mb-2">
                            <IconCode class="icon me-2 text-secondary" />
                            Programa: <strong>{{ parecer?.tema?.nome }}</strong>
                        </div>
                        <div class="mb-2">
                            <IconBriefcase class="icon me-2 text-secondary" />
                            Tipo: <strong>{{ parecer?.tipo?.nome }}</strong>
                        </div>
                        <div class="mb-2">
                            <IconHome class="icon me-2 text-secondary" />
                            Status: <strong>
                                <span v-if="parecer?.servico_status_id === 2" class="badge bg-yellow-lt">
                                    {{ parecer.status?.nome }}
                                </span>
                                <span v-else-if="parecer?.servico_status_id === 3" class="badge bg-blue-lt">
                                    {{ parecer.status?.nome }}
                                </span>
                                <span v-else-if="parecer?.servico_status_id === 4" class="badge bg-red-lt">
                                    {{ parecer.status?.nome }}
                                </span>
                            </strong>
                        </div>
                        <div class="mb-2">
                            <IconMessage class="icon me-2 text-secondary" />
                            Parecer: <strong>parecer</strong>
                        </div>
                        <div class="mb-2">
                            <IconCalendar class="icon me-2 text-secondary" />
                            Data do parece: <strong>13/01/1985</strong>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </Modal>
</template>