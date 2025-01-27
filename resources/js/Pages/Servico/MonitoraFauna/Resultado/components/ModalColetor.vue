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
  dados_coletor: { type: Object, default: { dataChart: {} } },
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
        <div>
          <pre>{{ dados_coletor }}</pre>
          <div class="mb-4">
            <h3>Analise da Riqueza por Família</h3>
            <BarChart v-if="dados_coletor.chartData" :chart_data="dados_coletor.chartData"
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
