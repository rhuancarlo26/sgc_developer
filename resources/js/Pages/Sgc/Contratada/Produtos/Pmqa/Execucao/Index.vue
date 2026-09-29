<script setup>
import { ref, computed } from "vue";
import { router } from "@inertiajs/vue3";
import ProdutoTabsLayout from "../ProdutoTabsLayout.vue";
import ModelSearchFormAllColumns from "@/Components/ModelSearchFormAllColumns.vue";
import Table from "@/Components/Table.vue";
import NavButton from "@/Components/NavButton.vue";
import ModalCampanha from "./ModalCampanha.vue";
import { dateTimeFormat } from "@/Utils/DateTimeUtils";
import { IconSettings, IconPencil, IconEye, IconListDetails } from "@tabler/icons-vue";
import { Link } from "@inertiajs/vue3";
import { usePmqaPermissions } from '../Composables/usePmqaPermissions';

const props = defineProps({
  contrato: Object,
  pmqa: Object,
  produto: [String, Object],
  campanhas: Object,
  campanhas: Object,
  pontos: Object,
  canApprove: { type: Boolean, default: false },
});

const activeTab = ref("execucao");
const modalCampanha = ref(null);

const { podeGerenciar: podeGerenciarExecucao, reprovarFase } = usePmqaPermissions(props, 'execucao');

const abrirModalCampanha = (item = null) => {
  modalCampanha.value?.abrirModal(item);
};


const podeEnviarAoFiscal = computed(() => {
    const listaCampanhas = props.campanhas?.data ?? props.campanhas ?? [];

    return listaCampanhas.some(campanha => {
        if (!campanha.campanha_pontos) return false;

        return campanha.campanha_pontos.some(cp => cp.coleta && cp.medicao);
    });
});

const enviarParaAnalise = () => {
    if (!confirm("Tem certeza que deseja enviar a Execução para análise?")) return;
    router.post(route('sgc.contratada.produtos.pmqa.enviarAnaliseFase', [props.contrato.id, props.produto, props.pmqa.id]), {
        fase: 'execucao'
    }, { preserveScroll: true });
};

const aprovarFase = () => {
    if (!confirm("Confirmar a aprovação desta fase de Execução?")) return;
    router.post(route('sgc.contratada.produtos.pmqa.aprovarFase', [props.contrato.id, props.produto, props.pmqa.id]), {
        fase: 'execucao'
    }, { preserveScroll: true });
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
      <ModelSearchFormAllColumns :columns="['Nome da campanha', 'Data de início', 'Data de término', 'Pontos', 'Ação']">
        <template #action>
          <NavButton
              v-if="!canApprove && podeGerenciarExecucao"
              type-button="primary"
              title="Enviar ao fiscal"
              @click="enviarParaAnalise"
              class="me-2"
              :disabled="!podeEnviarAoFiscal"
          />
          <NavButton
              v-if="canApprove && podeGerenciarExecucao"
              type-button="danger"
              title="✖ Reprovar Execução"
              @click="reprovarFase"
              class="me-2"
          />
          <NavButton
              v-if="canApprove && podeGerenciarExecucao"
              type-button="success"
              title="✓ Aprovar Execução"
              @click="aprovarFase"
              class="me-2"
          />
          <NavButton v-if="!canApprove && podeGerenciarExecucao" @click="abrirModalCampanha()" type-button="success" title="Nova campanha" />
        </template>
      </ModelSearchFormAllColumns>

      <Table
        :columns="['Nome da campanha', 'Data de início', 'Data de término', 'Pontos', 'Ação']"
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
              <NavButton
                @click="abrirModalCampanha(item)"
                :icon="(!canApprove && podeGerenciarExecucao) ? IconPencil : IconEye"
                class="btn-icon me-1"
                :type-button="(!canApprove && podeGerenciarExecucao) ? 'primary' : 'info'"
              />

              <Link :href="route('contratos.contratada.sgc.pmqa.execucao.gerenciar', { contrato: contrato.id, produto: produto, pmqa: pmqa.id, campanha: item.id })"
                  class="btn btn-icon btn-info me-1"
                  :title="(!canApprove && podeGerenciarExecucao) ? 'Gerenciar Medições' : 'Visualizar Medições'">
                  <IconSettings v-if="!canApprove && podeGerenciarExecucao" />
                  <IconListDetails v-else />
              </Link>
            </td>
          </tr>
        </template>
      </Table>

      <ModalCampanha
        :contrato="contrato"
        :pmqa="pmqa"
        :pontos="pontos"
        :produto="produto"
        :canApprove="canApprove"
        ref="modalCampanha"
      />
    </template>
  </ProdutoTabsLayout>
</template>
