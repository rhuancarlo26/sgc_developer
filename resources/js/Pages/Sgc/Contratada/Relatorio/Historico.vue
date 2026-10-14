<script setup>
import { computed, ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import NavbarContrato from '../NavbarContrato.vue';
import DocxHistorico from './DocxHistorico.vue';

const props = defineProps({ historico: { type: Array, default: () => [] }, contrato: Object });
const resumo = computed(() => props.historico[0] || null);
const titulo = computed(() => resumo.value ? resumo.value.relatorio_num + 'º RELATÓRIO DE COORDENAÇÃO' : 'Histórico do relatório');
const docxModal = ref(null);
const selectedItemId = ref(null);
const abrirDocxModal = (itemId, versao) => {
  selectedItemId.value = itemId;
  docxModal.value.abrirModal(itemId, props.contrato.id, versao);
};
</script>

<template>
  <Head :title="resumo ? 'Relatório ' + resumo.relatorio_num + ' - Revisão ' + resumo.versao : titulo" />
  <AuthenticatedLayout>
    <template #header>
      <Breadcrumb :links="[
        { route: route('sgc.contratada.relatorios.index', { contrato: contrato.id }), label: contrato.contratada },
        { route: '#', label: 'Versão anterior do relatório' }
      ]" />
    </template>
    <NavbarContrato :tipo="contrato" class="relatorios-layout">
      <template #body>
        <div class="card card-body relatorios-conteudo">
          <div class="historico-acoes">
            <Link :href="route('sgc.contratada.relatorios.index', { contrato: contrato.id })" class="btn btn-sm btn-outline-secondary">Voltar aos relatórios</Link>
            <Link v-if="resumo" :href="route('sgc.contratada.relatorio.detalhes', [contrato.id, resumo.relatorio_num])" class="btn btn-sm btn-outline-primary">Acessar revisão atual</Link>
          </div>
          <header class="historico-cabecalho">
            <h3>{{ titulo }}</h3>
            <div v-if="resumo" class="historico-resumo">
              <span class="badge text-bg-secondary">{{ resumo.versao === 0 ? 'VER 0' : 'REV ' + resumo.versao }}</span>
              <span v-if="resumo.periodo">Período: {{ resumo.periodo }}</span>
            </div>
            <p class="text-muted mb-0">Consulta de uma versão anterior. Os documentos abaixo pertencem à revisão indicada.</p>
          </header>
          <div class="table-responsive">
            <table class="table table-striped align-middle mb-0">
              <thead>
                <tr>
                  <th scope="col">ITEM DO RELATÓRIO</th>
                  <th scope="col" class="text-end">AÇÕES</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="item in historico" :key="item.id">
                  <td>{{ item.nome_topico }}</td>
                  <td class="text-end">
                    <a v-if="item.id_item >= 17" :href="route('sgc.contratada.download_anexo', { contratoId: contrato.id, itemId: item.id_item, relatorioNum: item.relatorio_num, versao: item.versao })" class="btn btn-outline-primary">Anexo</a>
                    <button v-else type="button" class="btn btn-success mr-2" @click="abrirDocxModal(item.id_item, item.versao)">Abrir</button>
                  </td>
                </tr>
                <tr v-if="!historico.length"><td colspan="2" class="text-center text-muted py-4">Nenhum registro disponível nesta revisão.</td></tr>
              </tbody>
            </table>
          </div>
        </div>
      </template>
    </NavbarContrato>
    <DocxHistorico v-if="resumo" ref="docxModal" :itemId="selectedItemId" :comentarios="null" :contrato="contrato" :numRelatorio="resumo.relatorio_num" />
  </AuthenticatedLayout>
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
.historico-acoes { display: flex; justify-content: space-between; flex-wrap: wrap; gap: 10px; }
.historico-cabecalho { text-align: center; padding: 24px 0; }
.historico-cabecalho h3 { font-size: 1.3em; font-weight: 600; color: #0e2236; margin-bottom: 12px; }
.historico-resumo { display: flex; justify-content: center; align-items: center; flex-wrap: wrap; gap: 12px; margin-bottom: 12px; }
.historico-cabecalho p { font-size: .875rem; }
.table th { white-space: nowrap; }
.table td { padding-top: 12px; padding-bottom: 12px; }
</style>
