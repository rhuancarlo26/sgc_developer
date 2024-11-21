<script setup>
import Modal from "@/Components/Modal.vue";
import {ref} from "vue";
import {dateTimeFormat} from "@/Utils/DateTimeUtils.js";
import NavButton from "@/Components/NavButton.vue";
import {IconEye} from "@tabler/icons-vue";

const modalVisualizarPassagemFauna = ref();
const passagemFauna = ref({});

const abrirModal = (item) => {
    passagemFauna.value = item;

    modalVisualizarPassagemFauna.value.getBsModal().show();
}

defineExpose({abrirModal});
</script>

<template>
    <Modal ref="modalVisualizarPassagemFauna" :title="'Passagem de fauna ' + passagemFauna.nome_id"
           modal-dialog-class="modal-xl">
        <template #body>
            <div class="row">
                <div class="col">
                    <div class="table-responsive">
                        <table class="table card-table table-bordered">
                            <tr>
                                <th>ID passagem de fauna</th>
                                <td>{{ passagemFauna.nome_id }}</td>
                            </tr>
                            <tr>
                                <th>Rodovia</th>
                                <td>{{ passagemFauna.rodovia }}</td>
                            </tr>
                            <tr>
                                <th>Tipo de estrutura</th>
                                <td>{{ passagemFauna.tipo_de_estrutura }}</td>
                            </tr>
                            <tr>
                                <th>Nome</th>
                                <td>{{ passagemFauna.nome }}</td>
                            </tr>
                            <tr>
                                <th>Latitude</th>
                                <td>{{ passagemFauna.latitude }}</td>
                            </tr>
                            <tr>
                                <th>Observações</th>
                                <td>{{ passagemFauna.observacao }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="col">
                    <div class="table-responsive">
                        <table class="table card-table table-bordered">
                            <tr>
                                <th>Data</th>
                                <td>{{ dateTimeFormat(passagemFauna.created_at) }}</td>
                            </tr>
                            <tr>
                                <th>UF</th>
                                <td>{{ passagemFauna.uf }}</td>
                            </tr>
                            <tr>
                                <th>Classificação</th>
                                <td>{{ passagemFauna.classificacao }}</td>
                            </tr>
                            <tr>
                                <th>Dimensões (m²)</th>
                                <td>{{ passagemFauna.dimensoes }}</td>
                            </tr>
                            <tr>
                                <th>Logitude</th>
                                <td>{{ passagemFauna.longitude }}</td>
                            </tr>
                            <tr>
                                <th>Km</th>
                                <td>{{ passagemFauna.km }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </template>
        <template #footer>
        </template>
    </Modal>
</template>
