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
import ModalVisualizarLote from "./ModalVisualizarLote.vue";

const modalVisualizarLote = ref({})

const props = defineProps({
  contrato: { type: Object },
  servico: { type: Object },
  lotes: { type: Object }
});

const abrirModal = (item) => {
  modalVisualizarLote.value.abrirModal(item)
}

const enviarLoteFiscal = () => {
  router.post(route('contratos.contratada.servicos.cont_ocorrencia.configuracao.lote_obra.enviar_lote_fiscal', { contrato: props.contrato.id, servico: props.servico.id }));
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
          :href="route('contratos.contratada.servicos.index', { contrato: props.contrato.id })">
        Voltar
        </Link>
      </div>
    </template>

    <Navbar :contrato="contrato" :servico="servico">
      <template #body>
        <ModelSearchFormAllColumns :columns="[]">
          <template #action
            v-if="!servico.cont_ocorr_parecer_configuracao || servico.cont_ocorr_parecer_configuracao?.fk_status === 1">
            <NavButton @click="enviarLoteFiscal()" type-button="primary" title="Submeter ao fiscal" />
            <NavLink route-name="contratos.contratada.servicos.cont_ocorrencia.configuracao.lote_obra.create"
              :param="{ contrato: props.contrato.id, servico: props.servico.id }" class="btn btn-success"
              title="Novo lote" />
          </template>
        </ModelSearchFormAllColumns>
        <Table
          :columns="['Lote', 'Nome', 'BR', 'UF', 'KM inicial', 'KM final', 'Construtora/Consórcio', 'N° contrato', 'Situação', 'Supervisora de obra', 'Ação']"
          :records="lotes" table-class="table-hover">
          <template #body="{ item }">
            <tr>
              <td>{{ item.nome_id }}</td>
              <td>{{ item.nome }}</td>
              <td>{{ item.rodovia?.rodovia }}</td>
              <td>{{ item.uf?.uf }}</td>
              <td>{{ item.km_inicial }}</td>
              <td>{{ item.km_final }}</td>
              <td>{{ item.empresa }}</td>
              <td>{{ item.num_contrato }}</td>
              <td>{{ item.situacao_contrato }}</td>
              <td>{{ item.supervisora_obras }}</td>
              <td class="d-inline-flex">
                <NavButton @click="abrirModal(item)" type-button="info" class="btn-icon" :icon="IconEye" />

                <template
                  v-if="!servico.cont_ocorr_parecer_configuracao || servico.cont_ocorr_parecer_configuracao?.fk_status === 1">
                  <NavLink class="btn btn-icon btn-primary me-1"
                    route-name="contratos.contratada.servicos.cont_ocorrencia.configuracao.lote_obra.create"
                    :param="{ contrato: contrato.id, servico: servico.id, lote: item.id }" :icon="IconPencil" />
                  <LinkConfirmation v-slot="confirmation" :options="{ text: 'A remoção do lote será permanente.' }">
                    <Link :onBefore="confirmation.show"
                      :href="route('contratos.contratada.servicos.cont_ocorrencia.configuracao.lote_obra.delete', { contrato: contrato.id, servico: servico.id, lote: item.id })"
                      as="button" method="delete" type="button" class="btn btn-icon btn-danger">
                    <IconTrash />
                    </Link>
                  </LinkConfirmation>
                </template>
              </td>
            </tr>
          </template>
        </Table>
      </template>
    </Navbar>

    <ModalVisualizarLote ref="modalVisualizarLote" />

  </AuthenticatedLayout>
</template>
