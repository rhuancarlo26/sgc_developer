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
import ModalResultado from "./ModalResultado.vue";

const refModalResultado = ref({});

const props = defineProps({
  contrato: { type: Object },
  servico: { type: Object },
  resultados: { type: Object },
  campanhas: { type: Array }
});

const abrirModalResultado = (item) => {
  refModalResultado.value.abrirModal(item);
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
            <NavButton @click="abrirModalResultado()" type-button="success" title="Novo resultado" />
          </template>
        </ModelSearchFormAllColumns>

        <!-- Listagem-->
        <Table :columns="['Nome do resultado', 'Campanhas', 'Data', 'Ação']" :records="resultados"
          table-class="table-hover">
          <template #body="{ item }">
            <tr>
              <td>{{ item.nome }}</td>
              <td>
                <span v-for="campanha in item.campanhas.map(campanha => campanha.nome)" :key="campanha"
                  class="badge bg-warning text-white m-1">
                  {{ campanha }}
                </span>
              </td>
              <td>{{ dateTimeFormat(item.created_at) }}</td>
              <td>
                <NavLink route-name="contratos.contratada.servicos.pmqa.resultado.resultado"
                  :param="{ contrato: contrato.id, servico: servico.id, resultado: item.id }"
                  class="btn btn-icon btn-info me-1" :icon="IconSettings" />
                <NavButton @click="abrirModalResultado(item)" :icon="IconPencil" class="btn-icon"
                  type-button="primary" />
                <LinkConfirmation v-slot="confirmation" :options="{ text: 'A remoção da campanha será permanente.' }">
                  <Link :onBefore="confirmation.show"
                    :href="route('contratos.contratada.servicos.pmqa.resultado.delete', { contrato: contrato.id, servico: servico.id, resultado: item.id })"
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

    <ModalResultado :contrato="contrato" :servico="servico" :campanhas="campanhas" ref="refModalResultado" />

  </AuthenticatedLayout>
</template>