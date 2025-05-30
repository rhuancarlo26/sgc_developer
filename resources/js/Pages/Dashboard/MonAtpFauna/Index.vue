<script setup>
import BarChart from '@/Components/BarChart.vue';
import PieChart from '@/Components/PieChart.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Map from '@/Components/MapPontos.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
  at_fauna_execucao_registros: Object,
  chartDataPieAbundancia: Object,
  chartDataPieDiversidade: Object,
  chartDataBar2: Object,
  getChartDataBarCampanhas: Object,
  campanhas: Object,
  especiesGroup: Object,
  contrato: Object
});

const total = ref('total');
const registro = ref('resultado');
const mapaVisualizarTrecho = ref(null);
const selectedCampanha = ref(null);

const filteredEspeciesGroupByCampanha = computed(() => {

  if (!selectedCampanha.value) {
    return props.especiesGroup;
  }

  const filteredGroup = {};

  Object.entries(props.especiesGroup).forEach(([especie, registros]) => {
    const registrosFiltrados = registros.filter(registro => {
      return registro.campanhas && registro.campanhas.id === selectedCampanha.value;
    });

    if (registrosFiltrados.length) {
      filteredGroup[especie] = registrosFiltrados;
    }
  });

  return filteredGroup;
});

const chartDataBarFilteredByCampanha = computed(() => {
  const speciesGroup = filteredEspeciesGroupByCampanha.value;
  const labels = Object.keys(speciesGroup);
  const data = labels.map(especie => speciesGroup[especie].length);

  return {
    labels,
    datasets: [
      {
        label: 'Ocorrências',
        data,
        backgroundColor: "rgba(30, 144, 255, 0.8)",
        borderColor: "rgba(30, 144, 255, 1)",
        borderWidth: 1,
      },
    ],
  };
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
      align: "center",
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

const chartOptionsBar = ref({
  responsive: true,
  plugins: {
    legend: { display: false },
    tooltip: { enabled: true },
    datalabels: {
      anchor: "end",
      align: "top",
      color: "#ffffff",
      backgroundColor: "#666",
      borderRadius: 4,
      padding: 4,
      font: { weight: "bold", size: 12 },
    },
  },
  scales: {
    x: {
      grid: { display: false },
    },
    y: {
      beginAtZero: true,
      grid: { drawBorder: false },
    },
  },
  barPercentage: 0.5,
  maxBarThickness: 40,
});


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

const trechosVisualizacao = computed(() => {
  let geojson = [];

  props.at_fauna_execucao_registros.forEach(at_fauna_execucao_registro => {
    const longitude = Number(at_fauna_execucao_registro.longitude);
    const latitude = Number(at_fauna_execucao_registro.latitude);

    geojson.push([
      JSON.stringify({
        type: "Feature",
        geometry: {
          type: "Point",
          coordinates: [longitude, latitude]
        }
      }),
      modalPontoRegistro(at_fauna_execucao_registro),
      at_fauna_execucao_registro
    ]);
  });

  return geojson;
});

function getClasseLabel(id) {
  switch (String(id)) {
    case '1': return 'Aves';
    case '2': return 'Mamíferos';
    case '3': return 'Répteis';
    case '4': return 'Anfíbios';
    default: return id;
  }
}

const modalPontoRegistro = (r) => {
  console.log(r)
  const fmtDate = d => d ? new Date(d).toLocaleDateString('pt-BR') : 'N/A'
  const fmtNum = (v, dp = 2) => isNaN(parseFloat(v)) ? 'N/A' : parseFloat(v).toFixed(dp)
  const mk = v => (v != null && v !== '') ? v : 'N/A'

  return `
  <div class="popup-record">
    <h3 class="popup-title">Registro: ${mk(r.nome_registro)}</h3>

    <section class="popup-section">
      <ul>
        <li><strong>Grupo Amostrado:</strong> ${mk(r.grupo_faunistico.nome)}</li>
        <li><strong>Campanha:</strong> ${mk(r.fk_campanha)}</li>
        <li><strong>Estado:</strong> ${mk(r.estado.nome)}</li>
        <li><strong>Data:</strong> ${fmtDate(r.data_registro)}</li>
      </ul>
    </section>
    <section class="popup-section">
      <ul>
        <li><strong>KM:</strong> ${fmtNum(r.km)}</li>
        <li><strong>Latitude:</strong> ${fmtNum(r.latitude, 5)}</li>
        <li><strong>Longitude:</strong> ${fmtNum(r.longitude, 5)}</li>
        <li><strong>Zona:</strong> ${mk(r.zona)}</li>
        <li><strong>Sentido:</strong> ${mk(r.sentido)}</li>
        <li><strong>Margem:</strong> ${mk(r.margem)}</li>
      </ul>
    </section>
    <section class="popup-section">
      <ul>
        <li><strong>Classe:</strong> ${getClasseLabel(r.classe)}</li>
        <li><strong>Família:</strong> ${mk(r.familia)}</li>
        <li><strong>Gênero:</strong> ${mk(r.genero)}</li>
        <li><strong>Espécie:</strong> ${mk(r.especie)}</li>
        <li><strong>Nome Comum:</strong> ${mk(r.nome_comum)}</li>
      </ul>
    </section>

    <section class="popup-section">
      <ul>
        <li><strong>Redução Biológica:</strong> ${mk(r.reducao_biologica)}</li>
        <li><strong>Coletado:</strong> ${mk(r.coletado)}</li>
        <li><strong>Faixa Etária:</strong> ${mk(r.faixa_etaria)}</li>
        <li><strong>IUCN:</strong> ${mk(r.iucn)}</li>
        <li><strong>Indivíduos:</strong> ${mk(r.n_individuos)}</li>
      </ul>
    </section>
  </div>`
}

const modalPontoRegistro1 = (registro) => {
  return `
    <span><strong>Dados do Registro</strong></span><br>
    <span><strong>ID: </strong> ${registro.id}</span><br>
    <span><strong>Nome do Registro: </strong> ${registro.nome_registro}</span><br>
    <span><strong>ID do Serviço: </strong> ${registro.fk_servico}</span><br>
    <span><strong>Grupo Amostrado: </strong> ${registro.fk_grupo_amostrado}</span><br>
    <span><strong>Campanha: </strong> ${registro.fk_campanha}</span><br>
    <span><strong>Estado: </strong> ${registro.fk_estado}</span><br>
    <span><strong>Data de Registro: </strong> ${registro.data_registro}</span><br>
    <span><strong>KM: </strong> ${registro.km}</span><br>
    <span><strong>Latitude: </strong> ${registro.latitude}</span><br>
    <span><strong>Longitude: </strong> ${registro.longitude}</span><br>
    <span><strong>Zona: </strong> ${registro.zona ? registro.zona : 'N/A'}</span><br>
    <span><strong>Sentido: </strong> ${registro.sentido}</span><br>
    <span><strong>Margem: </strong> ${registro.margem}</span><br>
    <span><strong>Classe: </strong> ${registro.classe}</span><br>
    <span><strong>Ordem: </strong> ${registro.ordem}</span><br>
    <span><strong>Família: </strong> ${registro.familia}</span><br>
    <span><strong>Gênero: </strong> ${registro.genero}</span><br>
    <span><strong>Espécie: </strong> ${registro.especie}</span><br>
    <span><strong>Nome Comum: </strong> ${registro.nome_comum}</span><br>
    <span><strong>Redução Biológica: </strong> ${registro.reducao_biologica}</span><br>
    <span><strong>Sexo: </strong> ${registro.sexo}</span><br>
    <span><strong>Coletado: </strong> ${registro.coletado}</span><br>
    <span><strong>Faixa Etária: </strong> ${registro.faixa_etaria}</span><br>
    <span><strong>Nº Registro Tombamento: </strong> ${registro.n_registro_tombamento ? registro.n_registro_tombamento : 'N/A'}</span><br>
    <span><strong>Avaliação Estadual: </strong> ${registro.estadual}</span><br>
    <span><strong>Avaliação Federal: </strong> ${registro.federal}</span><br>
    <span><strong>IUCN: </strong> ${registro.iucn}</span><br>
    <span><strong>Nº de Indivíduos: </strong> ${registro.n_individuos}</span><br>
    <span><strong>Carcaça Removida: </strong> ${registro.carcaca_removida}</span><br>
    <span><strong>Hora de Registro: </strong> ${registro.hora_registro}</span><br>
  `;
};

setTimeout(() => {
  mapaVisualizarTrecho.value.renderMapa()
  mapaVisualizarTrecho.value.setLinestrings(trechosVisualizacao.value, true);
}, 500);

</script>

<template>

  <Head title="Dashboard" />
  <AuthenticatedLayout>
    <section aria-label="Cabeçalho" class="card mb-4 p-4 shadow-sm section-header">
      <h1 class="h4 fw-bold mb-2 section-title">Programa de Monitoramento do Atropelamento de Fauna</h1>
      <div class="header-info d-flex justify-content-center gap-4 flex-wrap">
        <p class="info-item"><strong>Contratada:</strong> {{ contrato.contratada }}</p>
        <p class="info-item"><strong>Contrato:</strong> {{ contrato.numero_contrato }}</p>
        <p class="info-item"><strong>UFs/BRs:</strong> {{ contrato.ufs }} / {{ contrato.brs }}</p>
      </div>
    </section>
    <div class="row mb-4 ">
      <div class="col-6">
        <div class="card">
          <Map ref="mapaVisualizarTrecho" height="75vh" :manual-render="true" />
        </div>
        <div class="col-12 ">
          <div class="card mt-4">
            <div class="header-info d-flex justify-content-center gap-4 flex-wrap">
              <p class="info-item mt-4"><strong>Registros</strong></p>
            </div>
            <aside class="p-3 shadow-sm w-100" style="width: 300px; max-height: 80vh;">
              <div class="btn-group w-100 mb-4" role="group" aria-label="Modo de exibição">
                <button type="button" class="btn" :class="total === 'total' ? 'btn-primary' : 'btn-outline-secondary'"
                  @click="total = 'total'">Registros totais</button>
                <button type="button" class="btn"
                  :class="total === 'campanha' ? 'btn-primary' : 'btn-outline-secondary'"
                  @click="total = 'campanha'">Registros por campanha</button>
              </div>
            </aside>
            <div v-if="total === 'total'" class="row">
              <BarChart :chart_data="props.chartDataBar2" :chart_options="chartOptionsBar2" />
            </div>
            <div v-if="total === 'campanha'" class="row">
              <div class="col-11 m-4">
                <InputLabel value="Selecione uma campanha" />
                <select v-model.number="selectedCampanha" class="form-select">
                  <option disabled value="">Selecione</option>
                  <option v-for="campanha in props.campanhas || []" :key="campanha?.id" :value="campanha?.id">
                    {{ campanha?.id }}
                  </option>
                </select>
              </div>
              <div v-if="selectedCampanha" class="row">
                <BarChart :chart_data="chartDataBarFilteredByCampanha" :chart_options="chartOptionsBar2" />
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-6">
        <div class="card">
          <div class="header-info d-flex justify-content-center gap-4 flex-wrap">
            <p class="info-item mt-4"><strong>Resultado</strong></p>
          </div>
          <div class="row">
            <div class="col-6 ">
              <div class="card-body">
                <h3 class="card-title text-center">Abundância</h3>
                <div class="justify-content-center" style="height:auto;">
                  <PieChart :chart_data="props.chartDataPieAbundancia" :chart_options="chartOptionsPie" />
                </div>
              </div>
            </div>
            <div class="col-6 mb-3">
              <div class="card-body">
                <h3 class="card-title text-center">Riqueza</h3>
                <div class="d-flex justify-content-center align-items-center">
                  <PieChart :chart_data="props.chartDataPieDiversidade" :chart_options="chartOptionsPie" />
                </div>
              </div>
            </div>
            <div class="col-12 mb-3">
              <div class="card-body">
                <h3 class="card-title text-center">Registro por campanha</h3>
                <div class="d-flex justify-content-center align-items-center">
                  <BarChart :chart_data="getChartDataBarCampanhas" :chart_options="chartOptionsBar" />
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