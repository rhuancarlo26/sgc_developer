<script setup>
import PieChart from '@/Components/PieChart.vue';
import BarChart from '@/Components/BarChart.vue';

import InputLabel from '@/Components/InputLabel.vue';
import Map from '@/Components/Map.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import Navbar from '../Navbar.vue';

const activeTab = ref('resultados');

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
            <h1 class="text-center mb-4">Programa de Afugentamento e Resgate de Fauna</h1>
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
                        <div class="col-4">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="option" id="areas"
                                    @click="activeTab = 'areas'">
                                <label class="form-check-label" for="areas">Áreas de supressão vegetal</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div v-if="activeTab === 'resultados'" class="row mt-3">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="card">
                                <div class="card-header">Abundância</div>
                                <div class="card-body">
                                    <PieChart :chart_data="pieCharts.abundancia" :chart_options="pieChartOptions" />
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="card">
                                <div class="card-header">Diversidade</div>
                                <div class="card-body">
                                    <PieChart :chart_data="pieCharts.diversidade" :chart_options="pieChartOptions" />
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
                                            Total de Registros
                                        </div>
                                        <div class="col mb-3">
                                            Área suprimida
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mb-3">
                                            Registro/Hectare
                                        </div>
                                        <div class="col mb-3">
                                            Taxa de Mortalidade
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">Tipo de Registro</div>
                                <div class="card-body">
                                    <PieChart :chart_data="pieCharts.tipoRegistro" :chart_options="pieChartOptions" />
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">Destinação</div>
                                <div class="card-body">
                                    <PieChart :chart_data="pieCharts.destinacao" :chart_options="pieChartOptions" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div v-if="activeTab === 'registros'" class="row mt-3">
                    <div class="card mb-4">
                        <div class="card-header">
                            IQA (média para o período)
                        </div>
                        <div class="card-body">
                            <BarChart :chart_data="chartData" :chart_options="chartOptions" />
                        </div>
                    </div>
                </div>
                <div v-if="activeTab === 'areas'" class="row mt-3">
                    <div class="col mb-3">
                        <div class="card">
                            <div class="card-body">
                               
                                <div class="row">
                                    <div class="col-12 mb-3">
                                        <strong>Nome:</strong> 
                                    </div>
                                </div>
                               
                                <div class="row">
                                    <div class="col-6 mb-3">
                                        <strong>Latitude:</strong> 
                                    </div>
                                    <div class="col-6 mb-3">
                                        <strong>Longitude:</strong> 
                                    </div>
                                </div>
                              
                                <div class="row">
                                    <div class="col-12 mb-3">
                                        <strong>Área total:</strong> 
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-6 mb-3">
                                        <strong>Licença:</strong> 
                                    </div>
                                    <div class="col-6 mb-3">
                                        <strong>Situação da obra:</strong> 
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-6 mb-3">
                                        <strong>Data início:</strong> 
                                    </div>
                                    <div class="col-6 mb-3">
                                        <strong>Data fim:</strong> 
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-12 mb-3">
                                        <strong>Responsável pela obra:</strong>
                                        
                                    </div>
                                </div>
                              
                                <div class="row">
                                    <div class="col-12">
                                        <strong>Cobertura Vegetal:</strong> 
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </AuthenticatedLayout>
</template>
