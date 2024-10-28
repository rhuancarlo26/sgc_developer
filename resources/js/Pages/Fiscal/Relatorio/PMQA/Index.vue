<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import Navbar from "../../Navbar.vue";
import ModelSearchFormAllColumns from "@/Components/ModelSearchFormAllColumns.vue";
import Table from "@/Components/Table.vue";
import { IconDots } from "@tabler/icons-vue";
import ModalEmitirParecer from "./ModalEmitirParecer.vue";
import { ref } from "vue";
import ModalVisualizarRelatorio from "@/Pages/Servico/PMQA/Relatorio/ModalVisualizarRelatorio.vue";

const props = defineProps({
  contrato: { type: Object },
  relatorios: { type: Object }
});

const modalEmitirParecerRef = ref({});
const modalVisualizarRelatorioRef = ref({});

const abrirModalEmitirParecer = (item) => {
  modalEmitirParecerRef.value.abrirModal(item);
}

const abrirVisualizarRelatorio = (item) => {
  modalVisualizarRelatorioRef.value.abrirModal(item);
}

</script>
<template>

  <Head :title="`${contrato.contratada.slice(0, 10)}...`" />

  <AuthenticatedLayout>

    <template #header>
      <div class="w-100 d-flex justify-content-between">
        <Breadcrumb class="align-self-center" :links="[
          { route: route('contratos.gestao.listagem', contrato.tipo_contrato), label: `Gestão de Contratos` },
          { route: '#', label: contrato.contratada }
        ]
          " />
        <Link class="btn btn-dark"
          :href="route('contratos.contratada.servicos.index', { contrato: props.contrato.id })">
        Voltar
        </Link>
      </div>
    </template>

    <Navbar :contrato="contrato">
      <template #body>
        <ModelSearchFormAllColumns :columns="[]">
          <template #action>
          </template>
        </ModelSearchFormAllColumns>

        <Table :columns="['Nome relátorio', 'Status do relátorio', 'Ação']" :records="relatorios"
          table-class="table-hover">
          <template #body="{ item }">
            <tr>
              <td>{{ item.nome }}</td>
              <td class="text-center">
                <span v-if="item.fk_status === 1" class="badge bg-yellow-lt">
                  Em análise
                </span>
                <span v-else-if="item.fk_status === 3" class="badge bg-blue-lt">
                  Aprovado
                </span>
                <span v-else-if="item.fk_status === 2" class="badge bg-red-lt">
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
                  <a @click="abrirVisualizarRelatorio(item)" class="dropdown-item" href="javascript:void(0)">
                    relatório
                  </a>
                  <a @click="abrirModalEmitirParecer(item)" class="dropdown-item" href="javascript:void(0)">
                    Parecer
                  </a>
                </div>
              </td>
            </tr>
          </template>
        </Table>
      </template>
    </Navbar>

    <ModalEmitirParecer :contrato="contrato" ref="modalEmitirParecerRef" />
    <ModalVisualizarRelatorio :contrato="contrato" :servico="{}" ref="modalVisualizarRelatorioRef" />

  </AuthenticatedLayout>
</template>
