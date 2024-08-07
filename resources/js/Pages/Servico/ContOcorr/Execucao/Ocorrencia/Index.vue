<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import Navbar from "../../Navbar.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import ModelSearchFormAllColumns from "@/Components/ModelSearchFormAllColumns.vue";
import Table from "@/Components/Table.vue";
import NavButton from "@/Components/NavButton.vue";
import LinkConfirmation from "@/Components/LinkConfirmation.vue";
import { ref } from "vue";
import { IconEye } from "@tabler/icons-vue";
import { IconPlus } from "@tabler/icons-vue";
import { IconPencil } from "@tabler/icons-vue";
import { IconTrash } from "@tabler/icons-vue";
import NavLink from "@/Components/NavLink.vue";
import { IconMap } from "@tabler/icons-vue";
import { dateTimeFormat } from "@/Utils/DateTimeUtils";
import ModalVisualizarOcorrencia from "./ModalVisualizarOcorrencia.vue";

const modalVisualizarOcorrencia = ref({});

const props = defineProps({
  contrato: { type: Object },
  servico: { type: Object },
  ocorrencias: { type: Object }
});

const abrirModal = (item) => {
  modalVisualizarOcorrencia.value.abrirModal(item)
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
        <ModelSearchFormAllColumns :columns="['nome_id', 'intensidade', 'lote.nome_id', 'lote.empresa']">
          <template #action>
            <NavLink route-name="contratos.contratada.servicos.cont_ocorrencia.execucao.ocorrencia.create"
              :param="{ contrato: props.contrato.id, servico: props.servico.id }" class="btn btn-success"
              title="Nova ocorrência" />
          </template>
        </ModelSearchFormAllColumns>
        <Table
          :columns="['ID Ocorrência', 'Intensidade Ocorrência', 'Data da Ocorrência', 'Ocorrênia anterior', 'Prazo Ocorrência', 'Lote', 'Construtora', 'Status Aprovação', 'Envio', 'Ação']"
          :records="ocorrencias" table-class="table-hover">
          <template #body="{ item }">
            <tr>
              <td>{{ item.nome_id }}</td>
              <td>{{ item.intensidade }}</td>
              <td>{{ dateTimeFormat(item.data_ocorrencia) }}</td>
              <td>-</td>
              <td>-</td>
              <td>{{ item.lote?.nome_id }}</td>
              <td>{{ item.lote?.empresa }}</td>
              <td>-</td>
              <td>-</td>
              <td class="d-inline-flex">
                <NavButton @click="abrirModal(item)" type-button="info" class="btn-icon" :icon="IconEye" />
                <NavLink class="btn btn-icon btn-primary me-1"
                  route-name="contratos.contratada.servicos.cont_ocorrencia.execucao.ocorrencia.create"
                  :param="{ contrato: contrato.id, servico: servico.id, ocorrencia: item.id }" :icon="IconPencil" />
                <LinkConfirmation v-slot="confirmation" :options="{ text: 'A remoção do lote será permanente.' }">
                  <Link :onBefore="confirmation.show"
                    :href="route('contratos.contratada.servicos.cont_ocorrencia.execucao.ocorrencia.delete', { contrato: contrato.id, servico: servico.id, ocorrencia: item.id })"
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

    <ModalVisualizarOcorrencia ref="modalVisualizarOcorrencia" />

  </AuthenticatedLayout>
</template>