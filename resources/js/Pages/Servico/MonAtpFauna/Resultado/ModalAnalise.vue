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
import Map from "@/Components/Map.vue";

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

const modalMapa = ref(null);
const mapaTrecho = ref(null);
let coordenadas = ref(null);

const abrirMapa = async () => {
    var coordinates = [
        [-15.602000, -47.654000, 0.0],
        [-15.602053, -47.653684, 0.5],
        [-15.602105, -47.653368, 1.0],
        [-15.602158, -47.653053, 1.5],
        [-15.602211, -47.652737, 2.0],
        [-15.602263, -47.652421, 2.5],
        [-15.602316, -47.652105, 3.0],
        [-15.602368, -47.651789, 3.5],
        [-15.602421, -47.651474, 4.0],
        [-15.602474, -47.651158, 4.5],
        [-15.602526, -47.650842, 5.0],
        [-15.602579, -47.650526, 5.5],
        [-15.602632, -47.650211, 6.0],
        [-15.602684, -47.649895, 6.5],
        [-15.602737, -47.649579, 7.0],
        [-15.602789, -47.649263, 7.5],
        [-15.602842, -47.648947, 8.0],
        [-15.602895, -47.648632, 8.5],
        [-15.602947, -47.648316, 9.0],
        [-15.603000, -47.648000, 10.0]
    ];
    //   if (!coordenadas.length) {
    //     await getGeoJson();
    //   }
    mapaTrecho.value.renderMapa();

    setTimeout(() => {
        mapaTrecho.value.setDensity(coordinates);
    }, 500);
}

// const getGeoJson = async () => {
//     await axios.post(route('contratos.gestao.get_geo_json')).then(r => {
//         coordenadas = r.data;
//     });
// };

// const trechos = computed(() => {
//     let geojson = [];

//     coordenadas.forEach(contrato => {
//         contrato.trechos.forEach(trecho => {
//             geojson.push([trecho.coordenada, modalTechoMap(contrato, trecho), trecho]);
//         });
//     });

//     return geojson;
// })

