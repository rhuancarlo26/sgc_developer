<script setup>
import PieChart from '@/Components/PieChart.vue';
import BarChart from '@/Components/BarChart.vue';

import InputLabel from '@/Components/InputLabel.vue';
import Map from '@/Components/MapPontos.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import ModalVideo from '../ModalVideo.vue';
import NavButton from '@/Components/NavButton.vue';


const props = defineProps({
    contrato: Object,
    area_supressao: Object,
    getChartDataBarPorBioma: Object,
    getChartDataPieAreas: Object,
    getChartDataBarPorBiomaPlano: Object,
    getChartDataBarPorBiomaDiferenca: Object,
    totalAreas: Number,
    areaSuprimida: Number,
    areaTotalAutorizada: Number,
    areaNaoSuprimida: Number,
    destinacoes: Object,
    filtroRoa: Boolean,
    destinacoes: {
        type: Array,
        default: () => []
    }
});

const mapaVisualizarTrecho = ref(null);
const filtroSituacao = ref(null);
const registro = ref('resultado');
const filtroRoa = ref(true);
const selectedChave = ref('')

const destinacaoSelecionada = computed(() => {
    return props.destinacoes.find(d => d.chave === selectedChave.value) || null
})

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

const trechosVisualizacao = computed(() => {
    let geojson = [];

    props.area_supressao.forEach(area_supressao => {
        const longitude = Number(area_supressao.longitude);
        const latitude = Number(area_supressao.latitude);

        geojson.push([
            JSON.stringify({
                type: "Feature",
                geometry: {
                    type: "Point",
                    coordinates: [longitude, latitude]
                }
            }),
            modalPlanoSupressao(area_supressao),
            area_supressao
        ]);
    });

    return geojson;
});

const modalPlanoSupressao = (registro) => {
    return `
    <span><strong>Chave:</strong> ${registro.chave}</span><br>
    <span><strong>Área em App:</strong> ${registro.area_em_app}</span><br>
    <span><strong>Shapefile em App:</strong> ${registro.shapefile_em_app || 'N/A'}</span><br>
    <span><strong>Área fora App:</strong> ${registro.area_fora_app}</span><br>
    <span><strong>Shapefile fora App:</strong> ${registro.shapefile_fora_app || 'N/A'}</span><br>
    <span><strong>ID do Arquivo:</strong> ${registro.arquivo_id}</span><br>
    <span><strong>ID do Serviço:</strong> ${registro.servico_id}</span><br>
    <span><strong>Criado em:</strong> ${new Date(registro.created_at).toLocaleString()}</span><br>
    <span><strong>Atualizado em:</strong> ${new Date(registro.updated_at).toLocaleString()}</span><br>
    <span><strong>Deletado em:</strong> ${registro.deleted_at ? new Date(registro.deleted_at).toLocaleString() : 'N/A'}</span><br>
    <span><strong>Data Inicial:</strong> ${registro.dt_inicial || 'N/A'}</span><br>
    <span><strong>Data Final:</strong> ${registro.dt_final || 'N/A'}</span><br>
    <span><strong>Local Shape em App:</strong> ${registro.local_shape_em_app || 'N/A'}</span><br>
    <span><strong>Local Shape fora App:</strong> ${registro.local_shape_fora_app || 'N/A'}</span><br>
    `;
};



setTimeout(() => {
    mapaVisualizarTrecho.value.renderMapa()
    mapaVisualizarTrecho.value.setLinestrings(trechosVisualizacao.value, true);
}, 500);
</script>

