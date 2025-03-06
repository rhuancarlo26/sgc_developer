<script setup>
import Modal from "@/Components/Modal.vue";
import BarChart from "@/Components/BarChart.vue";
import PieChart from "@/Components/PieChart.vue";
import { useForm } from "@inertiajs/vue3";
import { ref } from "vue";
import InputLabel from "@/Components/InputLabel.vue";

const props = defineProps({
  contrato: { type: Object },
  servico: { type: Object },
  dados_campanha: { type: Object, default: { dataChart: {} } },
  dados_armadilha: { type: Object, default: { dataChart: {} } },
  dados_ordem: { type: Object, default: { dataChart: {} } },
  dados_familia: { type: Object, default: { dataChart: {} } },
});

const modal = ref();

const form = useForm({});

const abrirModal = () => {
  modal.value.getBsModal().show();
}

const chartOptions = ref({
  responsive: true,
  plugins: {
    legend: {
      position: 'top',
    }
  }
});

defineExpose({ abrirModal });
</script>

<template>
  <Modal ref="modal" title="Modal" modal-dialog-class="modal-xl">
    <template #body>
      <div class="card-body">
        <div class="mb-4">
          <div class="mb-4">
            <h3>Análise da Riqueza por Campanha</h3>
            <BarChart v-if="dados_campanha.chartData" :chart_data="dados_campanha.chartData"
              :chart_options="chartOptions" />
          </div>
          <div class="row">
            <div class="col">
              <InputLabel value="Analise" />
              <textarea name="analise" id="analise" class="form-control" rows="4"></textarea>
            </div>
          </div>
        </div>
        <div class="mb-4">
          <div class="mb-4">
            <h3>Analise da Riqueza x Módulo x Métodos</h3>
            <BarChart v-if="dados_armadilha.chartData" :chart_data="dados_armadilha.chartData"
              :chart_options="chartOptions" />
            <div class="row">
              <div class="col">
                <InputLabel value="Analise" />
                <textarea name="analise" id="analise" class="form-control" rows="4"></textarea>
              </div>
            </div>
          </div>
        </div>
        <div class="mb-4">
          <div class="mb-4">
            <h3>Analise da Riqueza por Ordem</h3>
            <div v-if="dados_ordem.chartData" class="d-flex justify-content-center" :style="{ height: '525px' }">
              <PieChart :chart_data="dados_ordem.chartData" :chart_options="chartOptions" />
            </div>
            <div class="row">
              <div class="col">
                <InputLabel value="Analise" />
                <textarea name="analise" id="analise" class="form-control" rows="4"></textarea>
              </div>
            </div>
          </div>
        </div>
        <div>
          <div class="mb-4">
            <h3>Analise da Riqueza por Família</h3>
            <BarChart v-if="dados_familia.chartData" :chart_data="dados_familia.chartData"
              :chart_options="chartOptions" />
          </div>
          <div class="row">
            <div class="col">
              <InputLabel value="Analise" />
              <textarea name="analise" id="analise" class="form-control" rows="4"></textarea>
            </div>
          </div>
        </div>
      </div>
    </template>
  </Modal>
</template>
