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
  especiesGroup: Object
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


const modalPontoRegistro = (registro) => {
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
    <div>
      <div class="card card-body mb-4">
        <div class="justify-content-center">
          <h1 class="text-center">
            Programa de Monitoramento do Atropelamento de Fauna
          </h1>
        </div>
      </div>
      <div class="">
        <div class="d-flex">
          <div class="col-7 card card-body me-4">
            <Map ref="mapaVisualizarTrecho" height="900px" :manual-render="true" />
          </div>
          <div class="col-5">
            <div class="card card-body mb-4">
              <div class="">
                <label class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="resultado" value="resultado" v-model="registro">
                  <span class="form-check-label">Resultados</span>
                </label>
                <label class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="resultado" value="registro" v-model="registro">
                  <span class="form-check-label">Registro</span>
                </label>
              </div>
            </div>
            <div v-if="registro === 'resultado'">
              <!-- Seção dos gráficos de pizza -->
              <div class="mb-4">
                <div class="row">
                  <div class="col-md-6 mb-3">
                    <div class="card h-100">
                      <div class="card-body">
                        <h3 class="card-title text-center">Abundância</h3>
                        <div class="d-flex justify-content-center align-items-center"
                          style="height:255px; width:255px;">
                          <PieChart :chart_data="props.chartDataPieAbundancia" :chart_options="chartOptionsPie" />
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6 mb-3">
                    <div class="card h-100">
                      <div class="card-body">
                        <h3 class="card-title text-center">Riqueza</h3>
                        <div class="d-flex justify-content-center align-items-center"
                          style="height:255px; width:255px;">
                          <PieChart :chart_data="props.chartDataPieDiversidade" :chart_options="chartOptionsPie" />
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="card h-100">
                <div class="card-body">
                  <h3 class="card-title text-center">Registro por campanha
                  </h3>
                  <div class="d-flex justify-content-center align-items-center">
                    <BarChart :chart_data="getChartDataBarCampanhas" :chart_options="chartOptionsBar" />
                  </div>
                </div>
              </div>
            </div>
            <div v-else>
              <div class="card">
                <div class="m-3">
                  <label class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="total" value="total" v-model="total">
                    <span class="form-check-label">Registros totais</span>
                  </label>
                  <label class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="total" value="campanha" v-model="total">
                    <span class="form-check-label">Registros por campanha</span>
                  </label>
                </div>
                <div v-if="total === 'total'" class="row">
                  <BarChart height="2000px" :chart_data="props.chartDataBar2" :chart_options="chartOptionsBar2" />

                </div>
                <div v-if="total === 'campanha'" class="row">
                  <div class="col-11 m-4">
                    <InputLabel value="Selecione uma campanha" />
                    <select v-model.number="selectedCampanha" class="form-select">
                      <option disabled value="">Selecione</option>
                      <!-- se props.campanhas for null, iteramos sobre [] -->
                      <option v-for="campanha in props.campanhas || []" :key="campanha?.id" :value="campanha?.id">
                        {{ campanha?.id }}
                      </option>
                    </select>
                  </div>
                  <div v-if="selectedCampanha" class="row">
                    <BarChart height="2000px" :chart_data="chartDataBarFilteredByCampanha"
                      :chart_options="chartOptionsBar2" />
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
