<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import NavbarContrato from '@/Pages/Sgc/Contratada/NavbarContrato.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import { Link } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { formatarDataCampanha } from './datasCampanha';
import PlanilhasSimplificadasVisualizar from './PlanilhasSimplificadasVisualizar.vue';
import AnexosFaunaVisualizar from './AnexosFaunaVisualizar.vue';
const props = defineProps({ campanha: Object, campanha_id: [Number, String], contrato: [Number, String], produto: String, contratos: Object, canApprove: Boolean });
const statusExibicao = computed(() => props.campanha?.status === 'Rejeitada' ? 'Reprovada' : props.campanha?.status);
const showAnalysisModal = ref(false);
</script>

<template>
    <AuthenticatedLayout>
        <template #header><Breadcrumb :links="[{ route: route('sgc.gestao.listagem', props.contratos.tipo_contrato), label: 'Gestão de Contratos' }, { route: route('sgc.contratada.produtos.index', [props.contrato, 'fauna']), label: props.contratos.contratada }, { route: '#', label: 'Visualizar campanha simplificada de Fauna' }]" /></template>
        <NavbarContrato :tipo="{ id: props.contrato }" class="fauna-visualizar-layout"><template #body>
            <div class="campanha-conteudo"><div class="campanha-blocos">
                <section class="campanha-bloco campanha-resumo" aria-label="Dados da campanha">
                <div v-if="campanha.analises?.length" class="alert alert-info mb-3 d-flex justify-content-between align-items-center analysis-trigger" @click="showAnalysisModal = true"><span><i class="bi bi-info-circle me-2"></i>{{ campanha.analises.length }} análise{{ campanha.analises.length !== 1 ? 's' : '' }} registrada{{ campanha.analises.length !== 1 ? 's' : '' }}</span><span class="badge bg-info text-white">Clique para visualizar</span></div>
                <div class="text-center mb-4"><h2 class="mb-2">VISUALIZAR CAMPANHA SIMPLIFICADA DE FAUNA</h2><span class="badge fs-5 px-3 py-2 fw-bold" :class="{ 'bg-success text-white': campanha.status === 'Aprovada', 'bg-danger text-white': campanha.status === 'Rejeitada', 'bg-warning text-white': campanha.status === 'Em análise', 'bg-secondary text-white': campanha.status === 'Em elaboração' }">{{ statusExibicao }}</span></div>
                <div class="row g-3 mb-4">
                    <div class="col-md-3"><div class="info-box"><span>Campanha</span><strong>{{ campanha.id_campanha || 'N/A' }}</strong></div></div>
                    <div class="col-md-3"><div class="info-box"><span>Empreendimento</span><strong>{{ campanha.cod_emp || 'N/A' }}</strong></div></div>
                    <div class="col-md-3"><div class="info-box"><span>Data inicial</span><strong>{{ formatarDataCampanha(campanha.data_ini) }}</strong></div></div><div class="col-md-3"><div class="info-box"><span>Data final</span><strong>{{ formatarDataCampanha(campanha.data_fim) }}</strong></div></div><div class="col-md-3"><div class="info-box"><span>SEI DNIT</span><strong>{{ campanha.sei_dnit || 'N/A' }}</strong></div></div>
                    <div class="col-md-3"><div class="info-box"><span>Subproduto</span><strong>{{ campanha.subproduto || 'N/A' }}</strong></div></div>
                </div>
                </section>
                <section class="campanha-bloco bloco-planilhas" aria-label="Planilhas de resultados">
                <PlanilhasSimplificadasVisualizar :planilhas="campanha.planilhas" :subproduto="campanha.subproduto" />
                </section>
                <section class="campanha-bloco bloco-fotos"><h4 class="section-title">Fotos</h4><div v-if="campanha.fotos?.length" class="photo-grid"><a v-for="foto in campanha.fotos" :key="foto.id" :href="foto.url" target="_blank" rel="noopener" class="photo-card"><img v-if="foto.url" loading="lazy" :src="foto.url" :alt="foto.nome_arquivo || 'Foto da campanha'"><div v-else class="photo-placeholder">Sem imagem</div><div class="photo-meta"><strong :title="foto.nome_arquivo">{{ foto.nome_arquivo || 'Foto' }}</strong><span v-if="foto.data_captura">{{ foto.data_captura }}</span><span v-if="foto.latitude && foto.longitude">{{ foto.latitude }}, {{ foto.longitude }}</span><span v-if="foto.descricao">{{ foto.descricao }}</span></div></a></div><div v-else class="empty-state">Nenhuma foto vinculada.</div></section>
                <div class="campanha-bloco bloco-anexos"><AnexosFaunaVisualizar :anexos="campanha.anexos" /></div>
                <div v-if="props.canApprove || ['Rejeitada', 'Em elaboração'].includes(campanha.status)" class="campanha-acoes"><Link v-if="['Rejeitada', 'Em elaboração'].includes(campanha.status)" :href="route('sgc.contratada.produtos.fauna.simplificada.edit', [props.contrato, 'fauna', campanha.id])" class="btn btn-primary me-2">Editar campanha</Link><Link v-if="props.canApprove" :href="route('sgc.contratada.produtos.fauna.simplificada.analise', [props.contrato, 'fauna', campanha.id])" class="btn btn-success">Ir para análise</Link></div>
            </div></div>
            <div v-if="showAnalysisModal" class="modal-backdrop-custom" @click.self="showAnalysisModal = false"><div class="analysis-modal"><div class="d-flex justify-content-between align-items-center mb-3"><h5 class="mb-0">Histórico de análises</h5><button type="button" class="btn-close" @click="showAnalysisModal = false"></button></div><div v-for="analise in campanha.analises" :key="analise.id" class="analysis-card" :class="analise.status === 'Rejeitada' ? 'analysis-rejected' : 'analysis-approved'"><div class="d-flex justify-content-between gap-3"><strong>Versão {{ analise.versao }} — {{ analise.status === 'Rejeitada' ? 'Reprovada' : analise.status }}</strong><small>{{ analise.created_at }}</small></div><small v-if="analise.fiscal?.name" class="d-block mt-1">Fiscal: {{ analise.fiscal.name }}</small><p v-if="analise.observacoes" class="mb-0 mt-2">{{ analise.observacoes }}</p></div></div></div>
        </template></NavbarContrato>
    </AuthenticatedLayout>
