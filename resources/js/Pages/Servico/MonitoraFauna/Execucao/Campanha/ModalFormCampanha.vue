<script setup>
import InputLabel from "@/Components/InputLabel.vue";
import Modal from "@/Components/Modal.vue";
import { Link, router, useForm } from "@inertiajs/vue3";
import { computed, ref } from "vue";
import { dateTimeFormat } from "@/Utils/DateTimeUtils.js";
import { IconTrash } from "@tabler/icons-vue";
import Table from "@/Components/Table.vue";
import LinkConfirmation from "@/Components/LinkConfirmation.vue";
import NavButton from "@/Components/NavButton.vue";
import InputError from "@/Components/InputError.vue";
import TabDadosGerais from "./TabDadosGerais.vue";

const props = defineProps({
  contrato: { type: Object },
  servico: { type: Object },
  modulos: { type: Array }
})

const modalRef = ref({});

const abrirModal = (item) => {
  modalRef.value.getBsModal().show();
}

defineExpose({ abrirModal });
</script>

<template>
  <Modal ref="modalRef" title="Novo Módulo Amostral" modal-dialog-class="modal-xl">
    <template #body>
      <div class="card-header">
        <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs" role="tablist">
          <li class="nav-item" role="presentation">
            <a href="#dados_gerais" class="nav-link active" data-bs-toggle="tab" aria-selected="true" role="tab">DADOS
              GERAIS</a>
          </li>
          <li class="nav-item" role="presentation">
            <a href="#vincular_abio" class="nav-link" data-bs-toggle="tab" aria-selected="false" role="tab"
              tabindex="-1">VINCULAR ABIO</a>
          </li>
          <li class="nav-item" role="presentation">
            <a href="#vincular_profissionais" class="nav-link" data-bs-toggle="tab" aria-selected="false" role="tab"
              tabindex="-1">VINCULAR PROFISSIONAIS</a>
          </li>
        </ul>
      </div>
      <div class="card-body">
        <div class="tab-content">
          <div class="tab-pane active show" id="dados_gerais" role="tabpanel">
            <TabDadosGerais :contrato="contrato" :servico="servico" :modulos="modulos" />
          </div>
          <div class="tab-pane" id="vincular_abio" role="tabpanel">
          </div>
          <div class="tab-pane" id="vincular_profissionais" role="tabpanel">
          </div>
        </div>
      </div>
    </template>
  </Modal>
</template>
