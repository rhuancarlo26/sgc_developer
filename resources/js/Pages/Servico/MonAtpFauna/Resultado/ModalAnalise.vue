<script setup>
import { onMounted, ref, watch } from "vue";
import Modal from "@/Components/Modal.vue";
import { dateTimeFormat } from "@/Utils/DateTimeUtils.js";
import Table from "@/Components/Table.vue";
import axios from "axios";
import { Link, useForm } from "@inertiajs/vue3";
import BarChart from "@/Components/BarChart.vue";
import PieChart from "@/Components/PieChart.vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import { IconTrash } from "@tabler/icons-vue";
import LinkConfirmation from "@/Components/LinkConfirmation.vue";
import { computed } from "vue";

defineProps({
    showActionsModal: { type: Boolean }
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
    const { data } = await axios.get(route('contratos.contratada.servicos.mon_atp_fauna.resultado.analise', item.id))
    analise.value = data;
    data.analise ? Object.assign(form, data.analise) : Object.assign(form, { fk_resultado: item.id });
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

const numTotalIndividuos = computed(() => {
    return analise.value.atropelados_classe.reduce((acumulador, item) => acumulador + item.n_individuos, 0)
})

const numTotalespecies = computed(() => {
    return analise.value.atropelados_especie.reduce((acumulador, item) => acumulador + item.n_especies, 0)
})

onMounted(() => {
    modalRef.value.$el.addEventListener('hidden.bs.modal', () => {
        form.reset()
        tab.value = null;
        analise.value = null;
        resultado.value = null;
    });
});

defineExpose({ abrirModal });
</script>

<template>
    <form @submit.prevent="save">
        <Modal ref="modalRef" title="Análise dos resultados programa de atropelamento de fauna"
            modal-dialog-class="modal-xl">
            <template #body>
                <div class="card">
                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs">
                            <li class="nav-item">
                                <a href="#abundancia-classe" @click="tab = 'abundancia-classe'"
                                    :class="{ active: tab === 'abundancia-classe' }" class="nav-link"
                                    data-bs-toggle="tab">Abundância de animais atropelados por classe</a>
                            </li>
                            <li class="nav-item">
                                <a href="#animais-atropelados-especie" @click="tab = 'animais-atropelados-especie'"
                                    :class="{ active: tab === 'animais-atropelados-especie' }" class="nav-link"
                                    data-bs-toggle="tab">Diversidade de animais atropelados por classe</a>
                            </li>
                            <li class="nav-item">
                                <a href="#outras-analises" @click="tab = 'outras-analises'"
                                    :class="{ active: tab === 'outras-analises' }" class="nav-link"
                                    data-bs-toggle="tab">Outras análises</a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <div :class="[tab === 'abundancia-classe' ? 'active show' : '']" class="tab-pane"
                                id="abundancia-classe">
                                <div v-if="analise" class="row row-gap-2 justify-content-center">
                                    <div class="col-4">
                                        <PieChart :chart_data="{
                                            labels: analise.atropelados_classe?.map((item) => item.nome),
                                            datasets: [{
                                                label: 'Abundância por classe',
                                                backgroundColor: ['#41B883', '#E46651', '#00D8FF', '#DD1B16'],
                                                data: analise.atropelados_classe?.map((item) => parseInt((item.n_individuos / numTotalIndividuos) * 100))
                                            }]
                                        }" :chart_options="{
                                            responsive: true,
                                            plugins: {
                                                tooltip: {
                                                    enabled: false
                                                },
                                                datalabels: {
                                                    display: true,
                                                    color: 'white',
                                                    align: 'top',
                                                    font: {
                                                        weight: 'bold',
                                                    },
                                                    formatter: (value, context) => {
                                                        const label = context.chart.data.labels[context.dataIndex];
                                                        return `${label}: ${value}%`;
                                                    }
                                                }
                                            }
                                        }" />
                                    </div>
                                    <div class="col-12">
                                        <InputLabel>Análise:</InputLabel>
                                        <textarea v-model="form.analise_animais_atropelados_campanha"
                                            class="form-control" rows="3"></textarea>
                                    </div>
                                </div>
                            </div>

                            <div :class="[tab === 'animais-atropelados-especie' ? 'active show' : '']" class="tab-pane"
                                id="animais-atropelados-especie">
                                <div v-if="analise" class="row row-gap-2 justify-content-center">
                                    <div class="col-4">

                                        <PieChart :chart_data="{
                                            labels: analise.atropelados_especie?.map((item) => item.nome),
                                            datasets: [{
                                                label: 'Diversidade por classe',
                                                backgroundColor: ['#41B883', '#E46651', '#00D8FF', '#DD1B16'],
                                                data: analise.atropelados_especie?.map((item) => parseInt((item.n_especies / numTotalespecies) * 100))
                                            }]
                                        }" :chart_options="{
                                            responsive: true,
                                            plugins: {
                                                tooltip: {
                                                    enabled: false
                                                },
                                                datalabels: {
                                                    display: true,
                                                    color: 'white',
                                                    align: 'top',
                                                    font: {
                                                        weight: 'bold',
                                                    },
                                                    formatter: (value, context) => {
                                                        const label = context.chart.data.labels[context.dataIndex];
                                                        return `${label}: ${value}%`;
                                                    }
                                                }
                                            }
                                        }" />
                                    </div>
                                    <div class="col-12">
                                        <InputLabel>Análise:</InputLabel>
                                        <textarea v-model="form.analise_animais_atropelados_especie"
                                            class="form-control" rows="3"></textarea>
                                    </div>
                                </div>
                            </div>


                            <div :class="[tab === 'outras-analises' ? 'active show' : '']" class="tab-pane"
                                id="outras-analises">
                                <div v-if="analise" class="row row-gap-2">
                                    <div class="col-lg-12">
                                        <InputLabel value="Nome da Análise" />
                                        <input type="text" class="form-control">
                                    </div>
                                    <div class="col-lg-6">
                                        <InputLabel value="Buscar arquivo" for="arquivo" />
                                        <input @input="form.shapefile = $event.target.files[0]" id="arquivo" type="file"
                                            class="form-control">
                                        <InputError :message="form.errors.shapefile" />
                                    </div>
                                    <div class="col-12">
                                        <InputLabel>Análise dos resultados::</InputLabel>
                                        <textarea v-model="form.analise" class="form-control" rows="3"></textarea>
                                    </div>
                                    <div class="col-12">
                                        <button class="btn btn-success" type="button">
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
                                                    <td>{{ item.analise }}</td>
                                                    <td>
                                                        <a href="javascript:void(0);">
                                                            {{ item.nome_arquivo }}
                                                        </a>
                                                    </td>
                                                    <td>
                                                        <span v-if="!item.caminho_arquivo">-</span>
                                                        <span
                                                            v-else-if="item.extensao === 'jpg' || item.extensao === 'png'">

                                                        </span>
                                                        <span v-else>

                                                        </span>
                                                    </td>
                                                    <td>

                                                    </td>
                                                </tr>
                                            </template>
                                            <template #footer>
                                                <th colspan="6">Número Total de Indivíduos:
                                                    <b>{{ analise.total_individuos }}</b>
                                                </th>
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
