<script setup>
import Modal from "@/Components/Modal.vue";
import {dateTimeFormat} from "@/Utils/DateTimeUtils.js";
import {usePage} from "@inertiajs/vue3";
import {ref} from "vue";

const modalVisualizarOcorrenciaHistorico = ref();

let ocorrencia = ref({});

const abrirModal = (item) => {
    ocorrencia.value = {};

    if (item) {
        ocorrencia.value = item;
    }

    modalVisualizarOcorrenciaHistorico.value.getBsModal().show();
}

defineExpose({abrirModal});
</script>

<template>
    <Modal ref="modalVisualizarOcorrenciaHistorico" :title="'Ocorrência ' + ocorrencia.nome_id"
           modal-dialog-class="modal-xl">
        <template #body>
            <div class="row">
                <h3>Histórico</h3>
                <div class="col">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table card-table table-bordered table-hover">
                                <thead>
                                <tr>
                                    <th>Ação</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="item in ocorrencia.historico" :key="item.id">
                                    <td>{{ item.levantamento?.nome }}</td>
                                    <td>{{ dateTimeFormat(item.created_at) }}</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </template>
        <template #footer>
        </template>
    </Modal>
</template>
