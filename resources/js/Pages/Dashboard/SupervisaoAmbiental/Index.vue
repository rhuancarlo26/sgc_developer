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
const activeTabGraph = ref('rnc');

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

const situacoes = ref(['Sem Vistoria', 'Vistoriadas', 'Atendidas', 'Não Atendidas', 'Prazo Vencido', 'Prazo em Vigência', 'Leve', 'Moderada', 'Grave', 'Autuação Ambiental']);
const lotes = ref(['Lote A', 'Lote B', 'Lote C']); // Exemplo de lotes

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
            <h1 class="text-center mb-4">Programa de Supervisao Ambiental</h1>
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
                        <div class="col-4">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="option" id="registros"
                                    @click="activeTab = 'registros'">
                                <label class="form-check-label" for="registros">Registros</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div v-if="activeTab === 'resultados'" class="row mt-3">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="card h-100">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="filtroLocal"
                                                v-model="filtroLocal">
                                            <label class="form-check-label" for="filtroLocal">Local de
                                                Ocorrência</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="filtroTipo"
                                                v-model="filtroTipo">
                                            <label class="form-check-label" for="filtroTipo">Tipo de Ocorrência</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <PieChart :chart_data="pieCharts.abundancia" :chart_options="pieChartOptions" />
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="card h-100">
                                <div class="card-header">
                                    Intensidade
                                </div>
                                <div class="card-body">
                                    <PieChart :chart_data="pieCharts.diversidade" :chart_options="pieChartOptions" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                            <div class="card">
                                <div class="card-header">
                                    <div class="d-flex justify-content-center">
                                        <div class="form-check me-3">
                                            <input class="form-check-input" type="radio" name="optionGraph" id="rnc"
                                                @click="activeTabGraph = 'rnc'" checked />
                                            <label class="form-check-label" for="rnc">
                                                RNC por lote de obra
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="optionGraph"
                                                id="historico" @click="activeTabGraph = 'historico'" />
                                            <label class="form-check-label" for="historico">
                                                Histórico
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div v-if="activeTabGraph === 'rnc'">
                                        <BarChart :chart_data="chartData" :chart_options="chartOptions" />
                                    </div>
                                    <div v-if="activeTabGraph === 'historico'">
                                        <BarChart :chart_data="chartData" :chart_options="chartOptions" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div v-if="activeTab === 'registros'" class="row mt-3">
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="row mb-4">
                                <div class="col-md-4">
                                    <InputLabel value="Tipo de Registro" />
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="filtroRoa"
                                            v-model="filtroRoa">
                                        <label class="form-check-label" for="filtroRoa">ROA</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="filtroRnc"
                                            v-model="filtroRnc">
                                        <label class="form-check-label" for="filtroRnc">RNC</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <InputLabel value="Situação" />
                                    <select class="form-select" v-model="filtroSituacao">
                                        <option value="">Selecione</option>
                                        <option v-for="(situacao, index) in situacoes" :key="index" :value="situacao">{{
                                            situacao }}</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <InputLabel value="Lote de Obra" />
                                    <select class="form-select" v-model="filtroLote">
                                        <option value="">Selecione</option>
                                        <option v-for="(lote, index) in lotes" :key="index" :value="lote">{{ lote }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Descrição</th>
                                        <th>Status</th>
                                        <th>Data</th>
                                    </tr>
                                </thead>
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
