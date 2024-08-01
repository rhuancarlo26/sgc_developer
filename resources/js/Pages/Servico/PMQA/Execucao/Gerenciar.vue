<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import Navbar from "../Navbar.vue";
import { Head, Link } from "@inertiajs/vue3";
import ModelSearchFormAllColumns from "@/Components/ModelSearchFormAllColumns.vue";
import Table from "@/Components/Table.vue";
import NavButton from "@/Components/NavButton.vue";
import { IconEye } from "@tabler/icons-vue";
import { IconRulerMeasure } from "@tabler/icons-vue";
import { IconChartHistogram } from "@tabler/icons-vue";
import ModalVisualizarPonto from "./ModalVisualizarPonto.vue";
import { ref } from "vue";
import { IconSquareCheck } from "@tabler/icons-vue";

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
          { route: route('contratos.gestao.listagem', contrato.tipo_contrato), label: `Gestão de Contratos` },
          { route: '#', label: contrato.contratada }
        ]
          " />
        <Link class="btn btn-dark"
          :href="route('contratos.contratada.servicos.pmqa.execucao.index', { contrato: props.contrato.id, servico: props.servico.id })">
        Voltar
        </Link>
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
              <td>{{ item.ponto?.nomepontocoleta }}</td>
              <td>{{ item.ponto?.classe }}</td>
              <td>{{ item.ponto?.tipoambiente }}</td>
              <td>{{ item.ponto?.uf }}</td>
              <td>{{ item.ponto?.municipio }}</td>
              <td>
                <div class="d-flex align-item-center justify-content-center text-success">
                  <NavButton v-if="item.coleta" :icon="IconSquareCheck" class="btn-icon text-success"
                    type-button="default" />
                </div>
              </td>
              <td>
                <div class="d-flex align-item-center justify-content-center text-success">
                  <NavButton v-if="item.medicao" :icon="IconSquareCheck" class="btn-icon text-success"
                    type-button="default" />
                </div>
              </td>
              <td>
                <NavButton @click="abrirModalVisualizarPonto(item)" :icon="IconEye" class="btn-icon"
                  type-button="info" />
                <Link class="btn btn-icon btn-primary me-1"
                  :href="route('contratos.contratada.servicos.pmqa.execucao.coleta.create', { contrato: props.contrato.id, servico: props.servico.id, campanha: props.campanha.id, ponto: item.id })">
                <IconRulerMeasure />
                </Link>
                <Link v-if="item.ponto.lista" class="btn btn-icon btn-primary me-1"
                  :href="route('contratos.contratada.servicos.pmqa.execucao.medir.create', { contrato: props.contrato.id, servico: props.servico.id, campanha: props.campanha.id, ponto: item.id })">
                <IconChartHistogram />
                </Link>
              </td>
            </tr>
          </template>
        </Table>
      </template>
    </Navbar>

    <ModalVisualizarPonto :campanha="campanha" :contrato="contrato" :servico="servico" ref="modalVisualizarPonto" />

  </AuthenticatedLayout>
</template>
