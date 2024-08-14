<script setup>
import {onMounted, ref, watch} from "vue";
import Modal from "@/Components/Modal.vue";
import {dateTimeFormat} from "@/Utils/DateTimeUtils.js";
import Table from "@/Components/Table.vue";
import axios from "axios";

const modalRef = ref();
const resultado = ref(null);

const tab = ref(null);

const abrirModal = async (item) => {
    resultado.value = item;
    modalRef.value.getBsModal().show();
    tab.value = 'supressao';
}

const analiseSupressao = ref(null);

const analyzeSupressao = async (item) => {
    const {data} = await axios.get(route('contratos.contratada.servicos.supressao-vegetacao.resultado.supressao-analise', {
        servico: item.servico_id,
        resultado: item.id
    }))
    return data
}

watch(tab, async (value) => {
    if (!resultado.value || !value) return;
    if (value === 'supressao' && !analiseSupressao.value) {
        analiseSupressao.value = await analyzeSupressao(resultado.value);
    }
}, {immediate: true});

onMounted(() => {
    modalRef.value.$el.addEventListener('hidden.bs.modal', () => {
        tab.value = null;
        resultado.value = null;
        analiseSupressao.value = null;
    });
});

defineExpose({abrirModal});
</script>

<template>
    <Modal ref="modalRef" title="Visualizar Área de Supressão" modal-dialog-class="modal-xl">
        <template #body>
            <div class="card">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs">
                        <li class="nav-item">
                            <a href="#supressao" @click="tab = 'supressao'" class="nav-link active"
                               data-bs-toggle="tab">Supressão</a>
                        </li>
                        <li class="nav-item">
                            <a href="#pilhas" @click="tab = 'pilhas'" class="nav-link" data-bs-toggle="tab">Pilhas</a>
                        </li>
                        <li class="nav-item">
                            <a href="#destinacao" @click="tab = 'destinacao'" class="nav-link" data-bs-toggle="tab">Destinação</a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        <div v-if="analiseSupressao" class="tab-pane active show" id="supressao">
                            <div class="row row-gap-2">
                                <div class="col-12">
                                    <h4>Tabela com as áreas de supressão de vegetação</h4>
                                    <Table
                                        :columns="['ID Código', 'Data Inicial', 'Data Final', 'Nº ASV', 'Bioma', 'Área em APP (ha)', 'Área Fora APP (ha)', 'Área Total (ha)']"
                                        :records="{ data: analiseSupressao.supressao, links: [] }"
                                        table-class="table-hover">
                                        <template #body="{ item, key }">
                                            <tr>
                                                <td>{{ item.chave }}</td>
                                                <td>{{ dateTimeFormat(item.dt_inicial) }}</td>
                                                <td>{{ dateTimeFormat(item.dt_final) }}</td>
                                                <td>{{ item.licenca.numero_licenca }}</td>
                                                <td>{{ item.bioma.nome }}</td>
                                                <td>{{ item.area_em_app }}</td>
                                                <td>{{ item.area_fora_app }}</td>
                                                <td>{{ item.area_total }}</td>
                                            </tr>
                                        </template>
                                    </Table>
                                </div>
                                <div class="col-12">
                                    <hr class="mb-4"/>
                                </div>
                                <div class="col-12">
                                    <h4>Tabela resumo da área suprimida no período</h4>
                                    <Table
                                        :columns="['Descrição', 'Área em APP (ha)', 'Área Fora APP (ha)', 'Área Total (ha)']"
                                        :records="{ data: analiseSupressao.resumo, links: [] }"
                                        table-class="table-hover">
                                        <template #body="{ item, key }">
                                            <tr>
                                                <td>{{ item.desc }}</td>
                                                <td>{{ item.area_app }}</td>
                                                <td>{{ item.area_fora }}</td>
                                                <td>{{ item.total }}</td>
                                            </tr>
                                        </template>
                                    </Table>
                                </div>
                                <div class="col-12">
                                    <hr class="mb-4"/>
                                </div>
                                <div class="col-12">
                                    <h4>
                                        Percentual suprimido em relação ao total previsto nas ASVs: {{ analiseSupressao.percentualSuprimido }}%
                                    </h4>
                                </div>
                                <div class="col-12">
                                    <hr class="mb-4"/>
                                </div>
                                <div class="col-12">
                                    <h4>Gráfico de área total suprimida (área APP e fora APP) por mês</h4>
                                    CHART
                                </div>
                                <div class="col-12">
                                    <hr class="mb-4"/>
                                </div>
                                <div class="col-12 mt-3">
                                    <label for="">Análise da Supressão de Vegetação:</label>
                                    <textarea v-model="resultado.analise_supressao_vegetacao" class="form-control" rows="3"></textarea>
                                </div>
                                <div class="col-12 mt-3">
                                    <hr class="border-dark">
                                </div>
                                <div class="col-12">
                                    <h4>Tabela de supressão de vegetal de espécies ameaçadas/protegidas</h4>
                                    <Table
                                        :columns="['Descrição', 'Área em APP (ha)', 'Área Fora APP (ha)', 'Área Total (ha)']"
                                        :records="{ data: analiseSupressao.cortes, links: [] }"
                                        table-class="table-hover">
                                        <template #body="{ item, key }">
                                            <tr>
                                                <td>{{ item.nome }}</td>
                                                <td>{{ item.nome_popular }}</td>
                                                <td>{{ item.legislacao }}</td>
                                                <td>{{ item.n_individuos }}</td>
                                                <td>{{ item.n_compensacao }}</td>
                                            </tr>
                                        </template>
                                    </Table>
                                </div>
                                <div class="col-12">
                                    <label>Análise da Supressão de Espécies Protegidas:</label>
                                    <textarea v-model="resultado.analise_supressao_especies_protegida" class="form-control" rows="3"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="pilhas">
                            pilhas
                        </div>
                        <div class="tab-pane" id="destinacao">
                            destinacao
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
