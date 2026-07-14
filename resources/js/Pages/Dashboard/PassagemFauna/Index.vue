<script setup>
import BarChart from '@/Components/BarChart.vue';
import LineChart from '@/Components/LineChart.vue';
import PieChart from '@/Components/PieChart.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Map from '@/Components/MapPontos.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref, computed } from 'vue';


const total = ref('total');
const totalMapa = ref('passagemMapa')
const registro = ref('resultado');
const curva = ref('armadilha');
const mapaVisualizarTrecho = ref(null);
const selectedPassagem = ref('');



const props = defineProps({

  chartDataPieAbundancia: Object,
  chartDataPieDiversidade: Object,
  chartDataBar: Object,
  chartDataBar2: Object,
  getChartDataBarEspecie: Object,
  modulos: Object,
  contrato: Object,
  especiesGroup: Object,
  passagem: Object

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

const chartOptionsBar3 = ref({
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

const filteredEspeciesGroup = computed(() => {

  if (!selectedPassagem.value) {
    return props.especiesGroup;
  }
  const filteredGroup = {};
  Object.entries(props.especiesGroup).forEach(([especie, registros]) => {
    const registrosFiltrados = registros.filter(registro => {
      return registro.passagem.id === selectedPassagem.value.id
    });

    if (registrosFiltrados.length) {
      filteredGroup[especie] = registrosFiltrados;
    }
  });

  return filteredGroup;
});

// Propriedade computada para criar a estrutura do gráfico (labels e datasets)
const chartDataBarFiltered = computed(() => {
  const speciesGroup = filteredEspeciesGroup.value;
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

const registrosFiltradosPorPassagem = computed(() => {
  return props.modulos.filter(reg => {
    return reg.passagem.id === selectedPassagem.value.id;
  });
});

const trechosVisualizacao = computed(() => {
  let geojson = [];

  props.modulos.forEach(modulo => {
    const longitude = Number(modulo.longitude);
    const latitude = Number(modulo.latitude);

    geojson.push([
      JSON.stringify({
        type: "Feature",
        geometry: {
          type: "Point",
          coordinates: [longitude, latitude]
        }
      }),
      modalRegistroMap(modulo),
      modulo
    ]);
  });

  return geojson;
});

const modalRegistroMap = (registro) => {
  return `
    <span><strong>Dados do Registro</strong></span><br>
    <span><strong>Forma: </strong> ${registro.forma}</span><br>
    <span><strong>Tipo: </strong> ${registro.tipo}</span><br>
    <span><strong>Nº por Serviço: </strong> ${registro.num_por_servico}</span><br>
    <span><strong>Nome ID: </strong> ${registro.nome_id}</span><br>
    <span><strong>KM: </strong> ${registro.km}</span><br>
    <span><strong>Latitude: </strong> ${registro.latitude}</span><br>
    <span><strong>Longitude: </strong> ${registro.longitude}</span><br>
    <span><strong>Zona: </strong> ${registro.zona}</span><br>
    <span><strong>Classe: </strong> ${registro.classe}</span><br>
    <span><strong>Ordem: </strong> ${registro.ordem}</span><br>
    <span><strong>Família: </strong> ${registro.familia}</span><br>
    <span><strong>Gênero: </strong> ${registro.genero}</span><br>
    <span><strong>Espécie: </strong> ${registro.especie}</span><br>
    <span><strong>Nome Comum: </strong> ${registro.nome_comum}</span><br>
    <span><strong>Sexo: </strong> ${registro.sexo}</span><br>
    <span><strong>Faixa Etária: </strong> ${registro.faixa_etaria}</span><br>
    <span><strong>Nº de Indivíduos: </strong> ${registro.n_individuos}</span><br>
    <span><strong>Data de Registro: </strong> ${registro.data_registro}</span><br>
    <span><strong>Hora de Registro: </strong> ${registro.hora_registro}</span><br>
    <span><strong>Status Conservação Federal: </strong> ${registro.id_status_conservacao_federal}</span><br>
    <span><strong>Status Conservação IUCN: </strong> ${registro.id_status_conservacao_iucn}</span><br>
    <span><strong>Observações: </strong> ${registro.obs && registro.obs !== '-' ? registro.obs : 'Nenhuma'}</span><br>
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
      <h1 class="h4 fw-bold mb-2 section-title">Programa de Monitoramento de Passagem de Fauna</h1>
      <div class="header-info d-flex justify-content-center gap-4 flex-wrap">
        <p class="info-item"><strong>Contratada:</strong> {{ contrato?.contratada }}</p>
        <p class="info-item"><strong>Contrato:</strong> {{ contrato?.numero_contrato }}</p>
        <p class="info-item"><strong>UFs/BRs:</strong> {{ contrato?.ufs }} / {{ contrato?.brs }}</p>
      </div>
    </section>
    <div class="row mb-4 ">
      <div class="col-6">
        <div class="card">
          <Map ref="mapaVisualizarTrecho" height="75vh" :manual-render="true" />
        </div>
        <div v-if="selectedPassagem" class="col-12 ">
          <div class="card mt-4">
            <aside class="p-3 shadow-sm w-100" style="width: 300px; max-height: 80vh;">
              <div class="btn-group w-100 mb-4" role="group" aria-label="Modo de exibição">
                <button type="button" class="btn"
                  :class="totalMapa === 'passagemMapa' ? 'btn-primary' : 'btn-outline-secondary'"
                  @click="totalMapa = 'passagemMapa'">Passagem</button>
                <button type="button" class="btn"
                  :class="totalMapa === 'registro' ? 'btn-primary' : 'btn-outline-secondary'"
                  @click="totalMapa = 'registro'">Registro Vinculado</button>
              </div>
            </aside>
            <div v-if="totalMapa === 'passagemMapa'" class="col-12">
              <table class="table table-sm table-bordered">
                <thead class="table-light">
                  <tr>
                    <th colspan="2" class="text-center">Passagem Selecionada</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <th scope="row">ID</th>
                    <td>{{ selectedPassagem.id }}</td>
                  </tr>
                  <tr>
                    <th scope="row">Nome</th>
                    <td>{{ selectedPassagem.nome_id }}</td>
                  </tr>
                  <tr>
                    <th scope="row">Rodovia</th>
                    <td>{{ selectedPassagem.rodovia }}</td>
                  </tr>
                  <tr>
                    <th scope="row">UF</th>
                    <td>{{ selectedPassagem.uf }}</td>
                  </tr>
                  <tr>
                    <th scope="row">Km</th>
                    <td>{{ selectedPassagem.km }}</td>
                  </tr>
                  <tr>
                    <th scope="row">Tipo Estrutura</th>
                    <td>{{ selectedPassagem.tipo_de_estrutura }}</td>
                  </tr>
                  <tr>
                    <th scope="row">Classificação</th>
                    <td>{{ selectedPassagem.classificacao }}</td>
                  </tr>
                  <tr>
                    <th scope="row">Latitude</th>
                    <td>{{ selectedPassagem.latitude }}</td>
                  </tr>
                  <tr>
                    <th scope="row">Longitude</th>
                    <td>{{ selectedPassagem.longitude }}</td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- Tabela de Registros Vinculados -->
            <div v-if="totalMapa === 'registro'" class="col-12">
              <table class="table table-sm table-striped table-bordered">
                <thead class="table-light">
                  <tr>
                    <th>ID Registro</th>
                    <th>Data</th>
                    <th>Tipo</th>
                    <th>Nome Comum</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="reg in registrosFiltradosPorPassagem" :key="reg.id">
                    <td>{{ reg.id }}</td>
                    <td>{{ reg.data_registro }}</td>
                    <td>{{ reg.tipo }}</td>
                    <td>{{ reg.nome_comum || '—' }}</td>
                  </tr>
                  <tr v-if="!registrosFiltradosPorPassagem.length">
                    <td colspan="4" class="text-center text-muted">Nenhum registro encontrado</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      <div class="col-6">
        <aside class="p-3 shadow-sm w-100" style="max-height: 80vh;">
          <div class="btn-group w-100 mb-4" role="group" aria-label="Modo de exibição">
            <button type="button" class="btn"
              :class="registro === 'resultado' ? 'btn-primary' : 'btn-outline-secondary'"
              @click="registro = 'resultado'">
              Resultados
            </button>
            <button type="button" class="btn" :class="registro === 'registro' ? 'btn-primary' : 'btn-outline-secondary'"
              @click="registro = 'registro'">
              Registro
            </button>
          </div>
        </aside>
        <div v-if="registro === 'resultado'">
          <!-- Seção dos gráficos de pizza -->
          <div class="mb-4">
            <div class="row">
              <div class="col-md-6 mb-3">
                <div class="card h-100">
                  <div class="card-body">
                    <h3 class="card-title text-center">Abundância</h3>
                    <div class="d-flex justify-content-center align-items-center" style="height:255px; width:255px;">
                      <PieChart :chart_data="props.chartDataPieAbundancia" :chart_options="chartOptionsPie" />
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-6 mb-3">
                <div class="card h-100">
                  <div class="card-body">
                    <h3 class="card-title text-center">Riqueza</h3>
                    <div class="d-flex justify-content-center align-items-center" style="height:255px; width:255px;">
                      <PieChart :chart_data="props.chartDataPieDiversidade" :chart_options="chartOptionsPie" />
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="card">
            <aside class="p-3 shadow-sm w-100" style="max-height: 80vh;">
              <div class="btn-group w-100 mb-4" role="group" aria-label="Opções de Curva">
                <button type="button" class="btn"
                  :class="curva === 'armadilha' ? 'btn-primary' : 'btn-outline-secondary'" @click="curva = 'armadilha'">
                  Registro por passagem
                </button>
                <button type="button" class="btn" :class="curva === 'curva' ? 'btn-primary' : 'btn-outline-secondary'"
                  @click="curva = 'curva'">
                  Total de registros (Campanha)
                </button>
              </div>
            </aside>
            <div v-if="curva === 'armadilha'">
              <BarChart :chart_data="props.chartDataBar" :chart_options="chartOptionsBar3" />
            </div>
            <div v-else>
              <BarChart :chart_data="props.chartDataBar2" :chart_options="chartOptionsBar3" />
            </div>
          </div>
        </div>
        <div v-else>
          <div class="card">
            <aside class="p-3 shadow-sm w-100" style="max-height: 80vh;">
              <div class="btn-group w-100 mb-4" role="group" aria-label="Opções de Registro">
                <button type="button" class="btn" :class="total === 'total' ? 'btn-primary' : 'btn-outline-secondary'"
                  @click="total = 'total'">
                  Registros totais
                </button>
                <button type="button" class="btn"
                  :class="total === 'armadilha' ? 'btn-primary' : 'btn-outline-secondary'" @click="total = 'armadilha'">
                  Registros por passagem
                </button>
              </div>
            </aside>
            <div v-if="total === 'armadilha'" class="row">
              <div class="col-11 m-4">
                <InputLabel value="Selecione uma passagem" />
                <select v-model="selectedPassagem" class="form-select">
                  <option disabled value="">Selecione</option>
                  <option v-for="passagem in props.passagem" :value="passagem" :key="passagem.id">
                    {{ passagem.nome_id }}
                  </option>
                </select>
              </div>
              <div v-if="selectedPassagem">
                <div class="col-12 ">
                  <div class="card-body">
                    <h3 class="card-title text-center">Registro por Passagem</h3>
                    <div class="d-flex justify-content-center align-items-center">
                      <BarChart :chart_data="chartDataBarFiltered" :chart_options="chartOptionsBar2" />
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div v-if="total === 'total'" class="row">
              <div class="col-12 mt-4">
                <div class="card-body">
                  <h3 class="card-title text-center">Registros totais</h3>
                  <div class="d-flex justify-content-center align-items-center">
                    <BarChart :chart_data="props.getChartDataBarEspecie" :chart_options="chartOptionsBar2" />
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>


    <ModalVideo ref="modalVideoRef" url="/file/Dashboard/Dashboard_monitora_fauna.mp4" />

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
