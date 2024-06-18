<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import Navbar from "../../Navbar.vue";
import { Head, Link } from "@inertiajs/vue3";
import ModelSearchFormAllColumns from "@/Components/ModelSearchFormAllColumns.vue";
import Table from "@/Components/Table.vue";
import NavButton from "@/Components/NavButton.vue";
import { ref } from "vue";
import { IconEye } from "@tabler/icons-vue";
import { IconPlus } from "@tabler/icons-vue";
import { IconPencil } from "@tabler/icons-vue";
import { IconTrash } from "@tabler/icons-vue";
import NavLink from "@/Components/NavLink.vue";
import LinkConfirmation from "@/Components/LinkConfirmation.vue";
import ModalParametros from "./ModalParametros.vue";

const modalParametros = ref({});

const props = defineProps({
  contrato: { type: Object },
  servico: { type: Object },
  parametros: { type: Array }
});

const abrirModalParametros = () => {
  modalParametros.value.abrirModal();
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
      </div>
    </template>

    <Navbar :contrato="contrato" :servico="servico">
      <template #body>
        <!-- Pesquisa-->
        <ModelSearchFormAllColumns :columns="['nome', 'parametro.nome']">
          <template #action>
            <NavButton @click="abrirModalParametros()" type-button="success" title="Novo parâmetro" />
          </template>
        </ModelSearchFormAllColumns>

        <!-- Listagem-->
        <Table :columns="['Nome', 'Parâmetros', 'Ação']" :records="[]" table-class="table-hover">
          <template #body="{ item }">
            <tr>
              <td>{{ item.nome }}</td>
              <td>{{ item.parametros }}</td>
              <td></td>
            </tr>
          </template>
        </Table>
      </template>
    </Navbar>

    <ModalParametros :contrato="contrato" :servico="servico" :parametros="parametros" ref="modalParametros" />

  </AuthenticatedLayout>
</template>