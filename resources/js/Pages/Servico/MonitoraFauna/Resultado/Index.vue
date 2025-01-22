<script setup>

import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import Navbar from "../Navbar.vue";
import NavButton from "@/Components/NavButton.vue";
import ModelSearchFormAllColumns from "@/Components/ModelSearchFormAllColumns.vue";
import Table from "@/Components/Table.vue";
import { IconDots, IconMap, IconMinus } from "@tabler/icons-vue";
import { ref } from "vue";
import { Head, Link, router } from "@inertiajs/vue3";
import LinkConfirmation from "@/Components/LinkConfirmation.vue";
import { dateTimeFormat } from "@/Utils/DateTimeUtils";
import ModalFormResultado from "./ModalFormResultado.vue";

const props = defineProps({
  contrato: { type: Object },
  servico: { type: Object },
  resultados: { type: Object },
  campanhas: { type: Array }
});

const modalFormResultadoRef = ref();

const abrirModalFormResultado = (item) => {
  modalFormResultadoRef.value.abrirModal(item);
}

const excluir = (item_id) => {
  router.delete(route('contratos.contratada.servicos.monitora_fauna.resultado.destroy', { contrato: props.contrato.id, servico: props.servico.id, resultado: item_id }))
}

</script>
<template>

  <Head title="Resultado" />

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

    <Navbar :contrato="contrato" :servico="servico">
      <template #body>
        <ModelSearchFormAllColumns :columns="[]">
          <template #action>
            <NavButton @click="abrirModalFormResultado()" type-button="success" title="Novo Resultado" />
          </template>
        </ModelSearchFormAllColumns>

        <Table :columns="['ID Resultado', 'Nome Resultado', 'Campanhas', 'Data', 'Ação']" :records="resultados"
          table-class="table-hover">
          <template #body="{ item }">
            <tr>
              <td>{{ item.id }}</td>
              <td>{{ item.nome }}</td>
              <td>
                <span v-for="campanha in item.campanhas" :key="campanha.id" class="badge bg-warning text-white m-1">
                  {{ campanha.id }}
                </span>
              </td>
              <td>{{ dateTimeFormat(item.created_at) }}</td>
              <td>
                <button type="button" class="btn btn-icon btn-info dropdown-toggle p-2" data-bs-boundary="viewport"
                  data-bs-toggle="dropdown" aria-expanded="false">
                  <IconDots />
                </button>
                <div class="dropdown-menu dropdown-menu-end">
                  <a @click="abrirModalFormResultado(item)" class="dropdown-item">Editar</a>
                  <a @click="excluir(item.id)" class="dropdown-item" href="javascript:void(0)">Excluir</a>
                </div>
              </td>
            </tr>
          </template>
        </Table>
      </template>
    </Navbar>

    <ModalFormResultado ref="modalFormResultadoRef" :contrato="contrato" :servico="servico" :campanhas="campanhas" />

  </AuthenticatedLayout>
</template>
