<script setup>
import NavButton from '@/Components/NavButton.vue';
import BarChart from '@/Components/BarChart.vue';
import LineChart from '@/Components/LineChart.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Map from '@/Components/Map.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import Navbar from '../Navbar.vue';
import ModalVideo from '../ModalVideo.vue';

const modalVideoRef = ref({});
const registro = ref('resultado');

const abrirModalVideo = () => {
  modalVideoRef.value.abrirModal()
}

const chartDataBar = ref({
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

const chartOptionsBar = ref({
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

const chartDataLine = ref({
  labels: ["Ago/07", "Set/07", "Out/07", "Nov/07", "Dez/07", "Jan/08", "Fev/08", "Mar/08", "Abr/08", "Mai/08", "Jun/08", "Jul/08"],
  datasets: [
    {
      label: "IQA",
      data: [80, 75, 78, 72, 70, 65, 80, 60, 75, 85, 70, 65],
      borderColor: "black",
      backgroundColor: "transparent",
      pointBorderColor: "black",
      pointBackgroundColor: "white",
      pointRadius: 4,
      borderWidth: 1.5,
      tension: 0.2, // Suaviza a linha
    },
    {
      label: "Área de Qualidade",
      data: Array(12).fill(100), // Apenas para criar o fundo
      backgroundColor: [
        "rgba(173, 216, 230, 0.5)", // Ótima (Azul)
        "rgba(144, 238, 144, 0.5)", // Boa (Verde)
        "rgba(255, 255, 102, 0.5)", // Regular (Amarelo)
        "rgba(255, 165, 0, 0.5)", // Ruim (Laranja)
        "rgba(255, 69, 0, 0.5)", // Péssima (Vermelho)
      ],
      borderWidth: 0,
      fill: "start",
    }
  ],
});

const chartOptionsLine = ref({
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
      max: 100,
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
        <div class="text-end">
          <NavButton @click="abrirModalVideo()" :icon="IconPlayerPlay" title="Abrir Video" type-button="success" />
        </div>
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
                  <input class="form-check-input" type="radio" name="radios-inline" value="resultado"
                    v-model="registro">
                  <span class="form-check-label">Resultados</span>
                </label>
                <label class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="radios-inline" value="registro" v-model="registro">
                  <span class="form-check-label">Registro</span>
                </label>
              </div>
            </div>
            <div v-if="registro === 'resultado'">
              <div class="card mb-4">
                <div class="card-header">
                  Qualidade da água
                </div>
                <div class="card-body">
                  <p><strong>Pontos dentro do limite:</strong></p>
                  <p><strong>Pontos fora do limite:</strong></p>
                </div>
              </div>
              <div class="card mb-4">
                <div class="card-header">
                  IQA (média para o período)
                </div>
                <div class="card-body">
                  <BarChart :chart_data="chartDataBar" :chart_options="chartOptionsBar" />
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

                  <BarChart :chart_data="chartDataBar" :chart_options="chartOptionsBar" />
                </div>
              </div>
            </div>
            <div v-else>
              <div class="card card-body mb-4">
                <h5>PONTO 001</h5>
                <p><strong>Nome:</strong></p>
                <p><strong>Nome do recurso hídrico:</strong></p>
                <div class="row">
                  <div class="col">
                    <p><strong>Longitude:</strong></p>
                  </div>
                  <div class="col">
                    <p><strong>Latitude:</strong></p>
                  </div>
                </div>
                <p><strong>UF:</strong></p>
                <p><strong>Classificação:</strong></p>
                <div class="row">
                  <div class="col">
                    <p><strong>CLasse:</strong></p>
                  </div>
                  <div class="col">
                    <p><strong>Tipo de Ambiente:</strong></p>
                  </div>
                </div>
              </div>
              <div class="card card-body">
                <div class="row mb-4">
                  <div class="col">
                    <InputLabel value="Parâmetros" />
                    <select name="" id="" class="form-select">
                      <option value="teste">teste</option>
                    </select>
                  </div>
                  <div class="col">
                    <InputLabel value="Campanha" />
                    <select name="" id="" class="form-select">
                      <option value="teste">teste</option>
                    </select>
                  </div>
                </div>
                <LineChart :chart_data="chartDataLine" :chart_options="chartOptionsLine" />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <ModalVideo ref="modalVideoRef" url="/file/Dashboard/Dashboard_monitora_fauna.mp4" />

  </AuthenticatedLayout>

</template>
