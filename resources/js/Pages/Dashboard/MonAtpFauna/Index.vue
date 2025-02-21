<script setup>
import BarChart from '@/Components/BarChart.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Map from '@/Components/Map.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import Navbar from '../Navbar.vue';

const chartData = ref({
  labels: [
    "Pecari tajacu", "Amphisbaena ...", "Didelphis mars...",
    "Bothrops atrox", "Caluromys sp.", "Coragyps atratus",
    "Crotophaga ani", "Eunectes muri...", "Hylaeamys me...",
    "Hypsiboas rani...", "Metachirus nu...", "Nasua nasua",
    "Oxyrhopus me...", "Podocnemis u...", "Priodontes ma...",
    "Pseudopaludic...", "Puma concolor", "Ramphastos tu..."
  ],
  datasets: [
    {
      label: "Ocorrências",
      data: [3, 2, 2, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1],
      backgroundColor: "#007bff",
      borderRadius: 3,
    },
  ],
});

const chartOptions = ref({
  responsive: true,
  indexAxis: "y",
  plugins: {
    legend: { display: false },
    tooltip: { enabled: true },
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
  barPercentage: 0.5,
  maxBarThickness: 15,
});

</script>

<template>

  <Head title="Dashboard" />

  <AuthenticatedLayout>

    <Navbar />

    <div>
      <div class="card card-body mb-4">
        <div class="justify-content-center">
          <h1 class="text-center">
            Programa de Monitoramento do Atropelamento de Fauna
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
            <Map :height="'500px'" />
          </div>
          <div class="col-4">
            <div class="card card-body mb-4">
              <div class="">
                <label class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="radios-inline">
                  <span class="form-check-label">Resultados</span>
                </label>
                <label class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="radios-inline" checked="">
                  <span class="form-check-label">Regsitros</span>
                </label>
              </div>
            </div>
            <div class="card mb-4">
              <div class="card-body">
                <BarChart :chart_data="chartData" :chart_options="chartOptions" />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>

</template>
