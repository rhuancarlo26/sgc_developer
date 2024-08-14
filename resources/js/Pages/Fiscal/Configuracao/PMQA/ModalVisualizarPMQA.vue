<script setup>
import Modal from "@/Components/Modal.vue";
import {ref} from "vue";
import Table from "@/Components/Table.vue";

const modalVisualizacao = ref(null);
const pmqa = ref(null);
const pontos = ref(null);
const parametro = ref(null);

const abrirModal = (item) => {
    pontos.value = item.pontos
    parametro.value = item.parametros
    modalVisualizacao.value.getBsModal().show();
}


defineExpose({abrirModal});
</script>

<template>
    <Modal ref="modalVisualizacao" title="Programa de monitoramento de qualidade da água" modal-dialog-class="modal-xl">
        <template #body>
            <div class="accordion" id="accordion-example">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="heading-1">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#pontos" aria-expanded="false">
                            Pontos
                        </button>
                    </h2>
                    <div id="pontos" class="accordion-collapse collapse" data-bs-parent="#accordion-example">
                        <div class="accordion-body pt-0">
                            <table class="table card-table table-bordered table-hover">
                                <thead>
                                <tr>
                                    <th class="text-center">Cod. ponto</th>
                                    <th class="text-center">Pt. coleta</th>
                                    <th class="text-center">Latitude</th>
                                    <th class="text-center">Longitude</th>
                                    <th>Classificação</th>
                                    <th class="text-center">Classe</th>
                                    <th class="text-center">Tipo de ambiente</th>
                                    <th class="text-center">UF</th>
                                    <th>Municipio</th>
                                    <th>Bacia hidrografica</th>
                                    <th class="text-center">Km rodovia</th>
                                    <th class="text-center">Estaca</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="item in pontos">
                                    <td class="text-center">{{ item.id }}</td>
                                    <td class="text-center">{{ item.nome_ponto_coleta }}</td>
                                    <td class="text-center">{{ item.lat_x }}</td>
                                    <td class="text-center">{{ item.long_y }}</td>
                                    <td>{{ item.classificacao }}</td>
                                    <td class="text-center">{{ item.classe }}</td>
                                    <td class="text-center">{{ item.tipo_ambiente }}</td>
                                    <td class="text-center">{{ item.UF }}</td>
                                    <td>{{ item.municipio }}</td>
                                    <td>{{ item.bacia_hidrografica }}</td>
                                    <td class="text-center">{{ item.km_rodovia }}</td>
                                    <td class="text-center">{{ item.estaca }}</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="heading-2">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#parametros" aria-expanded="false">
                            Parametros
                        </button>
                    </h2>
                    <div id="parametros" class="accordion-collapse collapse" data-bs-parent="#accordion-example">
                        <div class="accordion-body pt-0">
                            <table class="table card-table table-bordered table-hover">
                                <thead>
                                <tr>
                                    <th class="text-center">Nome</th>
                                    <th class="text-center">Parâmetros</th>
                                    <th class="text-center">Pontos vinculados</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="item in parametro">
                                    <td>{{ item.nome }}</td>
                                    <td>
                                        <p v-if="item.parametros">
                                          <span v-for="(record, i) in item.parametros" :key="parametro"
                                                class="badge bg-warning text-white m-1">
                                            {{ record.parametro }}
                                          </span>
                                        </p>
                                    </td>
                                    <td class="text-center"> {{ item.pontos.length }}</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </Modal>
</template>
