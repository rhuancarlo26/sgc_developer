<script setup>
import PieChart from '@/Components/PieChart.vue';
import BarChart from '@/Components/BarChart.vue';

import InputLabel from '@/Components/InputLabel.vue';
import Map from '@/Components/MapPontos.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref, computed } from 'vue';


const activeTab = ref('resultados');
const mapaVisualizarTrecho = ref(null);

const props = defineProps({
    monitora_afugentamento_faunas: Object,
    chartDataPieAbundancia: Object,
    chartDataPieDiversidade: Object,
    getChartDataPieTipoRegistro: Object,
    totalRegistros: Object,
    getChartDataPieFormaRegistro: Object,
    getChartDataPieFormaRegistroGrafico: Object,
    chartDataBar2: Object,
});

const trechosVisualizacao = computed(() => {
    let geojson = [];

    props.monitora_afugentamento_faunas.forEach(monitora_afugentamento_fauna => {
        const longitude = Number(monitora_afugentamento_fauna.longitude);
        const latitude = Number(monitora_afugentamento_fauna.latitude);

        geojson.push([
            JSON.stringify({
                type: "Feature",
                geometry: {
                    type: "Point",
                    coordinates: [longitude, latitude]
                }
            }),
            modalPontoRegistro(monitora_afugentamento_fauna),
            monitora_afugentamento_fauna
        ]);
    });

    return geojson;
});

const modalPontoRegistro = (registro) => {
    return `
    <span><strong>Dados do Registro</strong></span><br>
    <span><strong>Nome do Registro: </strong> ${registro.nome_registro}</span><br>
    <span><strong>Data de Registro: </strong> ${registro.data_registro}</span><br>
    <span><strong>Hora de Registro: </strong> ${registro.hora_registro}</span><br>
    <span><strong>Estado: </strong> ${registro.id_estado}</span><br>
    <span><strong>KM: </strong> ${registro.km}</span><br>
    <span><strong>Latitude: </strong> ${registro.latitude}</span><br>
    <span><strong>Longitude: </strong> ${registro.longitude}</span><br>
    <span><strong>Zona: </strong> ${registro.zona ?? 'N/A'}</span><br>
    <span><strong>Sentido: </strong> ${registro.sentido}</span><br>
    <span><strong>Margem: </strong> ${registro.margem}</span><br>
    <span><strong>Classe: </strong> ${registro.classe}</span><br>
    <span><strong>Ordem: </strong> ${registro.ordem}</span><br>
    <span><strong>Família: </strong> ${registro.familia}</span><br>
    <span><strong>Gênero: </strong> ${registro.genero}</span><br>
    <span><strong>Espécie: </strong> ${registro.especie}</span><br>
    <span><strong>Nome Comum: </strong> ${registro.nome_comum}</span><br>
    <span><strong>Sexo: </strong> ${registro.sexo}</span><br>
    <span><strong>Faixa Etária: </strong> ${registro.faixa_etaria}</span><br>
    <span><strong>Nº de Indivíduos: </strong> ${registro.n_individuos}</span><br>
    <span><strong>Latitude da Soltura: </strong> ${registro.latitude_soltura}</span><br>
    <span><strong>Longitude da Soltura: </strong> ${registro.longitude_soltura}</span><br>
    <span><strong>Zona da Soltura: </strong> ${registro.zona_soltura || 'N/A'}</span><br>
    <span><strong>Nome Local: </strong> ${registro.nome_local || 'N/A'}</span><br>
    <span><strong>Coletado: </strong> ${registro.coletado ?? 'N/A'}</span><br>
    <span><strong>Nº Registro Tombamento: </strong> ${registro.n_registro_tombamento ?? 'N/A'}</span><br>
    <span><strong>Status Conservação Federal: </strong> ${registro.id_status_conservacao_federal}</span><br>
    <span><strong>Status Conservação IUCN: </strong> ${registro.id_status_conservacao_iucn}</span><br>
    <span><strong>Observações: </strong> ${registro.obs ?? 'N/A'}</span><br>
  `;
};

