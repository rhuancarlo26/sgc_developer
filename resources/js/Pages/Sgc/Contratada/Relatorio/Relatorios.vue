<script setup>
import { Head } from "@inertiajs/vue3";
import { onMounted, ref } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import Modal from '@/Components/Modal.vue';
import { sugerirPeriodoRelatorio } from './periodoRelatorio';
import NavbarContrato from "../NavbarContrato.vue";
import NavLink from '@/Components/NavLink.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { defineProps } from 'vue';
import { IconDoorEnter } from "@tabler/icons-vue";

const user = usePage().props.auth.user;

const props = defineProps({
  contrato: Object,
  dadosrelat: { type: Array }
});

const form = ref({
  id: null,
  contrato_id: props.contrato.id,
  item_id: null
});

const modalPeriodo = ref(null);
const novoRelatorio = useForm({ contrato: { id: props.contrato.id }, data_inicio: '', data_fim: '' });
const iniciarNovoRelatorio = () => {
  novoRelatorio.clearErrors();
  Object.assign(novoRelatorio, sugerirPeriodoRelatorio(props.dadosrelat));
  modalPeriodo.value.getBsModal().show();
};
const gerarRelatorio = () => {
  if (novoRelatorio.processing) return;
  novoRelatorio.post(route('sgc.contratada.relatorio.iniciar'), {
    preserveScroll: true,
    onSuccess: () => {
      modalPeriodo.value.getBsModal().hide();
      filtrarRelatorios();
    },
  });
};

// Método para verificar se há históricos para o contrato atual
const hasHistoricosForContrato = (relatorio) => {
  console.log('Verificando históricos para contrato:', props.contrato.id, 'Relatório:', relatorio.relatorio_num, 'Históricos:', relatorio.historicos);
  const hasHistoricos = relatorio.historicos && relatorio.historicos.length > 0 && relatorio.historicos.some(h => h.contrato_id == props.contrato.id);
  console.log('Tem históricos para o contrato?', hasHistoricos);
  return hasHistoricos;
};

// Método para obter a primeira versão de um histórico válido
const getFirstVersionForContrato = (relatorio) => {
  console.log('Obtendo primeira versão para contrato:', props.contrato.id, 'Relatório:', relatorio.relatorio_num, 'Históricos:', relatorio.historicos);
  const historico = relatorio.historicos.find(h => h.contrato_id == props.contrato.id);
  console.log('Primeiro histórico encontrado:', historico);
  return historico ? historico.versao : 0; // Retorna 0 se não houver históricos
};

// Método para obter a versão mais recente
const getLatestVersion = (relatorio) => {
  console.log('Calculando versão mais recente para contrato:', props.contrato.id, 'Relatório:', relatorio.relatorio_num, 'Históricos:', relatorio.historicos);
  if (relatorio.historicos && relatorio.historicos.length > 0) {
    const filteredHistoricos = relatorio.historicos.filter(h => h.contrato_id == props.contrato.id);
    console.log('Históricos filtrados:', filteredHistoricos);
    if (filteredHistoricos.length > 0) {
      return Math.max(...filteredHistoricos.map(h => h.versao)) + 1;
    }
  }
  console.log('Sem históricos válidos, retornando 0');
  return 0;
};

const filteredRelatorios = ref([]);

const filtrarRelatorios = () => {
  console.log('Filtrando relatórios para contrato:', props.contrato.id, 'Dados recebidos:', props.dadosrelat);
  const seen = new Set();
  filteredRelatorios.value = props.dadosrelat.filter(relatorio => {
    if (seen.has(relatorio.relatorio_num)) {
      return false;
    }
    seen.add(relatorio.relatorio_num);
    return true;
  });
};

onMounted(() => {
  console.log('Contrato ID (onMounted):', props.contrato.id);
  filtrarRelatorios();
});
</script>

