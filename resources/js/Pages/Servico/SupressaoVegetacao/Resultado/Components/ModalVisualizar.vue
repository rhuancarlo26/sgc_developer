<script setup>
import {onMounted, ref, watch} from "vue";
import Modal from "@/Components/Modal.vue";
import {dateTimeFormat} from "@/Utils/DateTimeUtils.js";
import Table from "@/Components/Table.vue";
import axios from "axios";
import {useForm} from "@inertiajs/vue3";
import BarChart from "@/Components/BarChart.vue";

const modalRef = ref();
const resultado = ref(null);

const tab = ref(null);

const form = useForm({
    id: null,
    analise_destinacao_pilhas: null,
    analise_pilhas_cadastradas: null,
    analise_supressao_vegetacao: null,
    analise_pilhas_especie_protetigas: null,
    analise_supressao_especies_protegida: null,
})

const abrirModal = async (item) => {
    resultado.value = item;
    Object.assign(form, item)
    modalRef.value.getBsModal().show();
    tab.value = 'supressao';
}

const save = () => {
    form.post(route('contratos.contratada.servicos.supressao-vegetacao.resultado.store-analisar'), {
        preserveState: true,
        onSuccess: () => {
            modalRef.value.getBsModal().hide();
            form.reset();
        }
    })
}

const analyzeSupressao = async (item) => {
    const {data} = await axios.get(route('contratos.contratada.servicos.supressao-vegetacao.resultado.supressao-analise', {
        servico: item.servico_id,
        resultado: item.id
    }))
    return data
}

const analyzePilha = async (item) => {
    const {data} = await axios.get(route('contratos.contratada.servicos.supressao-vegetacao.resultado.pilha-analise', {
        servico: item.servico_id,
        resultado: item.id
    }))
    return data
}

const analyzeDestinacao = async (item) => {
    const {data} = await axios.get(route('contratos.contratada.servicos.supressao-vegetacao.resultado.destinacao-analise', {
        servico: item.servico_id,
        resultado: item.id
    }))
    return data
}

const analiseSupressao = ref(null)
const analisePilha = ref(null)
const analiseDestinacao = ref(null)

watch(tab, async (value) => {
    if (!resultado.value || !value) return;
    if (value === 'supressao' && !analiseSupressao.value) {
        analiseSupressao.value = await analyzeSupressao(resultado.value);
    }
    if (value === 'pilhas' && !analisePilha.value) {
        analisePilha.value = await analyzePilha(resultado.value);
    }
    if (value === 'destinacao' && !analiseDestinacao.value) {
        analiseDestinacao.value = await analyzeDestinacao(resultado.value);
    }
}, {immediate: true});

onMounted(() => {
    modalRef.value.$el.addEventListener('hidden.bs.modal', () => {
        tab.value = null;
        resultado.value = null;
        analiseSupressao.value = null;
        analisePilha.value = null;
        analiseDestinacao.value = null;
    });
});

defineExpose({abrirModal});
</script>

