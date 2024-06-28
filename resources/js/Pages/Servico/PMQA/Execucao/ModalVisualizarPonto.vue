<script setup>
import Modal from "@/Components/Modal.vue";
import { ref } from "vue";
import TabDadosPonto from "./TabDadosPonto.vue";
import TabColeta from "./TabColeta.vue";
import TabMedicao from "./TabMedicao.vue";

const props = defineProps({
  contrato: { type: Object },
  servico: { type: Object },
  campanha: { type: Object },
});

const ponto = ref({});
const modal = ref();

const abrirModal = (item) => {
  ponto.value = item;

  modal.value.getBsModal().show();
}

defineExpose({ abrirModal });
</script>

<template>
  <Modal ref="modal" :title="`Visualizar ${ponto.nomepontocoleta} - ${campanha.nome}`" modal-dialog-class="modal-xl">
    <template #body>
      <div class="card-body">
        <div class="card">
          <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs" role="tablist">
              <li class="nav-item" role="presentation">
                <a href="#tab-dados-ponto" class="nav-link active" data-bs-toggle="tab" aria-selected="true"
                  role="tab">Dados do ponto</a>
              </li>
              <li class="nav-item" role="presentation">
                <a href="#tab-dados-coleta" class="nav-link" data-bs-toggle="tab" aria-selected="false" tabindex="-1"
                  role="tab">Dados da coleta</a>
              </li>
              <li class="nav-item" role="presentation">
                <a href="#tab-dados-medicao" class="nav-link" data-bs-toggle="tab" aria-selected="false" tabindex="-1"
                  role="tab">dados da medição</a>
              </li>
            </ul>
          </div>
          <div class="card-body">
            <div class="tab-content">
              <div class="tab-pane active show" id="tab-dados-ponto" role="tabpanel">
                <TabDadosPonto :ponto="ponto" />
              </div>
              <div class="tab-pane" id="tab-dados-coleta" role="tabpanel">
                <TabColeta />
              </div>
              <div class="tab-pane" id="tab-dados-medicao" role="tabpanel">
                <TabMedicao />
              </div>
            </div>
          </div>
        </div>
      </div>
    </template>
    <template #footer>
    </template>
  </Modal>
</template>
