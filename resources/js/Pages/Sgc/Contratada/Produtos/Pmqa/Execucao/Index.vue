<script setup>
import { ref } from "vue";
import ProdutoTabsLayout from "../../ProdutoTabsLayout.vue";
import ModelSearchFormAllColumns from "@/Components/ModelSearchFormAllColumns.vue";
import Table from "@/Components/Table.vue";
import NavButton from "@/Components/NavButton.vue";
import ModalCampanha from "./ModalCampanha.vue";
import { dateTimeFormat } from "@/Utils/DateTimeUtils";
import { IconSettings, IconPencil } from "@tabler/icons-vue";
import NavLink from "@/Components/NavLink.vue";

const props = defineProps({
  contrato: Object,
  pmqa: Object,
  produto: [String, Object],
  campanhas: Object,
  pontos: Object,
});

const activeTab = ref("execucao");
const modalCampanha = ref(null);

const abrirModalCampanha = (item = null) => {
  modalCampanha.value?.abrirModal(item);
};
</script>

<template>
  <ProdutoTabsLayout
    :contratos="contrato"
    :title="'PMQA - EIA'"
    :pmqa="pmqa"
    :produto="produto"
    v-model:activeTab="activeTab"
  >
    <template #execucao>
      <ModelSearchFormAllColumns :columns="['nome', 'data_inicio', 'data_termino']">
        <template #action>
          <NavButton @click="abrirModalCampanha()" type-button="success" title="Nova campanha" />
        </template>
      </ModelSearchFormAllColumns>

      <Table
        :columns="['Nome da campanha','Data de início','Data de término','Pontos','Ação']"
        :records="campanhas"
        table-class="table-hover"
      >
        <template #body="{ item }">
          <tr>
            <td class="text-center">{{ item.nome_campanha }}</td>
            <td class="text-center">{{ dateTimeFormat(item.dt_inicio) }}</td>
            <td class="text-center">{{ dateTimeFormat(item.dt_fim) }}</td>
            <td class="text-center">{{ item.pontos.length }}</td>
            <td class="text-center">
              <NavLink route-name="contratos.contratada.sgc.pmqa.execucao.gerenciar"
                  :param="{ contrato: contrato.id, produto: produto, pmqa: pmqa.id, campanha: item.id }"
                  class="btn btn-icon btn-info me-1" :icon="IconSettings" />
              <NavButton @click="abrirModalCampanha(item)" :icon="IconPencil" class="btn-icon" type-button="primary" />
            </td>
          </tr>
        </template>
      </Table>

      <ModalCampanha
        :contrato="contrato"
        :pmqa="pmqa"
        :pontos="pontos"
        :produto="produto"
        ref="modalCampanha"
      />
    </template>
  </ProdutoTabsLayout>
</template>
