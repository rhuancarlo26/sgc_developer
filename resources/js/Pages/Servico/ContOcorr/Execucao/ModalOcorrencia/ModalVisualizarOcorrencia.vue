<script setup>
import Modal from "@/Components/Modal.vue";
import {dateTimeFormat} from "@/Utils/DateTimeUtils.js";
import {usePage} from "@inertiajs/vue3";
import {ref} from "vue";

const modalVisualizarOcorrencia = ref();

let ocorrencia = ref({});

const abrirModal = (item) => {
    ocorrencia.value = {};

    if (item) {
        ocorrencia.value = item;
    }

    modalVisualizarOcorrencia.value.getBsModal().show();
}

defineExpose({abrirModal});
</script>

<template>
    <Modal ref="modalVisualizarOcorrencia" :title="'Ocorrência ' + ocorrencia.nome_id" modal-dialog-class="modal-xl">
        <template #body>
            <div class="mb-4">
                <div class="row">
                    <h3>Local</h3>
                    <div class="col">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table card-table table-bordered table-hover">
                                    <tbody>
                                    <tr>
                                        <th>BR</th>
                                        <td>{{ ocorrencia.rodovia?.rodovia }}</td>
                                    </tr>
                                    <tr>
                                        <th>UF</th>
                                        <td>{{ ocorrencia.rodovia?.uf?.uf }}</td>
                                    </tr>
                                    <tr>
                                        <th>Data da Ocorrência</th>
                                        <td>{{ dateTimeFormat(ocorrencia.data_ocorrencia) }}</td>
                                    </tr>
                                    <tr>
                                        <th>KM</th>
                                        <td>{{ ocorrencia.km }}</td>
                                    </tr>
                                    <tr>
                                        <th>Estaca</th>
                                        <td>{{ ocorrencia.estaca }}</td>
                                    </tr>
                                    <tr>
                                        <th>Lado</th>
                                        <td>{{ ocorrencia.lado }}</td>
                                    </tr>
                                    <tr>
                                        <th>Latitude</th>
                                        <td>{{ ocorrencia.latitude }}</td>
                                    </tr>
                                    <tr>
                                        <th>Longitude</th>
                                        <td>{{ ocorrencia.longitude }}</td>
                                    </tr>
                                    <tr>
                                        <th>Lote</th>
                                        <td>{{ ocorrencia.lote?.nome_id }}</td>
                                    </tr>
                                    <tr>
                                        <th>Empresa / Consórico</th>
                                        <td>{{ ocorrencia.lote?.empresa }}</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table card-table table-bordered table-hover">
                                    <tbody>
                                    <tr>
                                        <th>Contrato de obra</th>
                                        <td>{{ ocorrencia.lote?.num_contrato }}</td>
                                    </tr>
                                    <tr>
                                        <th>Indícios de responsabilidade da construtora</th>
                                        <td>{{ ocorrencia.indicio_responsabilidade ? 'Sim' : 'Não' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Possível causa</th>
                                        <td>{{ ocorrencia.possivel_causa }}</td>
                                    </tr>
                                    <tr>
                                        <th>Descrição da causa</th>
                                        <td>{{ ocorrencia.descricao_causa }}</td>
                                    </tr>
                                    <tr>
                                        <th>RNC direto</th>
                                        <td>{{ ocorrencia.rnc_direto ? 'Sim' : 'Não' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Intensidade de ocorrência</th>
                                        <td>{{ ocorrencia.intensidade }}</td>
                                    </tr>
                                    <tr>
                                        <th>Tipo de ocorrência</th>
                                        <td>{{ ocorrencia.tipo }}</td>
                                    </tr>
                                    <tr>
                                        <th>Status da ocorrência</th>
                                        <td>{{ ocorrencia.status }}</td>
                                    </tr>
                                    <tr>
                                        <th>Ocorrências anteriores</th>
                                        <td></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mb-4">
                <h3>Descrição</h3>
                <div class="row">
                    <div class="col">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table card-table table-bordered table-hover">
                                    <tbody>
                                    <tr>
                                        <th>Local da ocorrência</th>
                                        <td>{{ ocorrencia.local }}</td>
                                    </tr>
                                    <tr>
                                        <th>Classificação da ocorrência</th>
                                        <td>{{ ocorrencia.classificacao }}</td>
                                    </tr>
                                    <tr>
                                        <th>Descrição da ocorrência</th>
                                        <td>{{ ocorrencia.descricao }}</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table card-table table-bordered table-hover">
                                    <tbody>
                                    <tr>
                                        <th>Área total da ocorrência (m²)</th>
                                        <td>{{ ocorrencia.area_total }}</td>
                                    </tr>
                                    <tr>
                                        <th>Prazo para correção (dias)</th>
                                        <td>{{ ocorrencia.prazo }}</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mb-4">
                <div class="row">
                    <h3>Sugestões de ações corretivas</h3>
                    <div class="col">
                        <span><strong>Ações: </strong>{{ ocorrencia.acoes }}</span>
                    </div>
                </div>
            </div>
            <div class="mb-4">
                <div class="row">
                    <h3>Norma / fundamento legal</h3>
                    <div class="col">
                        <span><strong>Norma: </strong>{{ ocorrencia.norma }}</span>
                    </div>
                </div>
            </div>
            <div class="mb-4">
                <div class="row">
                    <h3>Registro fotográfico</h3>
                    <div class="row">
                        <div v-for="registro in ocorrencia.registros" :key="registro.id" class="col-2">
                            <img :src="usePage().props.app_url + '/storage/' + registro.caminho_arquivo"
                                 alt="registro fotográfico">
                        </div>
                    </div>
                </div>
            </div>
            <div>
                <div class="row">
                    <h3>Observações</h3>
                    <div class="col">
                        <span><strong>Observação: </strong>{{ ocorrencia.obs }}</span>
                    </div>
                </div>
            </div>
        </template>
        <template #footer>
        </template>
    </Modal>
</template>
