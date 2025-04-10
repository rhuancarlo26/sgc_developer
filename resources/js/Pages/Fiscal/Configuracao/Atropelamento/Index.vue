<script setup>
import Table from "@/Components/Table.vue";
import ModelSearchForm from "@/Components/ModelSearchForm.vue";
import { Head } from "@inertiajs/vue3";
import Navbar from "../../Navbar.vue";
import { IconDots } from "@tabler/icons-vue";
import ModalParecerFiscal from "./Modals/ModalParecerFiscal.vue";
import ModalVisualizarServico from "./Modals/ModalVisualizarServico.vue";
import { ref } from "vue";

defineProps({
  contrato: Object,
  servicos: Object
});

const modalParecerFiscalRef = ref({});
const modalVisualizacaoServicoRef = ref({});

const abrirModalParecer = (item) => {
  modalParecerFiscalRef.value.abrirModal(item);
}

const abrirModalVisualizarServico = (item) => {
  modalVisualizacaoServicoRef.value.abrirModal(item);
}

</script>
<template>

  <Head :title="`${contrato.contratada.slice(0, 10)}...`" />

  <Navbar :contrato="contrato">
    <template #body>
      <ModelSearchForm :search-columns="{}" />
      <Table :columns="['#', 'Serviço', 'Parecer', 'Status Aprovação', 'Ação']" :records="servicos"
        table-class="table-hover">
        <template #body="{ item }">
          <tr>
            <td class="text-center">{{ item.id }}</td>
            <td class="text-center">{{ item.tema?.nome_tema }} - {{ item.tipo?.nome }}</td>
            <td> {{ item.parecer_atropelamento?.parecer }}</td>
            <td class="text-center">
              <span v-if="item.parecer_atropelamento?.fk_status === 1" class="badge bg-yellow-lt">
                Em análise
              </span>
              <span v-else-if="item.parecer_atropelamento?.fk_status === 3" class="badge bg-blue-lt">
                Aprovado
              </span>
              <span v-else-if="item.parecer_atropelamento?.fk_status === 2" class="badge bg-red-lt">
                Pendente
              </span>
              <span v-else class="badge bg-red-lt">
                Em confecção
              </span>
            </td>
            <td class="text-center">
              <button type="button" class="btn btn-icon btn-info dropdown-toggle p-2" data-bs-boundary="viewport"
                data-bs-toggle="dropdown" aria-expanded="false">
                <IconDots />
              </button>
              <div class="dropdown-menu dropdown-menu-end">
                <a @click="abrirModalVisualizarServico(item)" class="dropdown-item" href="javascript:void(0)">
                  Visualizar
                </a>
                <a @click="abrirModalParecer(item)" class="dropdown-item" href="javascript:void(0)">
                  Parecer
                </a>
              </div>
            </td>
          </tr>
        </template>
      </Table>
    </template>
  </Navbar>

  <ModalParecerFiscal ref="modalParecerFiscalRef" />
  <ModalVisualizarServico ref="modalVisualizacaoServicoRef" />

</template>