const chartOptionsBar2 = ref({
    indexAxis: "y", // Faz o gráfico ser horizontal
    responsive: true,
    plugins: {
        legend: { display: false },
        tooltip: { enabled: true },
        datalabels: {
            anchor: "end",
            align: "right",
            color: "#fff",
            font: { weight: "bold", size: 12 },
        },
    },
    scales: {
        x: {
            beginAtZero: true,
            grid: { drawBorder: false },
        },
        y: {
            grid: { display: false },
        },
    },
});

const chartOptionsPie = ref({
    responsive: true,
    plugins: {
        legend: {
            display: true,
            position: 'bottom',
        },
        tooltip: {
            enabled: true,
        },
        datalabels: {
            anchor: "end",
            align: "end",
            offset: -50,
            formatter: (value, context) => {
                const dataArr = context.chart.data.datasets[0].data;
                const total = dataArr.reduce((sum, val) => sum + val, 0);
                const percentage = ((value / total) * 100).toFixed(2);
                return `${value} (${percentage}%)`;
            },
            color: 'black',
        },
    },
});

setTimeout(() => {
    mapaVisualizarTrecho.value.renderMapa()
    mapaVisualizarTrecho.value.setLinestrings(trechosVisualizacao.value, true);
}, 500);
</script>

<template>

    <Head title="Dashboard Afugentamento Fauna" />
    <AuthenticatedLayout>
        <div class="card card-body mb-4">

            <h1 class="text-center mb-4">Programa de Afugentamento e Resgate de Fauna</h1>

        </div>
        <div class="row">
            <div class="col-lg-8 mb-4">
                <div class="card card-body">
                    <Map ref="mapaVisualizarTrecho" height="900px" :manual-render="true" />
                </div>
            </div>
            <div class="col-lg-4">
                <!-- Seleção de Abas -->
                <div class="card card-body mb-4">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="option" id="resultados"
                                    @click="activeTab = 'resultados'" checked>
                                <label class="form-check-label" for="resultados">Resultados</label>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="option" id="registros"
                                    @click="activeTab = 'registros'">
                                <label class="form-check-label" for="registros">Registros</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Conteúdo da Aba Resultados -->
                <div v-if="activeTab === 'resultados'" class="row g-3">

                    <!-- Gráficos de Pizza -->
                    <div class="col-md-6">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <h5 class="card-title text-center mb-3">Abundância</h5>
                                <div style="height: 255px; width: 255px;">
                                    <PieChart :chart_data="props.chartDataPieAbundancia"
                                        :chart_options="chartOptionsPie" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <h5 class="card-title text-center mb-3">Riqueza</h5>
                                <div style="height: 255px; width: 255px;">
                                    <PieChart :chart_data="props.chartDataPieDiversidade"
                                        :chart_options="chartOptionsPie" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Indicadores -->
                    <div class="col-12">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <div class="row fw-bold text-center">
                                    <!-- Coluna para Total de Registros -->
                                    <div class="col-md-6 border-end py-2">
                                        Total de Registros: <strong>{{ totalRegistros }}</strong>
                                    </div>

                                    <!-- Coluna para Taxa de Mortalidade e lista -->
                                    <div class="col-md-6 py-2">
                                        <h3>Taxa de Mortalidade</h3>
                                        <ul class="list-group">
                                            <li v-for="item in getChartDataPieFormaRegistro" :key="item.id"
                                                class="list-group-item d-flex justify-content-between align-items-center">
                                                {{ item.nome }}
                                                <span class="badge rounded-pill">{{ item.total }}</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <!-- Gráficos Adicionais -->
                    <div class="col-md-6">
                        <div class="card shadow-sm">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <h5 class="card-title text-center mb-3">Tipo de Registro</h5>
                                <div style="height: 255px; width: 255px;">
                                    <PieChart :chart_data="props.getChartDataPieTipoRegistro"
                                        :chart_options="chartOptionsPie" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <h5 class="card-title text-center mb-3">Forma de registro</h5>
                                <div style="height: 255px; width: 255px;">
                                    <PieChart :chart_data="props.getChartDataPieFormaRegistroGrafico"
                                        :chart_options="chartOptionsPie" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div v-if="activeTab === 'registros'" class="row mt-3">
                    <div class="card mb-4">
                        <div class="card-body">
                            <BarChart height="2000px" :chart_data="props.chartDataBar2"
                                :chart_options="chartOptionsBar2" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
