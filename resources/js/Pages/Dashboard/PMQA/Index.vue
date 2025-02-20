<script setup>
import BarChart from '@/Components/BarChart.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Map from '@/Components/Map.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import Navbar from '../Navbar.vue';

const chartData = ref({
  labels: ["PF05", "PF10", "PF01", "PF09", "PF07", "PF03", "PF08", "PF02", "PF04", "PF06"],
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
    },
    y: {
      beginAtZero: true,
      grid: { drawBorder: false },
    },
  },
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
            Programa de Monitoramento da Qualidade da Água
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
                  <input class="form-check-input" type="radio" name="radios-inline" checked="">
                  <span class="form-check-label">Resultados</span>
                </label>
                <label class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="radios-inline">
                  <span class="form-check-label">Regsitros</span>
                </label>
              </div>
            </div>
            <div class="card mb-4">
              <div class="card-header">
                IQA (média para o período)
              </div>
              <div class="card-body">
                <BarChart :chart_data="chartData" :chart_options="chartOptions" />
              </div>
            </div>
            <div class="card">
              <div class="card-header">
                Outros Parâmetros (média para o período)
              </div>
              <div class="card-body">
                <div class="col mb-4">
                  <InputLabel value="Parâmetros" />
                  <select name="" id="" class="form-select">
                    <option value="teste">teste</option>
                  </select>
                </div>

                <BarChart :chart_data="chartData" :chart_options="chartOptions" />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>

</template>
