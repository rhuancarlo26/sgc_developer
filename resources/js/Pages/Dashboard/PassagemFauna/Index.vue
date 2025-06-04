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
const registro = ref('resultado');
const curva = ref('armadilha');
const mapaVisualizarTrecho = ref(null);


const props = defineProps({

  chartDataPieAbundancia: Object,
  chartDataPieDiversidade: Object,
  chartDataBar: Object,
  chartDataBar2: Object,
  chartDataLine: Object,
  modulos: Object,
  especiesGroup: Object,
  contrato: Object,

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


const chartDataBar2 = ref({
  labels: [
    "Pecari tajacu", "Amphisbaena ...", "Didelphis mars...",
    "Bothrops atrox", "Caluromys sp.", "Coragyps atratus",
    "Crotophaga ani", "Eunectes muri...", "Hylaeamys me...",
    "Hypsiboas rani...", "Metachirus nu...",
    "Nasua nasua", "Oxyrhopus me...",
    "Podocnemis u...", "Priodontes ma...",
    "Pseudopaludic...", "Puma concolor",
    "Ramphastos tu..."
  ],
  datasets: [
    {
      label: "Ocorrências",
      data: [3, 2, 2, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1],
      backgroundColor: "rgba(30, 144, 255, 0.8)",
      borderColor: "rgba(30, 144, 255, 1)",
      borderWidth: 1,
    },
  ],
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
    <div>
      <div class="card card-body mb-4">
        <div class="row justify-content-center">
          <div class="card-body text-center">
            <h1 class="card-title mb-3"> Programa de Monitoramento de Passagem de Fauna</h1>
            <p class="mb-2"><strong>Contratada:</strong> {{ contrato?.contratada }}</p>
            <p class="mb-2"><strong>Número do Contrato:</strong> {{ contrato?.numero_contrato }}</p>
            <p class="mb-0"><strong>UFs / BRs:</strong> {{ contrato?.ufs }} / {{ contrato?.brs }}</p>
          </div>
        </div>
      </div>
      <div class="">
        <div class="d-flex">
          <div class="col-8 card card-body me-4">
            <Map ref="mapaVisualizarTrecho" height="900px" :manual-render="true" />
          </div>
          <div class="col-4">
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
              <div class="card">
                <div class="mb-4">
                  <div class="m-1">
                    <label class="form-check form-check-inline">
                      <input class="form-check-input" type="radio" name="curva" value="armadilha" v-model="curva">
                      <span class="form-check-label">Registro por passagem</span>
                    </label>
                    <label class="form-check form-check-inline">
                      <input class="form-check-input" type="radio" name="curva" value="curva" v-model="curva">
                      <span class="form-check-label">Total de registros (Campanha)</span>
                    </label>
                  </div>
                </div>

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
                <div class="m-1">
                  <label class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="total" value="total" v-model="total">
                    <span class="form-check-label">Registros totais</span>
                  </label>
                  <label class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="total" value="armadilha" v-model="total">
                    <span class="form-check-label">Registros por armadilha</span>
                  </label>
                </div>
                <div v-if="total === 'armadilha'" class="row">
                  <div class="row w-50">
                    <div class="col">
                      <InputLabel value="Registro totais" />
                      <select name="" id="" class="form-select">
                        <option value="teste">teste</option>
                      </select>
                    </div>
                    <div class="col">
                      <InputLabel value="Registros por passagem" />
                      <select name="" id="" class="form-select">
                        <option value="teste">teste</option>
                      </select>
                    </div>
                  </div>
                </div>
                <BarChart :chart_data="chartDataBar2" :chart_options="chartOptionsBar2" />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <ModalVideo ref="modalVideoRef" url="/file/Dashboard/Dashboard_monitora_fauna.mp4" />

  </AuthenticatedLayout>

</template>
