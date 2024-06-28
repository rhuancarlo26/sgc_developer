<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import Navbar from "../Navbar.vue";
import { Head } from "@inertiajs/vue3";
import ModelSearchFormAllColumns from "@/Components/ModelSearchFormAllColumns.vue";
import Table from "@/Components/Table.vue";
import NavButton from "@/Components/NavButton.vue";
import { IconEye } from "@tabler/icons-vue";
import { IconRulerMeasure } from "@tabler/icons-vue";
import { IconChartHistogram } from "@tabler/icons-vue";
import ModalVisualizarPonto from "./ModalVisualizarPonto.vue";
import { ref } from "vue";

const modalVisualizarPonto = ref({});

const props = defineProps({
  contrato: { type: Object },
  servico: { type: Object },
  campanha: { type: Object },
  pontos: { type: Object }
});

const abrirModalVisualizarPonto = (item) => {
  modalVisualizarPonto.value.abrirModal(item);
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
        <ModelSearchFormAllColumns :columns="[]" />

        <!-- Listagem-->
        <Table :columns="['Ponto', 'classe', 'Tipo de ambiente', 'UF', 'Município', 'coleta', 'Medição', 'Ação']"
          :records="pontos" :axios-pagination="true" table-class="table-hover">
          <template #body="{ item }">
            <tr>
              <td>{{ item.nomepontocoleta }}</td>
              <td>{{ item.classe }}</td>
              <td>{{ item.tipoambiente }}</td>
              <td>{{ item.uf }}</td>
              <td>{{ item.municipio }}</td>
              <td></td>
              <td></td>
              <td>
                <NavButton @click="abrirModalVisualizarPonto(item)" :icon="IconEye" class="btn-icon"
                  type-button="info" />
                <NavButton :icon="IconRulerMeasure" class="btn-icon" type-button="primary" />
                <NavButton :icon="IconChartHistogram" class="btn-icon" type-button="primary" />
              </td>
            </tr>
          </template>
        </Table>
      </template>
    </Navbar>

    <ModalVisualizarPonto :campanha="campanha" :contrato="contrato" :servico="servico" ref="modalVisualizarPonto" />

  </AuthenticatedLayout>
</template>