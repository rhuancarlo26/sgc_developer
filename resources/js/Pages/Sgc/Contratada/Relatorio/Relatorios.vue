<script setup>
import { formatarDataRelatorio } from './dataRelatorio';
import { Head } from "@inertiajs/vue3";
import { onMounted, ref } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import Modal from '@/Components/Modal.vue';
import { sugerirPeriodoRelatorio } from './periodoRelatorio';
import NavbarContrato from "../NavbarContrato.vue";
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { defineProps } from 'vue';
import { IconDoorEnter, IconPencil, IconInfoCircle } from "@tabler/icons-vue";

const user = usePage().props.auth.user;

const props = defineProps({
  contrato: Object,
  dadosrelat: { type: Array },
  pode_editar_periodo: { type: Boolean, default: false }
});

const form = ref({
  id: null,
  contrato_id: props.contrato.id,
  item_id: null
});

const modalAprovacao = ref(null);
const aprovacaoSelecionada = ref(null);
const verAprovacao = relatorio => {
  aprovacaoSelecionada.value = { numero: relatorio.relatorio_num, dados: relatorio.aprovacao };
  modalAprovacao.value.getBsModal().show();
};

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

const modalEdicaoPeriodo = ref(null);
const numeroEditando = ref(null);
const editarPeriodo = useForm({ data_inicio: '', data_fim: '' });
const abrirEdicaoPeriodo = relatorio => {
  numeroEditando.value = relatorio.relatorio_num;
  editarPeriodo.reset();
  editarPeriodo.clearErrors();
  const datas = String(relatorio.periodo || '').match(/^(\d{2})\/(\d{2})\/(\d{4})\s+(?:a|à)\s+(\d{2})\/(\d{2})\/(\d{4})$/);
  if (datas) {
    editarPeriodo.data_inicio = datas[3] + '-' + datas[2] + '-' + datas[1];
    editarPeriodo.data_fim = datas[6] + '-' + datas[5] + '-' + datas[4];
  }
  modalEdicaoPeriodo.value.getBsModal().show();
};
const salvarPeriodo = () => {
  if (editarPeriodo.processing) return;
  editarPeriodo.patch(route('sgc.contratada.relatorio.periodo.update', { contrato: props.contrato.id, relatorio_num: numeroEditando.value }), {
    preserveScroll: true,
    onSuccess: () => { modalEdicaoPeriodo.value.getBsModal().hide(); filtrarRelatorios(); },
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
const getVersoesAnteriores = (relatorio) => {
  return [...new Set((relatorio.historicos || [])
    .filter(h => Number(h.contrato_id) === Number(props.contrato.id))
    .map(h => Number(h.versao))
    .filter(v => Number.isInteger(v) && v >= 0))].sort((a, b) => b - a);
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
    <Modal v-if="pode_editar_periodo" ref="modalEdicaoPeriodo" :title="'Editar período · Relatório ' + numeroEditando" modalDialogClass="modal-dialog-centered">
      <template #body>
        <p class="text-muted">A correção será aplicada ao relatório e às suas revisões. A alteração ficará registrada no histórico de auditoria.</p>
        <form id="form-editar-periodo" @submit.prevent="salvarPeriodo">
          <fieldset :disabled="editarPeriodo.processing" class="border-0 p-0 m-0">
            <div class="row g-3">
              <div class="col-sm-6">
                <label for="editar-periodo-inicio" class="form-label">Data inicial</label>
                <input id="editar-periodo-inicio" v-model="editarPeriodo.data_inicio" type="date" class="form-control" :class="{ 'is-invalid': editarPeriodo.errors.data_inicio }" required />
                <div class="invalid-feedback">{{ editarPeriodo.errors.data_inicio }}</div>
              </div>
              <div class="col-sm-6">
                <label for="editar-periodo-fim" class="form-label">Data final</label>
                <input id="editar-periodo-fim" v-model="editarPeriodo.data_fim" type="date" class="form-control" :min="editarPeriodo.data_inicio || undefined" :class="{ 'is-invalid': editarPeriodo.errors.data_fim }" required />
                <div class="invalid-feedback">{{ editarPeriodo.errors.data_fim }}</div>
              </div>
            </div>
          </fieldset>
        </form>
      </template>
      <template #footer>
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" :disabled="editarPeriodo.processing">Cancelar</button>
        <button type="submit" form="form-editar-periodo" class="btn btn-primary" :disabled="editarPeriodo.processing">{{ editarPeriodo.processing ? 'Salvando...' : 'Salvar período' }}</button>
      </template>
    </Modal>
    <Modal ref="modalAprovacao" :title="'Aprovação · Relatório ' + (aprovacaoSelecionada?.numero || '')" modalDialogClass="modal-dialog-centered">
      <template #body>
        <dl v-if="aprovacaoSelecionada" class="mb-0">
          <dt class="text-muted small mb-1">Aprovado por</dt>
          <dd class="mb-3">{{ aprovacaoSelecionada.dados.user?.name || 'Responsável não registrado' }}</dd>
          <dt class="text-muted small mb-1">Data e hora</dt>
          <dd class="mb-0">{{ formatarDataRelatorio(aprovacaoSelecionada.dados.created_at, 'Data não registrada') }}</dd>
        </dl>
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
                <table class="table table-striped align-middle relatorios-tabela">
                  <colgroup>
                    <col class="col-numero" />
                    <col class="col-periodo" />
                    <col class="col-status" />
                    <col class="col-aprovacao" />
                    <col class="col-versao" />
                    <col class="col-acesso" />
                  </colgroup>
                  <thead>
                    <tr>
                      <th scope="col" class="text-center">Relatório</th>
                      <th scope="col">Período</th>
                      <th scope="col" class="text-center">Status</th>
                      <th scope="col">Aprovação</th>
                      <th scope="col" class="text-center">Revisão</th>
                      <th scope="col" class="text-center">Acessar</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="relatorio in filteredRelatorios" :key="relatorio.id">
                      <td class="relatorio-numero text-center">{{ relatorio.relatorio_num }}º</td>
                      <td class="relatorio-periodo">
                        <div class="periodo-conteudo">
                          <span>{{ relatorio.periodo }}</span>
                          <button v-if="pode_editar_periodo" type="button" class="btn-editar-periodo" :aria-label="'Editar período do relatório ' + relatorio.relatorio_num" title="Editar período" @click="abrirEdicaoPeriodo(relatorio)"><IconPencil :size="16" /></button>
                        </div>
                      </td>
                      <td class="text-center">
                        <span :class="['badge', getStatusBadgeClass(relatorio.status)]">
                          {{ relatorio.status }}
                        </span>

                      </td>
                      <td class="relatorio-aprovacao">
                        <template v-if="relatorio.status === 'Relatório Aprovado'">
                          <button v-if="relatorio.aprovacao" type="button" class="aprovacao-resumo" :aria-label="'Ver detalhes da aprovação do relatório ' + relatorio.relatorio_num" title="Ver responsável e data da aprovação" @click="verAprovacao(relatorio)">
                            <span>{{ relatorio.aprovacao.user?.name || 'Ver aprovação' }}</span>
                            <IconInfoCircle :size="15" aria-hidden="true" />
                          </button>
                          <span v-else class="text-muted" title="Responsável e data da aprovação não foram registrados.">Não registrada</span>
                        </template>
                        <span v-else class="text-muted" aria-label="Sem aprovação">—</span>
                      </td>
                      <td class="text-center">
                        <!-- Caso sem históricos: apenas "VER 0" -->
                        <select class="form-select form-select-sm relatorio-versao" :aria-label="'Versão do relatório ' + relatorio.relatorio_num" v-if="!hasHistoricosForContrato(relatorio)" @change="goToVersion(contrato.id, relatorio.relatorio_num, $event.target.value)">
                          <option value="" selected>VER 0</option>
                        </select>
                        <!-- Caso com históricos: "REV" com versão mais recente e "VER 0" -->
                        <select v-else class="form-select form-select-sm relatorio-versao" :aria-label="'Versão do relatório ' + relatorio.relatorio_num" @change="goToVersion(contrato.id, relatorio.relatorio_num, $event.target.value)">
                          <option :value="''" selected>
                            REV {{ getLatestVersion(relatorio) }}
                          </option>
                          <option v-for="versao in getVersoesAnteriores(relatorio)" :key="versao" :value="versao">
                            {{ versao === 0 ? 'VER 0' : 'REV ' + versao }}
                          </option>
                        </select>
                      </td>
                      <td class="text-center">
                        <Link v-if="can('sgc.contratada.relatorio.detalhes')"
                          :href="route('sgc.contratada.relatorio.detalhes', [contrato.id, relatorio.relatorio_num])"
                          class="btn btn-sm btn-outline-primary relatorio-acesso"
                          :aria-label="'Acessar relatório ' + relatorio.relatorio_num">
                          <IconDoorEnter :size="16" aria-hidden="true" /> Acessar
                        </Link>
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
.relatorios-layout :deep(> .d-flex > :first-child) { width: auto; padding: 12px 8px; border: 1px solid #e2e8f0; border-radius: 8px; background: #fff; }
.relatorios-layout :deep(> .d-flex > :last-child) { width: auto; min-width: 0; }
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

.relatorios-tabela { width: 100%; min-width: 940px; table-layout: fixed; margin-bottom: 0; }
.relatorios-tabela .col-numero { width: 9%; }
.relatorios-tabela .col-periodo { width: 24%; }
.relatorios-tabela .col-status { width: 19%; }
.relatorios-tabela .col-aprovacao { width: 24%; }
.relatorios-tabela .col-versao, .relatorios-tabela .col-acesso { width: 12%; }
.relatorios-tabela th, .relatorios-tabela td { padding: 12px 16px; vertical-align: middle; text-align: center; }
.relatorios-tabela th { color: #475569; background: #f8fafc; font-size: .75rem; font-weight: 600; text-transform: uppercase; letter-spacing: .025em; white-space: nowrap; border-bottom: 1px solid #cbd5e1; }
.relatorios-tabela td { height: 50px; padding-top: 8px; padding-bottom: 8px; font-size: .8125rem; line-height: 1.45; }
.relatorios-tabela .badge { font-size: .75rem; }
.relatorios-tabela .relatorio-numero { font-weight: 600; color: #334155; }
.relatorio-periodo { white-space: nowrap; }
.aprovacao-resumo { display: inline-flex; align-items: center; gap: 6px; max-width: 100%; padding: 2px 0; border: 0; background: transparent; color: #334155; font: inherit; cursor: pointer; }
.aprovacao-resumo span { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.aprovacao-resumo :deep(svg) { flex-shrink: 0; color: #64748b; }
.aprovacao-resumo:hover { color: #2563eb; }
.aprovacao-resumo:focus-visible { outline: 2px solid #3b82f6; outline-offset: 3px; border-radius: 3px; }
.relatorio-versao { width: 104px; max-width: 100%; min-height: 34px; margin-inline: auto; font-size: .75rem; }
.relatorio-acesso { display: inline-flex; align-items: center; justify-content: center; gap: 6px; min-height: 34px; font-size: .75rem; white-space: nowrap; }
.relatorios-tabela tbody tr:hover > td { background-color: #eff6ff; }
.periodo-conteudo { display: flex; align-items: center; justify-content: center; gap: 8px; }
.btn-editar-periodo { display: inline-flex; align-items: center; justify-content: center; width: 30px; height: 30px; padding: 0; border: 1px solid #cbd5e1; border-radius: 4px; background: #fff; color: #475569; flex-shrink: 0; }
.btn-editar-periodo:hover, .btn-editar-periodo:focus-visible { border-color: #3b82f6; color: #2563eb; background: #eff6ff; }
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