// const modalTechoMap = (contrato, trecho) => {
//     return `
//   <h3>Dados do Trecho</h3>
//   <span><strong>Empresa: </strong> ${contrato.contratada}</span><br>
//   <span><strong>Contrato: </strong> ${contrato.numero_contrato}</span><br>
//   <span><strong>UF: </strong> ${trecho.uf.uf}</span><br>
//   <span><strong>BR: </strong> ${trecho.rodovia.rodovia}</span><br>
//   <span><strong>Km Inicial: </strong> ${trecho.km_inicial}</span><br>
//   <span><strong>Km Final: </strong> ${trecho.km_final}</span><br>
//   <span><strong>Tipo: </strong> ${trecho.trecho_tipo}</span>
//   `;
// }

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
                                <a href="#animais-atropelados-campanha" @click="tab = 'animais-atropelados-campanha'"
                                    :class="{ active: tab === 'animais-atropelados-campanha' }" class="nav-link"
                                    data-bs-toggle="tab">Taxa de atropelamentos por segmento</a>
                            </li>
                            <li class="nav-item">
                                <a href="#animais-atropelados-tipo-pista"
                                    @click="tab = 'animais-atropelados-tipo-pista'"
                                    :class="{ active: tab === 'animais-atropelados-tipo-pista' }" class="nav-link"
                                    data-bs-toggle="tab">Taxa de atropelamentos por tipo de pista</a>
                            </li>
                            <li class="nav-item">
                                <a href="#animais-atropelados-por-campanha"
                                    @click="tab = 'animais-atropelados-por-campanha'"
                                    :class="{ active: tab === 'animais-atropelados-por-campanha' }" class="nav-link"
                                    data-bs-toggle="tab">Número de animais atropelados por classe por campanha</a>
                            </li>
                            <li class="nav-item">
                                <a href="#mapa" @click="abrirMapa(); tab = 'mapa'" :class="{ active: tab === 'mapa' }"
                                    class="nav-link" data-bs-toggle="tab">Mapa</a>
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

                            <div :class="[tab === 'animais-atropelados-campanha' ? 'active show' : '']" class="tab-pane"
                                id="animais-atropelados-campanha">
                                <div v-if="analise" class="row row-gap-2">
                                    <div class="col-12">
                                        <BarChart :style="{ height: 300 }" :chart_data="{
                                            labels: [...analise.atropelados_campanha.campanha?.map((item) => 'Campanha: ' + item.campanha_id), 'Geral'],
                                            datasets: [{
                                                label: 'Taxa de atropelamentos (indivíduos/km/dia) por segmento',
                                                data: [
                                                    ...analise.atropelados_campanha.campanha?.map((item) => parseFloat(item.total_individuos / item.dif_km / item.total_dias).toFixed(6)),
                                                    parseFloat((analise.atropelados_campanha.campanha.reduce((acumulador, item) => acumulador + item.total_individuos, 0) / analise.atropelados_campanha.campanha.reduce((acumulador, item) => acumulador + item.dif_km, 0) / analise.atropelados_campanha.campanha.reduce((acumulador, item) => acumulador + item.total_dias, 0))).toFixed(6)
                                                ]
                                            }]
                                        }" :chart_options="{
                                            responsive: true,
                                            plugins: {
                                                tooltip: {
                                                    enabled: false
                                                },
                                                datalabels: {
                                                    display: true,
                                                    color: 'black',
                                                    align: 'top',
                                                    font: {
                                                        weight: 'bold',
                                                    },
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

                            <div :class="[tab === 'animais-atropelados-tipo-pista' ? 'active show' : '']"
                                class="tab-pane" id="animais-atropelados-tipo-pista">
                                <div v-if="analise" class="row row-gap-2">
                                    <div class="col-12">
                                        <BarChart :style="{ height: 300 }" :chart_data="{
                                            labels: ['Pavimentado', 'Não pavimentado', 'Geral'],
                                            datasets: [{
                                                label: 'Taxa de atropelamentos (indivíduos/km/dia) por tipo de pista',
                                                data: [parseFloat(analise?.atropelados_tipo_pista.pavimentado / analise?.atropelados_tipo_pista.dif_km / analise?.atropelados_tipo_pista.total_dias).toFixed(4), parseFloat(analise?.atropelados_tipo_pista.nao_pavimentado / analise?.atropelados_tipo_pista.dif_km / analise?.atropelados_tipo_pista.total_dias).toFixed(4), parseFloat((analise?.atropelados_tipo_pista.pavimentado + analise?.atropelados_tipo_pista.nao_pavimentado) / analise?.atropelados_tipo_pista.dif_km / analise?.atropelados_tipo_pista.total_dias).toFixed(4)]
                                            }]
                                        }" :chart_options="{
                                            responsive: true,
                                            plugins: {
                                                tooltip: {
                                                    enabled: false
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

                            <div :class="[tab === 'animais-atropelados-por-campanha' ? 'active show' : '']"
                                class="tab-pane" id="animais-atropelados-por-campanha">
                                <div v-if="analise" class="row row-gap-2">
                                    <div class="col-12">
                                        <BarChart :style="{ height: 300 }" :chart_data="{
                                            labels: [],
                                            datasets: analise.atropelados_campanha[0].datasets
                                        }" :chart_options="{
                                            responsive: true,
                                            plugins: {
                                                tooltip: {
                                                    enabled: false
                                                },
                                                datalabels: {
                                                    display: true,
                                                    color: 'black',
                                                    align: 'top',
                                                    font: {
                                                        weight: 'bold',
                                                    },
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

                            <div :class="[tab === 'mapa' ? 'active show' : '']" class="tab-pane" id="mapa">
                                <div v-if="analise" class="row row-gap-2">
                                    <div class="col-12">
                                        <Map ref="mapaTrecho" height="300px" :manual-render="true" />
                                    </div>
                                    <div class="col-12">
                                        <InputLabel>Análise:</InputLabel>
                                        <textarea v-model="form.analise_animais_atropelados_campanha"
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
                                            <template #body="{ item }">
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
