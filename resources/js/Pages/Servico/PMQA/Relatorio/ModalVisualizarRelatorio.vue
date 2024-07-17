<script setup>
import Modal from "@/Components/Modal.vue";
import { ref } from "vue";
import FolhaA4 from "./Components/FolhaA4.vue";
import RelatorioCapa from "./Components/relatorioCapa.vue";
import RelatorioDadosBasicos from "./Components/RelatorioDadosBasicos.vue";
import RelatorioParametros from "./Components/RelatorioParametros.vue";
import RelatorioPontos from "./Components/RelatorioPontos.vue";
import RelatorioVeiculos from "./Components/RelatorioVeiculos.vue";
import RelatorioCampanhas from "./Components/RelatorioCampanhas.vue";

const props = defineProps({
  contrato: { type: Object },
  servico: { type: Object }
});

const modalRelatorio = ref({});
const relatorio = ref({});
const pagina = ref(1);

const abrirModal = (item) => {
  relatorio.value = item;

  modalRelatorio.value.getBsModal().show();
}

defineExpose({ abrirModal });
</script>

<template>
  <Modal ref="modalRelatorio" title="Visualizar relatório" modal-dialog-class="modal-xl">
    <template #body>
      <div class="d-flex justify-content-center mb-4">
        <button @click="pagina -= 1" class="btn btn-primary me-4">Anterior</button>
        <button @click="pagina += 1" class="btn btn-primary">Próximo</button>
      </div>
      <FolhaA4>
        <div v-show="pagina === 1">
          <RelatorioCapa :contrato="contrato" :relatorio="relatorio" />
        </div>
        <div v-show="pagina === 2">
          <RelatorioDadosBasicos :servico="servico" />
        </div>
        <div v-show="pagina === 3">
          <RelatorioVeiculos :servico="servico" />
        </div>
        <div v-show="pagina === 4">
          <RelatorioPontos :servico="servico" :relatorio="relatorio" />
        </div>
        <div v-show="pagina === 5">
          <RelatorioParametros :servico="servico" :relatorio="relatorio" />
        </div>
        <div v-show="pagina === 6">
          <RelatorioCampanhas :relatorio="relatorio" />
        </div>
        <div v-show="pagina === 7">
          Teste 7
        </div>
        <div v-show="pagina === 8">
          Teste 8
        </div>
        <div v-show="pagina === 9">
          Teste 9
        </div>
      </FolhaA4>
    </template>
    <template #footer>
    </template>
  </Modal>
</template>
