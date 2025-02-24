<script setup>
import PieChart from '@/Components/PieChart.vue';
import BarChart from '@/Components/BarChart.vue';

import InputLabel from '@/Components/InputLabel.vue';
import Map from '@/Components/Map.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import Navbar from '../Navbar.vue';
import ModalVideo from '../ModalVideo.vue';
import NavButton from '@/Components/NavButton.vue';

const modalVideoRef = ref({});

const activeTab = ref('resultados');
const filtroRoa = ref(false);

const abrirModalVideo = () => {
    modalVideoRef.value.abrirModal()
}

const chartData = ref({
    labels: [
        "Canis lupus",
        "Felis catus",
        "Equus caballus",
        "Bos taurus",
        "Sus scrofa",
        "Capra aegagrus",
        "Ovis aries",
        "Gallus gallus",
        "Anas platyrhynchos",
        "Oryctolagus cuniculus"
    ],
    datasets: [
        {
            label: "Valores",
            data: [152, 81, 78, 75, 50, 44, 34, 23, 18, 2],
            backgroundColor: "#007bff",
            borderRadius: 5,
        },
    ],
});

const chartOptions = ref({

    responsive: true,
    plugins: {
        legend: { display: false },
        tooltip: { enabled: true },
    },
    scales: {
        x: {
            grid: { display: false },
            ticks: {

                minRotation: 90,
                maxRotation: 90,
            },
        },
        y: {
            beginAtZero: true,
            grid: { drawBorder: false },
        },
    },
});

const pieCharts = ref({
    abundancia: {
        labels: ['Mamíferos', 'Aves', 'Répteis', 'Anfíbios'],
        datasets: [
            {
                label: 'Abundância',
                data: [36.8, 39.5, 18, 5.3],
                backgroundColor: ['#5AA469', '#A3C4BC', '#FFD166', '#EF476F'],
            },
        ],
    },
    diversidade: {
        labels: ['Mamíferos', 'Aves', 'Répteis', 'Anfíbios'],
        datasets: [
            {
                label: 'Diversidade',
                data: [36.8, 39.5, 18, 5.3],
                backgroundColor: ['#5AA469', '#A3C4BC', '#FFD166', '#EF476F'],
            },
        ],
    },
    tipoRegistro: {
        labels: ['Mamíferos', 'Aves', 'Répteis', 'Anfíbios'],
        datasets: [
            {
                label: 'Tipo de Registro',
                data: [36.8, 39.5, 18, 5.3],
                backgroundColor: ['#5AA469', '#A3C4BC', '#FFD166', '#EF476F'],
            },
        ],
    },
    destinacao: {
        labels: ['Mamíferos', 'Aves', 'Répteis', 'Anfíbios'],
        datasets: [
            {
                label: 'Destinação',
                data: [36.8, 39.5, 18, 5.3],
                backgroundColor: ['#5AA469', '#A3C4BC', '#FFD166', '#EF476F'],
            },
        ],
    },
});

const pieChartOptions = ref({
    responsive: true,
    plugins: {
        legend: { position: 'bottom' },
        tooltip: { enabled: true },
    },
});
</script>