<template>

    <Head title="Dashboard Supressao Vegetal" />
    <AuthenticatedLayout>
        <section aria-label="Cabeçalho" class="card mb-4 p-4 shadow-sm section-header">
            <h1 class="h4 fw-bold mb-2 section-title">Programa de Supressao Vegetal</h1>
            <div class="header-info d-flex justify-content-center gap-4 flex-wrap">
                <p class="info-item"><strong>Contratada:</strong> {{ contrato.contratada }}</p>
                <p class="info-item"><strong>Contrato:</strong> {{ contrato.numero_contrato }}</p>
                <p class="info-item"><strong>UFs/BRs:</strong> {{ contrato.ufs }} / {{ contrato.brs }}</p>
            </div>
        </section>
        <div>
            <div class="">
                <div class="d-flex ">
                    <div class="col-7 card card-body me-4">
                        <Map ref="mapaVisualizarTrecho" height="700px" :manual-render="true" />

                    </div>

                    <div class="col-5">
                        <aside class="card p-3 shadow-sm w-100" style="width: 300px; max-height: 80vh;">
                            <div class="btn-group w-100 mb-4" role="group" aria-label="Modo de exibição">
                                <button type="button" class="btn"
                                    :class="registro === 'resultado' ? 'btn-primary' : 'btn-outline-secondary'"
                                    @click="registro = 'resultado'">Resultados</button>
                                <button type="button" class="btn"
                                    :class="registro === 'registro' ? 'btn-primary' : 'btn-outline-secondary'"
                                    @click="registro = 'registro'">Registro</button>
                            </div>
                        </aside>
                        <div v-if="registro === 'resultado'">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="card">
                                        <div class="card-header">TOTAL AUTORIZADO POR BIOMA</div>
                                        <div class="card-body">
                                            <BarChart :chart_data="props.getChartDataBarPorBiomaPlano"
                                                :chart_options="chartOptions" />

                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="card">
                                        <div class="card-header">TOTAL À SUPRIMIR POR BIOMA</div>
                                        <div class="card-body">
                                            <BarChart :chart_data="props.getChartDataBarPorBiomaDiferenca"
                                                :chart_options="chartOptions" />

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
                                                    <strong>TOTAL DE ÁREAS:</strong><br>
                                                    {{ totalAreas }}
                                                </div>
                                                <div class="col mb-3">
                                                    <strong>ÁREA SUPRIMIDA:</strong><br>
                                                    {{ areaSuprimida.toLocaleString() }} m²
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col mb-3">
                                                    <strong>ÁREA TOTAL AUTORIZADA:</strong><br>
                                                    {{ areaTotalAutorizada.toLocaleString() }} m²
                                                </div>
                                                <div class="col mb-3">
                                                    <strong>ÁREA NÃO SUPRIMIDA:</strong><br>
                                                    {{ areaNaoSuprimida.toLocaleString() }} m²
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
                                            <BarChart :chart_data="props.getChartDataBarPorBioma"
                                                :chart_options="chartOptions" />

                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">LOCAL DA SUPRESSÃO</div>
                                        <div class="card-body">
                                            <PieChart :chart_data="props.getChartDataPieAreas"
                                                :chart_options="pieChartOptions" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div v-else>
                            <div class="card mb-4">
                                <div class="card-body">
                                    <div class="row mb-4">
                                        <div class="col-md-6">
                                            <InputLabel value="ÁREA DE SUPRESSÃO" />
                                            <select class="form-select" v-model="filtroSituacao">
                                                <option value="">Selecione</option>
                                                <option v-for="(area_supressa, index) in area_supressao" :key="index"
                                                    :value="area_supressa">{{
                                                        area_supressa.chave }}</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">

                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="filtroRoa"
                                                    v-model="filtroRoa">
                                                <label class="form-check-label" for="filtroRoa">PILHAS E DESTINAÇÃO
                                                </label>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="col-12" v-if="filtroSituacao">
                                        <table class="table table-bordered">

                                            <tbody>
                                                <tr>
                                                    <td><strong>Nome:</strong></td>
                                                    <td colspan="3">{{ filtroSituacao.chave }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Latitude:</strong></td>
                                                    <td>{{ filtroSituacao.latitude }}</td>
                                                    <td><strong>Longitude:</strong></td>
                                                    <td>{{ filtroSituacao.longitude }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Data início:</strong></td>
                                                    <td>{{ filtroSituacao.dt_inicial }}</td>
                                                    <td><strong>Data fim:</strong></td>
                                                    <td>{{ filtroSituacao.dt_final }}</td>
                                                </tr>

                                                <tr>
                                                    <td><strong>Cobertura Vegetal:</strong></td>
                                                    <td colspan="3">{{ filtroSituacao.fitofisionomia }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Total suprimido em APP (ha):</strong></td>
                                                    <td colspan="3">
                                                        {{ filtroSituacao.area_em_app }} ( {{ areaSuprimidaAppPct }} %)
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Total suprimido fora de APP (ha):</strong></td>
                                                    <td colspan="3">
                                                        {{ filtroSituacao.area_fora_app }} ( {{ areaSuprimidaForaPct
                                                        }} %)
                                                    </td>
                                                </tr>

                                            </tbody>
                                        </table>
                                    </div>
                                    <div v-if="filtroRoa" class="card card-body mt-4">
                                        <h5 class="mb-3">PILHAS E DESTINAÇÃO</h5>                            
                                        <div class="mb-4">
                                            <label for="destSelect"><strong>Selecione Destinação:</strong></label>
                                            <select id="destSelect" v-model="selectedChave" class="form-select">
                                                <option disabled value="">— Selecione —</option>
                                                <option v-for="dest in destinacoes" :key="dest.id" :value="dest.chave">
                                                    {{ dest.chave }}
                                                </option>
                                            </select>
                                        </div>

                                     
                                        <div v-if="destinacaoSelecionada">
                                        
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <p><strong>Destinação (chave):</strong> {{
                                                        destinacaoSelecionada.chave }}</p>
                                                    <p><strong>Data de Envio:</strong> {{ destinacaoSelecionada.dt_envio
                                                        }}</p>
                                                    <p><strong>Uso da Madeira:</strong> {{
                                                        destinacaoSelecionada.uso_da_madeira }}</p>
                                                </div>
                                                <div class="col-md-6">
                                                    <p><strong>Destinatário:</strong> {{
                                                        destinacaoSelecionada.destinatario }}</p>
                                                    <p><strong>Observação:</strong> {{ destinacaoSelecionada.observacao
                                                        }}</p>
                                                </div>
                                            </div>

                                            <div v-for="pilha in destinacaoSelecionada.pilhas" :key="pilha.id"
                                                class="row mb-4">
                                                
                                                <div class="col-md-6">
                                                    <p><strong>Latitude:</strong> {{ pilha.latitude }}</p>
                                                    <p><strong>Longitude:</strong> {{ pilha.longitude }}</p>
                                                    <p><strong>Volume (m³):</strong> {{ pilha.volume }}</p>
                                                    <p><strong>Pátio de Estocagem:</strong> {{ pilha.patio_estocagem_id
                                                        }}</p>
                                                </div>
                                                <div class="col-md-6">
                                                    <p><strong>Licença:</strong> {{ pilha.licenca_id }}</p>
                                                    <p><strong>Destinação:</strong> {{ destinacaoSelecionada.chave }}
                                                    </p>
                                                    <p><strong>Observação:</strong> {{ pilha.observacao }}</p>
                                                </div>
                                                <hr>
                                            </div>
                                        </div>

                                        
                                        <p v-else class="text-muted">Selecione uma destinação para ver as pilhas.</p>
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


<style>
.section-header {
    background: linear-gradient(135deg, #f0f4ff 0%, #ffffff 100%);
    border-left: 4px solid #0054a6;
    border-radius: 0.5rem;
    padding: 1.5rem 2rem;
    text-align: center;
}

.section-title {
    font-size: 1.75rem;
    color: #0054a6;
    letter-spacing: 0.03rem;
    margin-bottom: 0.5rem;
}

.header-info {
    color: #333;
}

.info-item {
    font-size: 1rem;
}

.info-item strong {
    color: #0054a6;
}

@media (max-width: 768px) {
    .section-header {
        padding: 1rem;
    }

    .section-title {
        font-size: 1.5rem;
    }

    .header-info {
        gap: 1rem !important;
    }
}

/* Container do popup */
.leaflet-popup-custom .leaflet-popup-content-wrapper {
    background: rgba(255, 255, 255, 0.95);
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    padding: 12px 16px;
}

/* A seta abaixo do popup */
.leaflet-popup-custom .leaflet-popup-tip {
    background: rgba(255, 255, 255, 0.95);
}

/* Botão de fechar */
.leaflet-popup-custom .leaflet-popup-close-button {
    color: #555;
    font-size: 16px;
    opacity: 0.7;
}

.leaflet-popup-custom .leaflet-popup-close-button:hover {
    opacity: 1;
}

/* Título do popup */
.popup-point h4 {
    margin: 0 0 8px;
    font-size: 1rem;
    /* color: #0054a6; */
    border-bottom: 1px solid #e0e0e0;
    padding-bottom: 4px;
}

/* Lista de detalhes */
.popup-point dl {
    display: grid;
    grid-template-columns: max-content auto;
    row-gap: 4px;
    column-gap: 8px;
    font-size: 0.875rem;
    color: #333;
}

.popup-point dt {
    font-weight: 600;
    /* color: #0054a6; */
    text-align: right;
}

.popup-point dd {
    margin: 0;
    color: #555;
}
</style>