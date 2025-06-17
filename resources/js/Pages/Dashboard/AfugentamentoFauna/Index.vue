<script setup>
import PieChart from '@/Components/PieChart.vue';
import BarChart from '@/Components/BarChart.vue';

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
	contrato: Object
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
		<section aria-label="Cabeçalho" class="card mb-4 p-4 shadow-sm section-header">
			<h1 class="h4 fw-bold mb-2 section-title">Programa de Afugentamento e Resgate de Fauna</h1>
			<div class="header-info d-flex justify-content-center gap-4 flex-wrap">
				<p class="info-item"><strong>Contratada:</strong> {{ contrato.contratada }}</p>
				<p class="info-item"><strong>Contrato:</strong> {{ contrato.numero_contrato }}</p>
				<p class="info-item"><strong>UFs/BRs:</strong> {{ contrato.ufs }} / {{ contrato.brs }}</p>
			</div>
		</section>
		<div class="row">
			<div class="col-7 mb-4">
				<div class="card card-body">
					<Map ref="mapaVisualizarTrecho" height="100vh" :manual-render="true" />
				</div>
			</div>
			<div class="col-5">
				<aside class="p-3 shadow-sm w-100  mb-4" style="width: 300px; max-height: 80vh;">
					<div class="btn-group w-100 mb-4" role="group" aria-label="Modo de exibição">
						<button type="button" class="btn"
							:class="activeTab === 'resultados' ? 'btn-primary' : 'btn-outline-secondary'"
							@click="activeTab = 'resultados'">Resultados</button>
						<button type="button" class="btn"
							:class="activeTab === 'registros' ? 'btn-primary' : 'btn-outline-secondary'"
							@click="activeTab = 'registros'">Registros</button>
					</div>
				</aside>

				<div v-if="activeTab === 'resultados'" class="row g-3">
					<div class="col-md-6">
						<div class="card h-100 shadow-sm">
							<div class="card-body d-flex flex-column justify-content-center align-items-center">
								<h5 class="card-title text-center mb-3">Abundância</h5>
								<div style="height: auto; width: 100%;">
									<PieChart :chart_data="props.chartDataPieAbundancia" :chart_options="chartOptionsPie" />
								</div>
							</div>
						</div>
					</div>

					<div class="col-md-6">
						<div class="card h-100 shadow-sm">
							<div class="card-body d-flex flex-column justify-content-center align-items-center">
								<h5 class="card-title text-center mb-3">Riqueza</h5>
								<div style="height: auto; width: 100%;">
									<PieChart :chart_data="props.chartDataPieDiversidade" :chart_options="chartOptionsPie" />
								</div>
							</div>
						</div>
					</div>

					<div class="col-12">
						<div class="card shadow-sm">
							<div class="card-body">
								<div class="row fw-bold text-center">
									<!-- Coluna para Total de Registros -->
									<div class="col-md-6  py-2">
										<h3>Total de Registros</h3>
										<ul class="list-group">
											<li class="list-group-item d-flex justify-content-between align-items-center">
												Total
												<span class="badge rounded-pill">{{ totalRegistros }}</span>
											</li>
										</ul>
									</div>

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

					<div class="col-md-6">
						<div class="card shadow-sm">
							<div class="card-body d-flex flex-column justify-content-center align-items-center">
								<h5 class="card-title text-center mb-3">Tipo de Registro</h5>
								<div style="height: auto; width: 100%;">
									<PieChart :chart_data="props.getChartDataPieTipoRegistro" :chart_options="chartOptionsPie" />
								</div>
							</div>
						</div>
					</div>

					<div class="col-md-6">
						<div class="card h-100 shadow-sm">
							<div class="card-body d-flex flex-column justify-content-center align-items-center">
								<h5 class="card-title text-center mb-3">Forma de registro</h5>
								<div style="height: auto; width: 100%;">
									<PieChart :chart_data="props.getChartDataPieFormaRegistroGrafico" :chart_options="chartOptionsPie" />
								</div>
							</div>
						</div>
					</div>
				</div>

				<div v-if="activeTab === 'registros'" class="row g-3">
					<div class="col-12">
						<div class="card mb-4">
							<div class="card-body">
								<BarChart :chart_data="props.chartDataBar2" :chart_options="chartOptionsBar2" />
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


.leaflet-popup-custom .leaflet-popup-content-wrapper {
	width: 450px !important;
	max-width: 90vw !important;
	max-height: 55vh !important;
	overflow-y: auto;
	padding: 1rem;
	background: rgba(255, 255, 255, 0.98);
	border-radius: 8px;
	box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

/* seta */
.leaflet-popup-custom .leaflet-popup-tip {
	background: rgba(255, 255, 255, 0.98);
}

/* título */
.popup-record .popup-title {
	margin: 0 0 0.5rem;
	font-size: 1.2rem;
	color: #0054a6;
	font-weight: 600;
}

/* seções */
.popup-record .popup-section {
	margin-bottom: 0.75rem;
}

.popup-record .popup-section h4 {
	margin: 0 0 0.25rem;
	font-size: 1rem;
	color: #004080;
	border-bottom: 1px solid #e0e0e0;
	padding-bottom: 2px;
}

/* listas sem marcadores */
.popup-record .popup-section ul {
	list-style: none;
	padding: 0;
	margin: 0;
}

.popup-record .popup-section ul li {
	margin: 0.25rem 0;
	font-size: 0.875rem;
	color: #333;
}

.popup-record .popup-section ul li strong {
	color: #0054a6;
}
</style>