<template>

    <Head title="Dashboard Ambiente GEO" />
    <AuthenticatedLayout>
        <Navbar />
        <div class="card card-body mb-4">
            <div class="text-end">
                <NavButton @click="abrirModalVideo()" :icon="IconPlayerPlay" title="Abrir Video"
                    type-button="success" />
            </div>
            <h1 class="text-center mb-4">Programa de Supressao Vegetal</h1>
            <div class="row">
                <div class="col-md-4">
                    <InputLabel value="UF" />
                    <select class="form-select">
                        <option value="">Selecione</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <InputLabel value="BR" />
                    <select class="form-select">
                        <option value="">Selecione</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <InputLabel value="Período" />
                    <select class="form-select">
                        <option value="">Selecione</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-8 mb-4">
                <div class="card card-body">
                    <Map :height="'500px'" />
                </div>


                <div v-if="filtroRoa" class="card card-body mt-4">
                    <h5 class="mb-3">PILHAS E DESTINAÇÃO</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Latitude:</strong> [Latitude]</p>
                            <p><strong>Longitude:</strong> [Longitude]</p>
                            <p><strong>Volume (m³):</strong> [Volume]</p>
                            <p><strong>Pátio de Estocagem:</strong> [Pátio de Estocagem]</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Licença:</strong> [Licença]</p>
                            <p><strong>Destinação:</strong> [Destinação]</p>
                            <p><strong>Observação:</strong> [Observação]</p>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <h6>DESTINAÇÃO:</h6>
                        <div class="row mt-3">

                            <div class="col-md-6 mb-3">
                                <div class="card">

                                    <div class="card-body">
                                        <PieChart :chart_data="pieCharts.destinacao" :chart_options="pieChartOptions" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="card">

                                    <div class="card-body">
                                        <BarChart :chart_data="chartData" :chart_options="chartOptions" />

                                    </div>
                                </div>
                            </div>

                        </div>

                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card card-body mb-4">
                    <div class="row text-center">
                        <div class="col-4">
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
                                <label class="form-check-label" for="registros">Áreas de supressão vegetal</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div v-if="activeTab === 'resultados'" class="row mt-3">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="card">
                                <div class="card-header">TOTAL AUTORIZADO POR BIOMA</div>
                                <div class="card-body">
                                    <BarChart :chart_data="chartData" :chart_options="chartOptions" />

                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="card">
                                <div class="card-header">TOTAL À SUPRIMIR POR BIOMA</div>
                                <div class="card-body">
                                    <BarChart :chart_data="chartData" :chart_options="chartOptions" />

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col mb-3">
                                            TOTAL DE ÁREAS:
                                        </div>
                                        <div class="col mb-3">
                                            ÁREA SUPRIMIDA:
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mb-3">
                                            ÁREA TOTAL AUTORIZADA:
                                        </div>
                                        <div class="col mb-3">
                                            ÁREA NÃO SUPRIMIDA:
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">TOTAL SUPRIMIDO POR BIOMA</div>
                                <div class="card-body">
                                    <BarChart :chart_data="chartData" :chart_options="chartOptions" />

                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">LOCAL DA SUPRESSÃO</div>
                                <div class="card-body">
                                    <PieChart :chart_data="pieCharts.destinacao" :chart_options="pieChartOptions" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div v-if="activeTab === 'registros'" class="row mt-3">
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <InputLabel value="ÁREA DE SUPRESSÃO" />
                                    <select class="form-select" v-model="filtroSituacao">
                                        <option value="">Selecione</option>
                                        <option v-for="(situacao, index) in situacoes" :key="index" :value="situacao">{{
                                            situacao }}</option>
                                    </select>
                                </div>
                                <div class="col-md-6">

                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="filtroRoa"
                                            v-model="filtroRoa">
                                        <label class="form-check-label" for="filtroRoa">PILHAS E DESTINAÇÃO </label>
                                    </div>

                                </div>
                            </div>
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <td><strong>Nome:</strong></td>
                                        <td colspan="3">[Nome]</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Latitude:</strong></td>
                                        <td>[Latitude]</td>
                                        <td><strong>Longitude:</strong></td>
                                        <td>[Longitude]</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Área total autorizada:</strong></td>
                                        <td colspan="3">[Área total autorizada]</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Licença:</strong></td>
                                        <td colspan="3">[Licença]</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Situação da obra:</strong></td>
                                        <td colspan="3">[Situação da obra]</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Data início:</strong></td>
                                        <td>[Data início]</td>
                                        <td><strong>Data fim:</strong></td>
                                        <td>[Data fim]</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Responsável pela obra:</strong></td>
                                        <td colspan="3">[Responsável pela obra]</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Cobertura Vegetal:</strong></td>
                                        <td colspan="3">[Cobertura Vegetal]</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Total suprimido em APP (ha):</strong></td>
                                        <td colspan="3">xxxx (20%)</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Total suprimido fora de APP (ha):</strong></td>
                                        <td colspan="3">xxxxx (30%)</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Total Plantio Compensatório (ha):</strong></td>
                                        <td colspan="3">[Total Plantio Compensatório]</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Total Reposição Florestal (ha):</strong></td>
                                        <td colspan="3">[Total Reposição Florestal]</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <ModalVideo ref="modalVideoRef"
            url="https://drive.google.com/file/d/1fwXRFhU8RWKrNAEC7jgABI2H77fcwoz9/view?usp=sharing" />

    </AuthenticatedLayout>
</template>
