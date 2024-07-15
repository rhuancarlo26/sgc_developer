<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import Navbar from "../Navbar.vue";
import { Head, Link } from "@inertiajs/vue3";
import ModelSearchFormAllColumns from "@/Components/ModelSearchFormAllColumns.vue";
import Table from "@/Components/Table.vue";
import NavButton from "@/Components/NavButton.vue";
import { ref } from "vue";
import { dateTimeFormat } from "@/Utils/DateTimeUtils";
import { IconDots } from "@tabler/icons-vue";
import ModalFormRelatorio from "./ModalFormRelatorio.vue";

const modalFormRelatorio = ref({});

const props = defineProps({
  contrato: { type: Object },
  servico: { type: Object },
  relatorios: { type: Object },
  resultados: { type: Array }
});

const abrirModalFormRelatorio = (item) => {
  modalFormRelatorio.value.abrirModal(item);
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
              <td class="text-center">
                <span class="dropdown">
                  <button class="btn btn-info dropdown-toggle align-text-top" data-bs-boundary="viewport"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <IconDots />
                  </button>
                  <div class="dropdown-menu dropdown-menu-end" style="">
                    <a class="dropdown-item" href="javascript:void(0)">
                      Conclusão
                    </a>
                    <a class="dropdown-item" href="javascript:void(0)">
                      Visualizar relátorio
                    </a>
                    <a @click="abrirModalFormRelatorio(item)" class="dropdown-item" href="javascript:void(0)">
                      Editar
                    </a>
                    <Link
                      :href="route('contratos.contratada.servicos.pmqa.relatorio.delete', { contrato: contrato.id, servico: servico.id, relatorio: item.id })"
                      method="DELETE" class="dropdown-item">
                    Excluir
                    </Link>
                    <a class="dropdown-item" href="javascript:void(0)">
                      Enviar para o fiscal
                    </a>
                    <a class="dropdown-item" href="javascript:void(0)">
                      Exportar relatório
                    </a>
                  </div>
                </span>
              </td>
            </tr>
          </template>
        </Table>
      </template>
    </Navbar>

    <ModalFormRelatorio :contrato="contrato" :servico="servico" :resultados="resultados" ref="modalFormRelatorio" />

  </AuthenticatedLayout>
</template>