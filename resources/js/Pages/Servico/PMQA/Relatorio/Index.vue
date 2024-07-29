<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import Navbar from "../Navbar.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import ModelSearchFormAllColumns from "@/Components/ModelSearchFormAllColumns.vue";
import Table from "@/Components/Table.vue";
import NavButton from "@/Components/NavButton.vue";
import { ref } from "vue";
import { dateTimeFormat } from "@/Utils/DateTimeUtils";
import { IconDots } from "@tabler/icons-vue";
import ModalFormRelatorio from "./ModalFormRelatorio.vue";
import ModalVisualizarRelatorio from "./ModalVisualizarRelatorio.vue";

const modalFormRelatorio = ref({});
const modalVisualizarRelatorio = ref({});

const props = defineProps({
  contrato: { type: Object },
  servico: { type: Object },
  relatorios: { type: Object },
  resultados: { type: Array }
});

const abrirModalFormRelatorio = (item) => {
  modalFormRelatorio.value.abrirModal(item);
}

const abrirVisualizarRelatorio = (item) => {
  modalVisualizarRelatorio.value.abrirModal(item);
}

const excluirRelatorio = (item) => {
  router.delete(route('contratos.contratada.servicos.pmqa.relatorio.delete', { contrato: props.contrato.id, servico: props.servico.id, relatorio: item.id }));
}

</script>
<template>

  <Head :title="`${contrato.contratada.slice(0, 10)}...`" />

  <AuthenticatedLayout>

    <template #header>
      <div class="w-100 d-flex justify-content-between">
        <Breadcrumb class="align-self-center" :links="[
          { route: route('contratos.gestao.listagem', contrato.tipo_id), label: `Gestão de Contratos` },
          { route: '#', label: contrato.contratada }
        ]
          " />
        <Link class="btn btn-dark"
          :href="route('contratos.contratada.servicos.index', { contrato: props.contrato.id })">
        Voltar
        </Link>
      </div>
    </template>

    <Navbar :contrato="contrato" :servico="servico">
      <template #body>
        <!-- Pesquisa-->
        <ModelSearchFormAllColumns :columns="['nome']">
          <template #action>
            <NavButton @click="abrirModalFormRelatorio()" type-button="success" title="Novo relatório" />
          </template>
        </ModelSearchFormAllColumns>

        <!-- Listagem-->
        <Table :columns="['Nome do relatório', 'Data', 'Status', 'Ação']" :records="relatorios"
          table-class="table-hover">
          <template #body="{ item }">
            <tr>
              <td>{{ item.nome }}</td>
              <td>{{ dateTimeFormat(item.created_at) }}</td>
              <td class="text-center">
                <span v-if="item.status_id === 1" class="badge bg-azure-lt">
                  {{ item.status?.nome }}
                </span>
                <span v-else-if="item.status_id === 2" class="badge bg-red-lt">
                  {{ item.status?.nome }}
                </span>
                <span v-else-if="item.status_id === 3" class="badge bg-blue-lt">
                  {{ item.status?.nome }}
                </span>
              </td>
              <td>
                <button type="button" class="btn btn-icon btn-info dropdown-toggle p-2" data-bs-boundary="viewport"
                  data-bs-toggle="dropdown" aria-expanded="false">
                  <IconDots />
                </button>
                <div class="dropdown-menu dropdown-menu-end" style="">
                  <a class="dropdown-item" href="javascript:void(0)">
                    Conclusão
                  </a>
                  <a @click="abrirVisualizarRelatorio(item)" class="dropdown-item" href="javascript:void(0)">
                    Visualizar relátorio
                  </a>
                  <a @click="abrirModalFormRelatorio(item)" class="dropdown-item" href="javascript:void(0)">
                    Editar
                  </a>
                  <a @click="excluirRelatorio(item)" class="dropdown-item" href="javascript:void(0)">
                    Excluir
                  </a>
                  <a class="dropdown-item" href="javascript:void(0)">
                    Enviar para o fiscal
                  </a>
                  <a class="dropdown-item" target="_blank"
                    :href="route('contratos.contratada.servicos.pmqa.relatorio.gerar_pdf', { contrato: contrato.id, servico: servico.id, relatorio: item.id })">
                    Exportar relatório
                  </a>
                </div>
              </td>
            </tr>
          </template>
        </Table>
      </template>
    </Navbar>

    <ModalFormRelatorio :contrato="contrato" :servico="servico" :resultados="resultados" ref="modalFormRelatorio" />
    <ModalVisualizarRelatorio :contrato="contrato" :servico="servico" ref="modalVisualizarRelatorio" />

  </AuthenticatedLayout>
</template>