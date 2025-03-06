<script setup>

import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import Navbar from "../../Navbar.vue";
import NavButton from "@/Components/NavButton.vue";
import ModelSearchFormAllColumns from "@/Components/ModelSearchFormAllColumns.vue";
import Table from "@/Components/Table.vue";
import { IconDots, IconMap, IconMinus } from "@tabler/icons-vue";
import ModalFormModuloAmostral from "./ModalFormModuloAmostral.vue"
import { ref } from "vue";
import { Head, Link, router } from "@inertiajs/vue3";
import ModalMapa from "./ModalMapa.vue";
import LinkConfirmation from "@/Components/LinkConfirmation.vue";

const props = defineProps({
  contrato: { type: Object },
  servico: { type: Object },
  modulos: { type: Object },
  ufs: { type: Array }
});

const modalFormModuloAmostralRef = ref({});
const modalMapaRef = ref({});
const linkConfirmationRef = ref({});

const abrirModalFormModuloAmostral = (item) => {
  modalFormModuloAmostralRef.value.abrirModal(item);
}

const abrirMapa = (item) => {
  modalMapaRef.value.abrirModal(item);
}

const excluirModuloAmostral = (item) => {
  linkConfirmationRef.value.show(() => {
    router.delete(route('contratos.contratada.servicos.monitora_fauna.configuracoes.modulo_amostral.delete', {
      contrato: props.contrato.id,
      servico: props.servico.id,
      modulo: item.id
    }))
  })
}

</script>
<template>

  <Head title="Módulos Amostrais" />

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
            <NavButton @click="abrirModalFormModuloAmostral()" type-button="info" title="Novo Módulo" />
          </template>
        </ModelSearchFormAllColumns>

        <Table
          :columns="['ID Módulo', 'N° Parcelas', 'UF', 'Município', 'Bioma', 'Fitofisionomia', 'Shapefile', 'Observações', 'Ação']"
          :records="modulos" table-class="table-hover">
          <template #body="{ item }">
            <tr>
              <td>{{ item.id }}</td>
              <td>{{ item.tamanho_modulo }}</td>
              <td>{{ item.uf }}</td>
              <td>{{ item.municipio }}</td>
              <td>{{ item.bioma }}</td>
              <td>{{ item.fitofisionomia }}</td>
              <td>
                <NavButton v-if="!item.shape_file || item.shape_file.trim() === ''" class="text-red"
                  type-button="default" :icon="IconMinus" />
                <NavButton v-else @click="abrirMapa(item)" type-button="default" class="text-blue" :icon="IconMap" />
              </td>
              <td>{{ item.obs }}</td>
              <td>
                <button type="button" class="btn btn-icon btn-info dropdown-toggle p-2" data-bs-boundary="viewport"
                  data-bs-toggle="dropdown" aria-expanded="false">
                  <IconDots />
                </button>
                <div class="dropdown-menu dropdown-menu-end">
                  <a @click="abrirModalFormModuloAmostral(item)" href="javascript:void(0);" class="dropdown-item">
                    Editar
                  </a>
                  <a @click="excluirModuloAmostral(item)" href="javascript:void(0);" class="dropdown-item">
                    Excluir
                  </a>
                </div>
              </td>
            </tr>
          </template>
        </Table>
      </template>
    </Navbar>

    <ModalFormModuloAmostral :contrato="contrato" :servico="servico" :ufs="ufs" ref="modalFormModuloAmostralRef" />
    <ModalMapa ref="modalMapaRef" />
    <LinkConfirmation ref="linkConfirmationRef" :options="{ text: 'Deseja realmente excluir o modulo?' }" />

  </AuthenticatedLayout>
</template>
