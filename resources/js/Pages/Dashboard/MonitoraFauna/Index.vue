<script setup>
import BarChart from '@/Components/BarChart.vue';
import LineChart from '@/Components/LineChart.vue';
import PieChart from '@/Components/PieChart.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Map from '@/Components/MapPontos.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import ModalVideo from '../ModalVideo.vue';
import { IconPlayerPlay } from '@tabler/icons-vue';
import NavButton from '@/Components/NavButton.vue';

const props = defineProps({

  chartDataPieAbundancia: Object,
  chartDataPieDiversidade: Object,
  chartDataBar: Object,
  chartDataBar2: Object,
  chartDataLine: Object,
  modulos: Object

});

const total = ref('total');
const registro = ref('resultado');
const curva = ref('armadilha');
const modalVideoRef = ref({});
const mapaVisualizarTrecho = ref(null);


const chartOptionsPie = ref({
  responsive: true,
  plugins: {
    legend: {
      display: false, // Remove a legenda global
    },
    tooltip: {
      enabled: true,
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
const selectedModulo = ref(null);


const chartOptionsLine = ref({
  responsive: true,
  plugins: {
    legend: { display: false },
    tooltip: { enabled: true },
    datalabels: {
      anchor: "end",
      align: "top",
      color: "#333",
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

  props.modulos.forEach(modulo => {
    const longitude = Number(modulo.longitude_final);
    const latitude = Number(modulo.latitude_inicial);

    geojson.push([
      JSON.stringify({
        type: "Feature",
        geometry: {
          type: "Point",
          coordinates: [longitude, latitude]
        }
      }),
      modalPontoMap(modulo),
      modulo
    ]);
  });

  return geojson;
});

const modalPontoMap = (modulo) => {
  return `
    <span><strong>Dados do modulo de Coleta</strong></span><br>
    <span><strong>ID: </strong> ${modulo.id}</span><br>
    <span><strong>Chave: </strong> ${modulo.chave}</span><br>
    <span><strong>ID do Serviço: </strong> ${modulo.id_servico}</span><br>
    <span><strong>Tamanho do Módulo: </strong> ${modulo.tamanho_modulo}</span><br>
    <span><strong>Data de Cadastro: </strong> ${modulo.data_cadastro}</span><br>
    <span><strong>UF: </strong> ${modulo.uf}</span><br>
    <span><strong>Município: </strong> ${modulo.municipio}</span><br>
    <span><strong>Bioma: </strong> ${modulo.bioma}</span><br>
    <span><strong>Latitude Inicial: </strong> ${modulo.latitude_inicial}</span><br>
    <span><strong>Longitude Inicial: </strong> ${modulo.longitude_inicial}</span><br>
    <span><strong>Zona Inicial: </strong> ${modulo.zona_inicial ? modulo.zona_inicial : 'N/A'}</span><br>
    <span><strong>Latitude Final: </strong> ${modulo.latitude_final}</span><br>
    <span><strong>Longitude Final: </strong> ${modulo.longitude_final}</span><br>
    <span><strong>Zona Final: </strong> ${modulo.zona_final ? modulo.zona_final : 'N/A'}</span><br>
    <span><strong>Fitofisionomia: </strong> ${modulo.fitofisionomia}</span><br>
    <span><strong>Nome do Arquivo: </strong> ${modulo.nome_arquivo}</span><br>
    <span><strong>Observações: </strong> ${modulo.obs && modulo.obs !== '-' ? modulo.obs : 'Nenhuma'}</span><br>
    <span><strong>Local do Shape: </strong> ${modulo.local_shape}</span><br>
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
            Programa de Monitoramento de Fauna
          </h1>
        </div>
      </div>
      <div class="">
        <div class="d-flex">
          <div class="col-8 card card-body me-4">
            <Map ref="mapaVisualizarTrecho" height="900px" :manual-render="true" />
          </div>
          <div class="col-4">
            <div class="card card-body mb-4">
              <div>
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
                        <div class="d-flex justify-content-center align-items-center" style="height: 200px;">
                          <PieChart :chart_data="props.chartDataPieAbundancia" :chart_options="chartOptionsPie" />
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6 mb-3">
                    <div class="card h-100">
                      <div class="card-body">
                        <h3 class="card-title text-center">Diversidade</h3>
                        <div class="d-flex justify-content-center align-items-center" style="height: 200px;">
                          <PieChart :chart_data="props.chartDataPieDiversidade" :chart_options="chartOptionsPie" />
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <!-- Seção do gráfico de barras/linha com seleção -->
              <div class="card">
                <div class="card-header">
                  <div>
                    <label class="form-check form-check-inline">
                      <input class="form-check-input" type="radio" name="armadilha" value="armadilha" v-model="curva"
                        id="radioArmadilha">
                      <span class="form-check-label">Registros por modulo</span>
                    </label>
                    <label class="form-check form-check-inline">
                      <input class="form-check-input" type="radio" name="curva" value="curva" v-model="curva"
                        id="radioCurva" autocomplete="off">
                      <label for="radioCurva">Curva do coletor</label>
                    </label>
                  </div>
                </div>
                <div class="card-body">
                  <div v-if="curva === 'armadilha'">
                    <BarChart :chart_data="props.chartDataBar" :chart_options="chartOptionsBar" />
                  </div>
                  <div v-else>
                    <div style="overflow-x: auto; width: 100%;">
                      <LineChart style="min-width: 500px;" :chart_data="chartDataLine"
                        :chart_options="chartOptionsLine" />
                    </div>
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
                    <input class="form-check-input" type="radio" name="total" value="armadilha" v-model="total">
                    <span class="form-check-label">Registros por armadilha</span>
                  </label>
                </div>
                <div v-if="total === 'total'" class="row">
                  <BarChart height="2000px" :chart_data="props.chartDataBar2" :chart_options="chartOptionsBar2" />

                </div>
                <div v-if="total === 'armadilha'" class="row">
                  <div class="row">
                    <div class="col-11 m-4">
                      <InputLabel value="Selecione um Modulo" />
                      <select v-model="selectedModulo" class="form-select">
                        <option v-for="modulo in props.modulos" :value="modulo" :key="modulo.id">
                          {{ modulo.id }}
                        </option>
                      </select>
                    </div>
                    <div v-if="selectedModulo">               
                      <div class="col-11 m-4">
                        <InputLabel value="Selecione uma Armadilha" />
                        <select v-model="selectedArmadilha" class="form-select">
                          <option v-for="armadilha in selectedModulo.armadilhas" :value="armadilha" :key="armadilha.id">
                            {{ armadilha.tipo }}
                          </option>
                        </select>
                      </div>
                    </div>

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
