<script setup>
import Modal from "@/Components/Modal.vue";
import { ref } from "vue";
import {IconEye, IconPencil, IconTrash} from "@tabler/icons-vue";
import NavButton from "@/Components/NavButton.vue";
import Table from "@/Components/Table.vue";
import NavLink from "@/Components/NavLink.vue";
import LinkConfirmation from "@/Components/LinkConfirmation.vue";
import {Link} from "@inertiajs/vue3";

const modalVisualizacao = ref(null);
const pmqa = ref(null);

const abrirModal = (item) => {
    pmqa.value = item;
    modalVisualizacao.value.getBsModal().show();
}

defineExpose({ abrirModal });
</script>

<template>
    <Modal ref="modalVisualizacao" title="Programa de monitoramento de qualidade da água" modal-dialog-class="modal-xl">
        <template #body>
            <div class="accordion" id="accordion-example">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="heading-1">
                        <button class="accordion-button " type="button" data-bs-toggle="collapse"
                            data-bs-target="#introducao" aria-expanded="true">
                            Pontos
                        </button>
                    </h2>
                    <div id="introducao" class="accordion-collapse collapse show" data-bs-parent="#accordion-example">
                        <div class="accordion-body pt-0">
                            <pre>
                                {{pmqa}}
                            </pre>
<!--                            <Table-->
<!--                                :columns="['Cod. ponto', 'Pt. coleta', 'Latitude', 'Longitude', 'Classificação', 'Classe', 'Tipo de ambiente', 'UF', 'Municipio', 'Bacia hidrografica', 'Km rodovia', 'Estaca', 'Ação']"-->
<!--                                :records="pontos" table-class="table-hover">-->
<!--                                <template #body="{ item }">-->
<!--                                    <tr>-->
<!--                                        <td class="text-center">{{ item.id }}</td>-->
<!--                                        <td class="text-center">{{ item.nome_ponto_coleta }}</td>-->
<!--                                        <td class="text-center">{{ item.lat_x }}</td>-->
<!--                                        <td class="text-center">{{ item.long_y }}</td>-->
<!--                                        <td>{{ item.classificacao }}</td>-->
<!--                                        <td class="text-center">{{ item.classe }}</td>-->
<!--                                        <td class="text-center">{{ item.tipo_ambiente }}</td>-->
<!--                                        <td class="text-center">{{ item.UF }}</td>-->
<!--                                        <td>{{ item.municipio }}</td>-->
<!--                                        <td>{{ item.bacia_hidrografica }}</td>-->
<!--                                        <td class="text-center">{{ item.km_rodovia }}</td>-->
<!--                                        <td class="text-center">{{ item.estaca }}</td>-->
<!--                                        <td class="text-center">-->
<!--                                            <NavButton @click="abrirModalVisualizar(item)" type-button="info" class="btn-icon"-->
<!--                                                       :icon="IconEye"/>-->
<!--                                            <template-->
<!--                                                v-if="!servico.pmqa_config_lista_parecer || servico.pmqa_config_lista_parecer?.status_id === 1">-->
<!--                                                <NavLink class="btn btn-icon btn-primary me-1" v-if="ap(aprovacao)"-->
<!--                                                         route-name="contratos.contratada.servicos.pmqa.configuracao.ponto.create"-->
<!--                                                         :param="{ contrato: contrato.id, servico: servico.id, ponto: item.id }"-->
<!--                                                         :icon="IconPencil"/>-->
<!--                                                <LinkConfirmation v-slot="confirmation" v-if="ap(aprovacao)"-->
<!--                                                                  :options="{ text: 'A remoção de um ponto será permanente.' }">-->
<!--                                                    <Link :onBefore="confirmation.show"-->
<!--                                                          :href="route('contratos.contratada.servicos.pmqa.configuracao.ponto.delete', { contrato: contrato.id, servico: servico.id, ponto: item.id })"-->
<!--                                                          as="button" method="delete" type="button" class="btn btn-icon btn-danger">-->
<!--                                                        <IconTrash/>-->
<!--                                                    </Link>-->
<!--                                                </LinkConfirmation>-->
<!--                                            </template>-->
<!--                                        </td>-->
<!--                                    </tr>-->
<!--                                </template>-->
<!--                            </Table>-->
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="heading-2">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#justificativa" aria-expanded="false">
                            Parametros
                        </button>
                    </h2>
                    <div id="justificativa" class="accordion-collapse collapse" data-bs-parent="#accordion-example">
                        <div class="accordion-body pt-0">
                            {{ item?.justificativa }}
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="heading-3">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#objetivos" aria-expanded="false">
                            Pontos vinculados
                        </button>
                    </h2>
                    <div id="objetivos" class="accordion-collapse collapse" data-bs-parent="#accordion-example">
                        <div class="accordion-body pt-0">
                            {{ item?.objetivo }}
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </Modal>
</template>