<template>
  <div>
    <Head :title="`${contrato.contratada.slice(0, 10)}...`" />
    <Modal ref="modalPeriodo" title="Gerar novo relatório" modalDialogClass="modal-dialog-centered">
      <template #body>
        <p class="text-muted mb-3">Confira o próximo período bimestral sugerido. Você pode ajustar as datas antes de gerar.</p>
        <form id="form-novo-relatorio" @submit.prevent="gerarRelatorio">
          <fieldset :disabled="novoRelatorio.processing" class="border-0 p-0 m-0">
            <div class="row g-3">
              <div class="col-sm-6">
                <label for="relatorio-inicio" class="form-label">Data inicial</label>
                <input id="relatorio-inicio" v-model="novoRelatorio.data_inicio" type="date" class="form-control" :class="{ 'is-invalid': novoRelatorio.errors.data_inicio }" required />
                <div v-if="novoRelatorio.errors.data_inicio" class="invalid-feedback">{{ novoRelatorio.errors.data_inicio }}</div>
              </div>
              <div class="col-sm-6">
                <label for="relatorio-fim" class="form-label">Data final</label>
                <input id="relatorio-fim" v-model="novoRelatorio.data_fim" type="date" class="form-control" :class="{ 'is-invalid': novoRelatorio.errors.data_fim }" :min="novoRelatorio.data_inicio || undefined" required />
                <div v-if="novoRelatorio.errors.data_fim" class="invalid-feedback">{{ novoRelatorio.errors.data_fim }}</div>
              </div>
            </div>
            <p v-if="novoRelatorio.errors['contrato.id']" class="text-danger mt-3 mb-0">{{ novoRelatorio.errors['contrato.id'] }}</p>
          </fieldset>
        </form>
      </template>
      <template #footer>
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" :disabled="novoRelatorio.processing">Cancelar</button>
        <button type="submit" form="form-novo-relatorio" class="btn btn-info" :disabled="novoRelatorio.processing">{{ novoRelatorio.processing ? 'Gerando...' : 'Gerar relatório' }}</button>
      </template>
    </Modal>
    <AuthenticatedLayout>
      <template #header>
        <div class="w-100 d-flex justify-content-between">
          <Breadcrumb
            class="align-self-center"
            :links="[
              { route: route('contratos.gestao.listagem', contrato.tipo_contrato), label: `Gestão de Contratos` },
              { route: '#', label: contrato.contratada }
            ]"
          />
          <div>
            <button @click="iniciarNovoRelatorio" class="btn btn-info me-2 w-500">
              <i class="fas fa-file-alt"></i> Gerar Novo Relatório
            </button>
          </div>
        </div>
      </template>

      <NavbarContrato :tipo="contrato" class="relatorios-layout">
        <template #body>
          <div class="card card-body relatorios-conteudo">
            <div class="row mb-3">
              <div class="col-12 text-center">
                <h3 class="titulo-relatorio">
                  RELATÓRIO DE COORDENAÇÃO E EXECUÇÃO DOS SERVIÇOS
                </h3>
              </div>
            </div>
            
            <div class="row">
              <div class="col-12">
                <div class="table-responsive">
                <table class="table table-striped">
                  <thead>
                    <tr style="background-color: #237D9E; text-align: left;">
                      <th scope="col">RELATÓRIO Nº</th>
                      <th scope="col">PERÍODO</th>
                      <th scope="col">STATUS</th>
                      <th scope="col">VERSÕES</th>
                      <th scope="col">ACESSAR</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="relatorio in filteredRelatorios" :key="relatorio.id">
                      <td>{{ relatorio.relatorio_num }}</td>
                      <td>{{ relatorio.periodo }}</td>
                      <td>
                        <span :class="['badge', getStatusBadgeClass(relatorio.status)]">
                          {{ relatorio.status }}
                        </span>
                      </td>
                      <td>
                        <!-- Caso sem históricos: apenas "VER 0" -->
                        <select v-if="!hasHistoricosForContrato(relatorio)" @change="goToVersion(contrato.id, relatorio.relatorio_num, $event.target.value)">
                          <option value="" selected>VER 0</option>
                        </select>
                        <!-- Caso com históricos: "REV" com versão mais recente e "VER 0" -->
                        <select v-else @change="goToVersion(contrato.id, relatorio.relatorio_num, $event.target.value)">
                          <option :value="''" selected>
                            REV {{ getLatestVersion(relatorio) }}
                          </option>
                          <option :value="getFirstVersionForContrato(relatorio)">
                            VER 0
                          </option>
                        </select>
                      </td>
                      <td class="list-unstyled">
                        <NavLink 
                          :route-name="'sgc.contratada.relatorio.detalhes'"
                          :param="[contrato.id, relatorio.relatorio_num]"
                          title="Acessar Relatório"
                          :icon="IconDoorEnter"
                        />
                      </td>
                    </tr>
                  </tbody>
                </table>
                </div>
              </div>
            </div>
          </div>
        </template>
      </NavbarContrato>
    </AuthenticatedLayout>
  </div>
</template>

<style scoped>
.relatorios-layout.card { background: transparent; border: 0; box-shadow: none; padding: 0; }
.relatorios-layout :deep(> .d-flex) { display: grid !important; grid-template-columns: 190px minmax(0, 1fr); gap: 20px; align-items: start; }
.relatorios-layout :deep(> .d-flex > .col-md-1) { width: auto; padding: 12px 8px; border: 1px solid #e2e8f0; border-radius: 8px; background: #fff; }
.relatorios-layout :deep(> .d-flex > .col-md-11) { width: auto; min-width: 0; }
.relatorios-layout :deep(.navbar-nav .nav-link) { padding: 12px 10px; border-radius: 6px; }
.relatorios-layout :deep(.navbar-nav .nav-item.active > .nav-link) { background: #eff6ff; color: #1d4ed8; }
.relatorios-conteudo { min-width: 0; border-color: #e2e8f0; border-radius: 8px; padding: 20px; }
@media (max-width: 1199px) {
  .relatorios-layout :deep(> .d-flex) { grid-template-columns: 170px minmax(0, 1fr); gap: 16px; }
}
@media (max-width: 767px) {
  .relatorios-layout :deep(> .d-flex) { grid-template-columns: 1fr; }
  .relatorios-layout :deep(.navbar-nav) { flex-direction: row; flex-wrap: wrap; gap: 4px; }
  .relatorios-conteudo { padding: 16px; }
}
</style>

<script>
export default {
  methods: {
    // Método para obter a classe do badge de status
    getStatusBadgeClass(status) {
      switch(status) {
        case 'Análise DNIT':
          return 'text-bg-warning';
        case 'Revisão Contratada':
          return 'text-bg-primary';
        case 'Relatório Aprovado':
          return 'text-bg-success';
        default:
          return 'text-bg-secondary';
      }
    },
    // Método para navegar para uma versão específica do relatório
    goToVersion(contratoId, relatorioNum, versao) {
      if (versao) {
        this.$inertia.get(route('sgc.contratada.relatorio.historico', { contrato: contratoId, relatorio_num: relatorioNum, versao: versao }));
      }
    },
    // Método para iniciar um novo relatório
  },
};
</script>
