<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import Navbar from "../Navbar.vue";
import { Head, Link } from "@inertiajs/vue3";
import ModelSearchFormAllColumns from "@/Components/ModelSearchFormAllColumns.vue";
import Table from "@/Components/Table.vue";
import NavButton from "@/Components/NavButton.vue";
import NavLink from "@/Components/NavLink.vue";
import { ref } from "vue";
import { dateTimeFormat } from "@/Utils/DateTimeUtils";
import LinkConfirmation from "@/Components/LinkConfirmation.vue";
import { IconTrash } from "@tabler/icons-vue";
import { IconPencil } from "@tabler/icons-vue";
import { IconSettings } from "@tabler/icons-vue";
import ModalFormRelatorio from "./ModalFormRelatorio.vue";

const modalFormRelatorio = ref({});

const props = defineProps({
  contrato: { type: Object },
  servico: { type: Object },
  resultados: { type: Array }
});

const abrirModalFormRelatorio = () => {
  modalFormRelatorio.value.abrirModal();
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
        <Table :columns="['Nome do relatório', 'Data', 'Status', 'Ação']" :records="[]" table-class="table-hover">
          <template #body="{}">
            <tr>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
            </tr>
          </template>
        </Table>
      </template>
    </Navbar>

    <ModalFormRelatorio :contrato="contrato" :servico="servico" :resultados="resultados" ref="modalFormRelatorio" />

  </AuthenticatedLayout>
</template>