<template>
    <form @submit.prevent="save">
        <Modal ref="modalRef" title="Visualizar Área de Supressão" modal-dialog-class="modal-xl">
            <template #body>
                <div class="card">
                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs">
                            <li class="nav-item">
                                <a href="#supressao" @click="tab = 'supressao'" :class="{active: tab === 'supressao'}" class="nav-link"
                                   data-bs-toggle="tab">Supressão</a>
                            </li>
                            <li class="nav-item">
                                <a href="#pilhas" @click="tab = 'pilhas'" :class="{active: tab === 'pilhas'}" class="nav-link"
                                   data-bs-toggle="tab">Pilhas</a>
                            </li>
                            <li class="nav-item">
                                <a href="#destinacao" @click="tab = 'destinacao'" :class="{active: tab === 'destinacao'}" class="nav-link" data-bs-toggle="tab">Destinação</a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <div :class="[tab === 'supressao' ? 'active show' : '']" class="tab-pane" id="supressao">
                                <div v-if="analiseSupressao" class="row row-gap-2">
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
                                            Percentual suprimido em relação ao total previsto nas ASVs:
                                            {{ analiseSupressao.percentualSuprimido }}%
                                        </h4>
                                    </div>
                                    <div class="col-12">
                                        <hr class="mb-4"/>
                                    </div>
                                    <div class="col-12">
                                        <h4>Gráfico de área total suprimida (área APP e fora APP) por mês</h4>
                                        <BarChart
                                            :style="{ height: '80px' }"
                                            :chart_data="{
                                                labels: analiseSupressao.areaMes.map((item) => item.mes),
                                                datasets: [{
                                                    label: 'Área total (ha)',
                                                    backgroundColor: '#008ffb',
                                                    data: analiseSupressao.areaMes.map((item) => parseFloat(item.area_total))
                                                }]
                                            }"
                                            :chart_options="{ responsive: true }"
                                        />
                                    </div>
                                    <div class="col-12">
                                        <hr class="mb-4"/>
                                    </div>
                                    <div class="col-12">
                                        <label for="">Análise da Supressão de Vegetação:</label>
                                        <textarea v-model="form.analise_supressao_vegetacao" class="form-control"
                                                  rows="3"></textarea>
                                    </div>
                                    <div class="col-12">
                                        <hr class="mb-4">
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
                                        <textarea v-model="form.analise_supressao_especies_protegida"
                                                  class="form-control" rows="3"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div :class="[tab === 'pilhas' ? 'active show' : '']" class="tab-pane" id="pilhas">
                                <div v-if="analisePilha" class="row row-gap-2">
                                    <div class="col-12">
                                        <h4>Tabela com as pilhas cadastradas</h4>
                                        <Table
                                            :columns="['Código', 'Área de supressão', 'Data cadastro', 'Nº ASV', 'Tipo de pilha', 'Tipo de produto', 'Nome científico', 'Volume (m³)']"
                                            :records="{ data: analisePilha.pilha, links: [] }"
                                            table-class="table-hover">
                                            <template #body="{ item, key }">
                                                <tr>
                                                    <td>{{ item.chave }}</td>
                                                    <td>{{ item.area_supressao }}</td>
                                                    <td>{{ dateTimeFormat(item.created_at) }}</td>
                                                    <td>{{ item.licenca_id }}</td>
                                                    <td>{{ item.tipo_pilha_label }}</td>
                                                    <td>{{ item.produto }}</td>
                                                    <td>{{ item.corte ?? '-' }}</td>
                                                    <td>{{ item.volume ?? '-' }}</td>
                                                </tr>
                                            </template>
                                        </Table>
                                    </div>
                                    <div class="col-12">
                                        <hr class="mb-4"/>
                                    </div>
                                    <div class="col-12">
                                        <h4>Tabela com a somatória do volume das pilhas cadastradas</h4>
                                        <Table
                                            :columns="['Descrição', 'Vol. Total Matéria Org. (m³)', 'Vol. Total Toretes de Lenha (m³)', 'Vol. Total Tora comercial. (m³)', 'Vol. Total Madeira. (m³)']"
                                            :records="{ data: analisePilha.resumo, links: [] }"
                                            table-class="table-hover">
                                            <template #body="{ item, key }">
                                                <tr>
                                                    <td>{{ item.desc }}</td>
                                                    <td>{{ item.organico }}</td>
                                                    <td>{{ item.lenha }}</td>
                                                    <td>{{ item.comercial }}</td>
                                                    <td>{{ item.total }}</td>
                                                </tr>
                                            </template>
                                        </Table>
                                    </div>
                                    <div class="col-12">
                                        <hr class="mb-4"/>
                                    </div>
                                    <div class="col-12">
                                        <h4 class="m-0">
                                            Percentual do volume suprimido: {{ analisePilha.volume }}%
                                        </h4>
                                    </div>
                                    <div class="col-12">
                                        <hr class="mb-4"/>
                                    </div>
                                    <div class="col-12">
                                        <label for="">Análise das pilhas cadastradas:</label>
                                        <textarea v-model="form.analise_pilhas_cadastradas" class="form-control"
                                                  rows="3"></textarea>
                                    </div>
                                    <div class="col-12">
                                        <hr class="mb-4">
                                    </div>
                                    <div class="col-12">
                                        <h4>Tabela com a somatória do volume das pilhas cadastradas</h4>
                                        <Table
                                            :columns="['Código', 'Área de supressão', 'Data cadastro', 'Nº ASV', 'Tipo de pilha', 'Tipo de produto', 'Nome científico', 'Volume (m³)']"
                                            :records="{ data: analisePilha.pilhaProtegida, links: [] }"
                                            table-class="table-hover">
                                            <template #body="{ item, key }">
                                                <tr>
                                                    <td>{{ item.chave }}</td>
                                                    <td>{{ item.area_supressao }}</td>
                                                    <td>{{ dateTimeFormat(item.created_at) }}</td>
                                                    <td>{{ item.licenca_id }}</td>
                                                    <td>{{ item.tipo_pilha_label }}</td>
                                                    <td>{{ item.produto }}</td>
                                                    <td>{{ item.corte ?? '-' }}</td>
                                                    <td>{{ item.volume ?? '-' }}</td>
                                                </tr>
                                            </template>
                                        </Table>
                                    </div>
                                    <div class="col-12">
                                        <hr class="mb-4"/>
                                    </div>
                                    <div class="col-12">
                                        <label for="">Análise das pilhas de espécies protegidas/amaçadas</label>
                                        <textarea v-model="form.analise_pilhas_especie_protetigas" class="form-control"
                                                  rows="3"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div :class="[tab === 'destinacao' ? 'active show' : '']" class="tab-pane" id="destinacao">
                                <div v-if="analiseDestinacao" class="row row-gap-2">
                                    <div class="col-12">
                                        <h4>Tabela com as pilhas cadastradas</h4>
                                        <Table
                                            :columns="['Código', 'Data do envio', 'Pilhas', 'Destinatário', 'Uso da madeira', 'Volume (m³)', 'Observação']"
                                            :records="{ data: analiseDestinacao.destinacao, links: [] }"
                                            table-class="table-hover">
                                            <template #body="{ item, key }">
                                                <tr>
                                                    <td>{{ item.chave }}</td>
                                                    <td>{{ dateTimeFormat(item.dt_envio) }}</td>
                                                    <td>{{ item.pilhas }}</td>
                                                    <td>{{ item.destinatario }}</td>
                                                    <td>{{ item.uso_da_madeira }}</td>
                                                    <td>{{ item.volume }}</td>
                                                    <td>{{ item.observacao ?? '-' }}</td>
                                                </tr>
                                            </template>
                                        </Table>
                                    </div>
                                    <div class="col-12">
                                        <hr class="mb-4"/>
                                    </div>
                                    <div class="col-12">
                                        <h4>
                                            Volume total de madeira destinada:
                                            {{ analiseDestinacao.volumeDestinado }}(m³)
                                        </h4>
                                    </div>
                                    <div class="col-12">
                                        <h4>
                                            Volume total de madeira Estocada: {{ analiseDestinacao.volumeEstocado }}(m³)
                                        </h4>
                                    </div>
                                    <div class="col-12">
                                        <h4>
                                            Percentual do volume destinado:
                                            {{ analiseDestinacao.percentTotalDestinado }}%
                                        </h4>
                                    </div>
                                    <div class="col-12">
                                        <hr class="mb-3 mt-0"/>
                                    </div>
                                    <div class="col-12">
                                        <label for="">Análise da destinação das pilhas:</label>
                                        <textarea v-model="form.analise_destinacao_pilhas" class="form-control"
                                                  rows="3"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
            <template #footer>
                <button @click="modalRef.getBsModal().hide()" type="button" class="btn btn-secondary">Fechar</button>
                <button type="submit" class="btn btn-success">Salvar</button>
            </template>
        </Modal>
    </form>

</template>