</template>

<style scoped>

.fauna-visualizar-layout.card { background: transparent; border: 0; box-shadow: none; padding: 0; }
.fauna-visualizar-layout :deep(> .d-flex) { display: grid !important; grid-template-columns: 190px minmax(0, 1fr); gap: 20px; align-items: start; }
.fauna-visualizar-layout :deep(> .d-flex > .col-md-1) { width: auto; padding: 12px 8px; border: 1px solid #e2e8f0; border-radius: 8px; background: #fff; }
.fauna-visualizar-layout :deep(> .d-flex > .col-md-11) { width: auto; min-width: 0; }
.fauna-visualizar-layout :deep(.navbar-nav .nav-link) { padding: 12px 10px; border-radius: 6px; }
.fauna-visualizar-layout :deep(.navbar-nav .nav-item.active > .nav-link) { background: #eff6ff; color: #1d4ed8; }
.campanha-conteudo { min-width: 0; }
.campanha-blocos { display: flex; flex-direction: column; gap: 22px; }
.campanha-bloco { min-width: 0; padding: 24px; border: 1px solid #e2e8f0; border-radius: 8px; background: #fff; }
.campanha-resumo .row { margin-bottom: 0 !important; }
.campanha-resumo .info-box { height: 100%; background: #f8fafc; border-color: #e2e8f0; }
.campanha-bloco .section-title, .campanha-bloco :deep(.section-title), .bloco-planilhas :deep(section > h4) { margin: 0 0 18px; padding-bottom: 14px; border-bottom: 1px solid #e2e8f0; color: #0f172a; font-size: 1.05rem; font-weight: 600; }
.bloco-anexos :deep(section.mt-4) { margin-top: 0 !important; }
.bloco-anexos :deep(.anexos-grid) { align-items: start; }
.bloco-planilhas :deep(.nav-tabs) { margin-bottom: 18px !important; }
.campanha-acoes { display: flex; justify-content: flex-end; gap: 12px; padding: 16px 24px; border: 1px solid #e2e8f0; border-radius: 8px; background: #fff; }
@media (max-width: 1199px) { .fauna-visualizar-layout :deep(> .d-flex) { grid-template-columns: 170px minmax(0, 1fr); gap: 16px; } }
@media (max-width: 767px) {
    .fauna-visualizar-layout :deep(> .d-flex) { grid-template-columns: 1fr; }
    .fauna-visualizar-layout :deep(.navbar-nav) { flex-direction: row; flex-wrap: wrap; gap: 4px; }
    .campanha-bloco { padding: 16px; }
    .campanha-blocos { gap: 16px; }
    .campanha-acoes { flex-wrap: wrap; padding: 16px; }
}

.badge.bg-warning { color: #ffffff !important; }
.info-box { border: 1px solid #e2e4e6; border-radius: 6px; padding: 12px; min-height: 82px; }.info-box span { display: block; color: #6c757d; font-size: .85rem; margin-bottom: 6px; }.info-box strong { overflow-wrap: anywhere; }.section-title { font-size: 1.05rem; font-weight: 700; margin-bottom: 12px; }.analysis-trigger { cursor: pointer; }.photo-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 16px; }.photo-card { border: 1px solid #e2e4e6; border-radius: 8px; overflow: hidden; color: inherit; text-decoration: none; background: #fff; }.photo-card img { width: 100%; height: 180px; object-fit: cover; display: block; }.photo-placeholder { height: 180px; display: grid; place-items: center; background: #f1f3f5; color: #6c757d; }.photo-meta { display: flex; flex-direction: column; gap: 3px; padding: 10px; font-size: .8rem; }.photo-meta strong { font-size: .85rem; overflow-wrap: anywhere; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }.photo-meta span { overflow-wrap: anywhere; color: #475569; }.empty-state { border: 1px dashed #c9ced3; border-radius: 6px; color: #6c757d; padding: 16px; text-align: center; }.modal-backdrop-custom { position: fixed; inset: 0; z-index: 1055; background: rgba(0,0,0,.65); display: grid; place-items: center; padding: 24px; }.analysis-modal { width: min(760px,100%); max-height: 90vh; overflow: auto; background: #fff; border-radius: 8px; padding: 20px; }.analysis-card { border: 1px solid; border-radius: 6px; padding: 12px; margin-bottom: 10px; }.analysis-rejected { background: #fff5f5; border-color: #f1b7b7; color: #842029; }.analysis-approved { background: #f3fbf5; border-color: #b9dfc0; color: #1f6130; }
</style>
