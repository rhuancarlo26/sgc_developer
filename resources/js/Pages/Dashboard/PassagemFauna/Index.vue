<script setup>
import BarChart from '@/Components/BarChart.vue';
import LineChart from '@/Components/LineChart.vue';
import PieChart from '@/Components/PieChart.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Map from '@/Components/Map.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';

import ModalVideo from '../ModalVideo.vue';
import { IconPlayerPlay } from '@tabler/icons-vue';
import NavButton from '@/Components/NavButton.vue';

const total = ref('total');
const registro = ref('resultado');
const curva = ref('armadilha');
const modalVideoRef = ref({});

const abrirModalVideo = () => {
  modalVideoRef.value.abrirModal()
}

const chartDataPie = ref({
  labels: ["Anfíbios", "Répteis", "Aves", "Mamíferos"],
  datasets: [
    {
      data: [5.3, 18, 39.5, 36.8], // Percentuais
      backgroundColor: ["#a6c48a", "#7d9c6d", "#b3c99c", "#d5dfb3"], // Cores semelhantes à imagem
      borderColor: "#ffffff", // Bordas brancas para separar as fatias
      borderWidth: 2,
    },
  ],
});

const chartDataBar = ref({
  labels: ["PF05", "PF10", "PF01", "PF09", "PF07", "PF03", "PF08", "PF02", "PF04", "PF06"],
  datasets: [
    {
      label: "Ocorrências",
      data: [152, 81, 78, 75, 50, 41, 34, 23, 18, 2],
      backgroundColor: "#007bff",
      borderRadius: 5,
    },
  ],
});

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

const chartDataBar3 = ref({
  labels: ["PF05", "PF10", "PF01", "PF09", "PF07", "PF03", "PF08", "PF02", "PF04", "PF06"],
  datasets: [
    {
      label: "Ocorrências",
      data: [152, 81, 78, 75, 50, 41, 34, 23, 18, 2],
      backgroundColor: "#007bff",
      borderRadius: 5,
    },
  ],
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

</script>

<template>

  <Head title="Dashboard" />

  <AuthenticatedLayout>

    

    <div>
      <div class="card card-body mb-4">
        <div class="text-end">
          <NavButton @click="abrirModalVideo()" :icon="IconPlayerPlay" title="Abrir Video" type-button="success" />
        </div>
        <div class="justify-content-center">
          <h1 class="text-center">
            Programa de Monitoramento de Passagem de Fauna
          </h1>
          <div class=" d-flex justify-content-end">
            <div class="row w-25">
              <div class="col">
                <InputLabel value="UF" />
                <select name="" id="" class="form-select">
                  <option value="teste">teste</option>
                </select>
              </div>
              <div class="col">
                <InputLabel value="BR" />
                <select name="" id="" class="form-select">
                  <option value="teste">teste</option>
                </select>
              </div>
              <div class="col">
                <InputLabel value="Período" />
                <select name="" id="" class="form-select">
                  <option value="teste">teste</option>
                </select>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="">
        <div class="d-flex">
          <div class="col-8 card card-body me-4">
            <Map :height="'100vh'" />
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
              <div class="mb-4">
                <div class="d-flex">
                  <div class="col card me-4">
                    <h3>Abundância</h3>
                    <div class="d-flex justify-content-center" style="height: 200px;">
                      <PieChart :chart_data="chartDataPie" :chart_options="chartOptionsPie" />
                    </div>
                  </div>
                  <div class="col card">
                    <h3>Diversidade</h3>
                    <div class="d-flex justify-content-center" style="height: 200px;">
                      <PieChart :chart_data="chartDataPie" :chart_options="chartOptionsPie" />
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
                      <span class="form-check-label">Total de registros (Período)</span>
                    </label>
                  </div>
                </div>

                <div v-if="curva === 'armadilha'">
                  <BarChart :chart_data="chartDataBar3" :chart_options="chartOptionsBar3" />
                </div>
                <div v-else>
                  <BarChart :chart_data="chartDataBar3" :chart_options="chartOptionsBar3" />
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
