<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import Navbar from "../Navbar.vue";
import { Head, Link } from "@inertiajs/vue3";
import ModelSearchFormAllColumns from "@/Components/ModelSearchFormAllColumns.vue";
import Table from "@/Components/Table.vue";
import NavButton from "@/Components/NavButton.vue";
import NavLink from "@/Components/NavLink.vue";
import ModalCampanha from "./ModalCampanha.vue";
import { ref } from "vue";
import { dateTimeFormat } from "@/Utils/DateTimeUtils";
import LinkConfirmation from "@/Components/LinkConfirmation.vue";
import { IconTrash } from "@tabler/icons-vue";
import { IconPencil } from "@tabler/icons-vue";
import { IconSettings } from "@tabler/icons-vue";

const modalCampanha = ref({});

const props = defineProps({
  contrato: { type: Object },
  servico: { type: Object },
  campanhas: { type: Object },
  pontos: { type: Array }
});

const abrirModalCampanha = (item) => {
  modalCampanha.value.abrirModal(item);
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
        <Link class="btn btn-dark" :href="route('contratos.contratada.servicos.index', { contrato: props.contrato.id })">
        Voltar
        </Link>
      </div>
    </template>

    <Navbar :contrato="contrato" :servico="servico">
      <template #body>
        <!-- Pesquisa-->
        <ModelSearchFormAllColumns :columns="['nome', 'data_inicio', 'data_termino']">
          <template #action>
            <NavButton @click="abrirModalCampanha()" type-button="success" title="Nova campanha" />
          </template>
        </ModelSearchFormAllColumns>

        <!-- Listagem-->
        <Table :columns="['Nome da campanha', 'Data de início', 'Data de término', 'Pontos', 'Ação']"
          :records="campanhas" table-class="table-hover">
          <template #body="{ item }">
            <tr>
              <td>{{ item.nome }}</td>
              <td>{{ dateTimeFormat(item.data_inicio) }}</td>
              <td>{{ dateTimeFormat(item.data_termino) }}</td>
              <td>{{ item.pontos.length }}</td>
              <td>
                <NavLink route-name="contratos.contratada.servicos.pmqa.execucao.gerenciar"
                  :param="{ contrato: contrato.id, servico: servico.id, campanha: item.id }"
                  class="btn btn-icon btn-info me-1" :icon="IconSettings" />
                <!-- <NavButton :icon="IconSettings" class="btn-icon" type-button="info" /> -->
                <NavButton @click="abrirModalCampanha(item)" :icon="IconPencil" class="btn-icon"
                  type-button="primary" />
                <LinkConfirmation v-slot="confirmation" :options="{ text: 'A remoção da campanha será permanente.' }">
                  <Link :onBefore="confirmation.show"
                    :href="route('contratos.contratada.servicos.pmqa.execucao.destroy', { contrato: contrato.id, servico: servico.id, campanha: item.id })"
                    as="button" method="delete" type="button" class="btn btn-icon btn-danger">
                  <IconTrash />
                  </Link>
                </LinkConfirmation>
              </td>
            </tr>
          </template>
        </Table>
      </template>
    </Navbar>

    <ModalCampanha :contrato="contrato" :servico="servico" :pontos="pontos" ref="modalCampanha" />

  </AuthenticatedLayout>
</template>