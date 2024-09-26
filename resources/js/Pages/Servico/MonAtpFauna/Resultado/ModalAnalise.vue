<script setup>
import {onMounted, ref, watch} from "vue";
import Modal from "@/Components/Modal.vue";
import {dateTimeFormat} from "@/Utils/DateTimeUtils.js";
import Table from "@/Components/Table.vue";
import axios from "axios";
import {Link, useForm} from "@inertiajs/vue3";
import BarChart from "@/Components/BarChart.vue";
import PieChart from "@/Components/PieChart.vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import {IconTrash} from "@tabler/icons-vue";
import LinkConfirmation from "@/Components/LinkConfirmation.vue";

defineProps({
    showActionsModal: {type: Boolean}
})

const modalRef = ref();
const resultado = ref(null);

const tab = ref(null);

const form = useForm({
    id: null,
    analise_registros_identificados: null,
    analise_animais_atropelados_campanha: null,
    analise_animais_atropelados_km: null,
    analise_animais_atropelados_mes: null,
    analise_animais_atropelados_especie: null,
})

const analise = ref(null)

const abrirModal = async (item) => {
    Object.assign(form, item)
    tab.value = 'tabela-registros-identificados';
    const {data} = await axios.get(route('contratos.contratada.servicos.mon_atp_fauna.resultado.analise', item.id))
    analise.value = data;
    data.analise ? Object.assign(form, data.analise) : Object.assign(form, {fk_resultado: item.id});
    modalRef.value.getBsModal().show();
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

onMounted(() => {
    modalRef.value.$el.addEventListener('hidden.bs.modal', () => {
        form.reset()
        tab.value = null;
        analise.value = null;
        resultado.value = null;
    });
});

defineExpose({abrirModal});
</script>

<template>
    <form @submit.prevent="save">
        <Modal ref="modalRef" title="Análise dos resultados programa de atropelamento de fauna" modal-dialog-class="modal-xl">
            <template #body>
                <div class="card">
                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs">
                            <li class="nav-item">
                                <a href="#tabela-registros-identificados" @click="tab = 'tabela-registros-identificados'" :class="{active: tab === 'tabela-registros-identificados'}" class="nav-link"
                                   data-bs-toggle="tab">Tabela com os registros identificados</a>
                            </li>
                            <li class="nav-item">
                                <a href="#frequencia-atropelamentos" @click="tab = 'frequencia-atropelamentos'" :class="{active: tab === 'frequencia-atropelamentos'}" class="nav-link"
                                   data-bs-toggle="tab">Frequência de Atropelamentos por Classe</a>
                            </li>
                            <li class="nav-item">
                                <a href="#animais-atropelados-campanha" @click="tab = 'animais-atropelados-campanha'" :class="{active: tab === 'animais-atropelados-campanha'}" class="nav-link"
                                   data-bs-toggle="tab">Número de Animais Atropelados por Campanha</a>
                            </li>
                            <li class="nav-item">
                                <a href="#animais-atropelados-km" @click="tab = 'animais-atropelados-km'" :class="{active: tab === 'animais-atropelados-km'}" class="nav-link"
                                   data-bs-toggle="tab">Número de Animais Atropelados por Km</a>
                            </li>
                            <li class="nav-item">
                                <a href="#animais-atropelados-mes" @click="tab = 'animais-atropelados-mes'" :class="{active: tab === 'animais-atropelados-mes'}" class="nav-link"
                                   data-bs-toggle="tab">Número de Animais Atropelados por Mês</a>
                            </li>
                            <li class="nav-item">
                                <a href="#animais-atropelados-especie" @click="tab = 'animais-atropelados-especie'" :class="{active: tab === 'animais-atropelados-especie'}" class="nav-link"
                                   data-bs-toggle="tab">Número de Animais Atropelados por espécie</a>
                            </li>
                            <li class="nav-item">
                                <a href="#outras-analises" @click="tab = 'outras-analises'" :class="{active: tab === 'outras-analises'}" class="nav-link"
                                   data-bs-toggle="tab">Outras análises</a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <div :class="[tab === 'tabela-registros-identificados' ? 'active show' : '']" class="tab-pane" id="tabela-registros-identificados">
                                <div v-if="analise" class="row row-gap-2">
                                    <div class="col-12">
                                        <Table
                                            :columns="['Espécie', 'Nome comum', 'Nº Indivíduos', 'Frequência (%)', 'IUCN', 'Federal']"
                                            :records="{ data: analise.tabela_registro, links: [] }"
                                            table-class="table-hover">
                                            <template #body="{ item, key }">
                                                <tr>
                                                    <td>{{ item.especie }}</td>
                                                    <td>{{ item.nome_comum }}</td>
                                                    <td>{{ item.n_individuos }}</td>
                                                    <td>{{(item.n_individuos * 100 / analise.total_individuos).toFixed(2)}}%</td>
                                                    <td>{{ item.iucn }}</td>
                                                    <td>{{ item.federal }}</td>
                                                </tr>
                                            </template>
                                            <template #footer>
                                                <th colspan="6">Número Total de Indivíduos: <b>{{analise.total_individuos}}</b></th>
                                            </template>
                                        </Table>
                                    </div>
                                    <div class="col-12">
                                        <InputLabel>Análise:</InputLabel>
                                        <textarea v-model="form.analise_registros_identificados" class="form-control" rows="3"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div :class="[tab === 'frequencia-atropelamentos' ? 'active show' : '']" class="tab-pane" id="frequencia-atropelamentos">
                                <div v-if="analise" class="row row-gap-2 justify-content-center">
                                    <div class="col-4">
                                        <PieChart
                                            :chart_data="{
                                                labels: analise.atropelados_classe?.map((item) => item.nome),
                                                datasets: [{
                                                    label: 'Atropelamentos por classe',
                                                    backgroundColor: ['#41B883', '#E46651', '#00D8FF', '#DD1B16'],
                                                    data: analise.atropelados_classe?.map((item) => parseInt(item.n_individuos))
                                                }]
                                            }"
                                            :chart_options="{ responsive: true }"
                                        />
                                    </div>
                                    <div class="col-12">
                                        <InputLabel>Análise:</InputLabel>
                                        <textarea v-model="form.analise_animais_atropelados_campanha" class="form-control" rows="3"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div :class="[tab === 'animais-atropelados-campanha' ? 'active show' : '']" class="tab-pane" id="animais-atropelados-campanha">
                                <div v-if="analise" class="row row-gap-2">
                                    <div class="col-12">
                                        <BarChart
                                            :style="{ height: 300 }"
                                            :chart_data="{
                                                labels: analise.atropelados_campanha?.map((item) => 'Campanha: ' + item.id),
                                                datasets: [{
                                                    label: 'Atropelamentos por campanha',
                                                    data: analise.atropelados_campanha?.map((item) => parseInt(item.n_individuos))
                                                }]
                                            }"
                                            :chart_options="{ responsive: true }"
                                        />
                                    </div>
                                    <div class="col-12">
                                        <InputLabel>Análise:</InputLabel>
                                        <textarea v-model="form.analise_animais_atropelados_campanha" class="form-control" rows="3"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div :class="[tab === 'animais-atropelados-km' ? 'active show' : '']" class="tab-pane" id="animais-atropelados-km">
                                <div v-if="analise" class="row row-gap-2">
                                    <div class="col-12">
                                        <BarChart
                                            :style="{ height: 300 }"
                                            :chart_data="{
                                                labels: analise.atropelados_km?.map((item) => item.km),
                                                datasets: [{
                                                    label: 'Atropelamentos por KM',
                                                    data: analise.atropelados_km?.map((item) => item.n_individuos)
                                                }]
                                            }"
                                            :chart_options="{ responsive: true }"
                                        />
                                    </div>
                                    <div class="col-12">
                                        <InputLabel>Análise:</InputLabel>
                                        <textarea v-model="form.analise_animais_atropelados_km" class="form-control" rows="3"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div :class="[tab === 'animais-atropelados-mes' ? 'active show' : '']" class="tab-pane" id="animais-atropelados-mes">
                                <div v-if="analise" class="row row-gap-2">
                                    <div class="col-12">
                                        <BarChart
                                            :style="{ height: 300 }"
                                            :chart_data="{
                                                labels: analise.atropelados_mes?.map((item) => item.mes + ' - Campanha: ' + item.campanha),
                                                datasets: [{
                                                    label: 'Atropelamentos por mês',
                                                    data: analise.atropelados_mes?.map((item) => item.n_individuos)
                                                }]
                                            }"
                                            :chart_options="{ responsive: true }"
                                        />
                                    </div>
                                    <div class="col-12">
                                        <InputLabel>Análise:</InputLabel>
                                        <textarea v-model="form.analise_animais_atropelados_mes" class="form-control" rows="3"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div :class="[tab === 'animais-atropelados-especie' ? 'active show' : '']" class="tab-pane" id="animais-atropelados-especie">
                                <div v-if="analise" class="row row-gap-2">
                                    <div class="col-12">
                                        <BarChart
                                            :style="{ height: 300 }"
                                            :chart_data="{
                                                labels: analise.atropelados_especie?.map((item) => item.especie),
                                                datasets: [{
                                                    label: 'Atropelamentos por espécie',
                                                    data: analise.atropelados_especie?.map((item) => item.n_individuos)
                                                }]
                                            }"
                                            :chart_options="{ responsive: true }"
                                        />
                                    </div>
                                    <div class="col-12">
                                        <InputLabel>Análise:</InputLabel>
                                        <textarea v-model="form.analise_animais_atropelados_especie" class="form-control" rows="3"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div :class="[tab === 'outras-analises' ? 'active show' : '']" class="tab-pane" id="outras-analises">
                                <div v-if="analise" class="row row-gap-2">
                                    <div class="col-lg-12">
                                        <InputLabel value="Nome da Análise" />
                                        <input type="text" class="form-control">
                                    </div>
                                    <div class="col-lg-6">
                                        <InputLabel value="Buscar arquivo" for="arquivo"/>
                                        <input @input="form.shapefile = $event.target.files[0]" id="arquivo"
                                               type="file" class="form-control">
                                        <InputError :message="form.errors.shapefile"/>
                                    </div>
                                    <div class="col-12">
                                        <InputLabel>Análise dos resultados::</InputLabel>
                                        <textarea v-model="form.analise" class="form-control" rows="3"></textarea>
                                    </div>
                                    <div class="col-12">
                                        <button class="btn btn-success" @click="" type="button">
                                            Incluir Análise
                                        </button>
                                    </div>
                                    <div class="col-12">
                                        <Table
                                            :columns="['Nome', 'Análise dos resultados', 'Visualizar Arquivo', 'Ação']"
                                            :records="{ data: analise.outras_analises, links: [] }"
                                            table-class="table-hover">
                                            <template #body="{ item, key }">
                                                <tr>
                                                    <td>{{item.analise}}</td>
                                                    <td>
                                                        <a  href="javascript:void(0);">
                                                            {{item.nome_arquivo}}
                                                        </a>
                                                    </td>
                                                    <td>
                                                        <span v-if="!item.caminho_arquivo">-</span>
                                                        <span v-else-if="item.extensao === 'jpg' || item.extensao === 'png'">

                                                        </span>
                                                        <span v-else>

                                                        </span>
                                                    </td>
                                                    <td>

                                                    </td>
                                                </tr>
                                            </template>
                                            <template #footer>
                                                <th colspan="6">Número Total de Indivíduos: <b>{{analise.total_individuos}}</b></th>
                                            </template>
                                        </Table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
            <template #footer>
                <button @click="modalRef.getBsModal().hide()" type="button" class="btn btn-secondary">Fechar</button>
                <button v-if="showActionsModal" type="submit" class="btn btn-success">Salvar</button>
            </template>
        </Modal>
    </form>

</template>
