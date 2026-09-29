<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import NavbarContrato from '@/Pages/Sgc/Contratada/NavbarContrato.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import { Link } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import PlanilhasSimplificadasVisualizar from './PlanilhasSimplificadasVisualizar.vue';
const props = defineProps({ campanha: Object, campanha_id: [Number, String], contrato: [Number, String], produto: String, contratos: Object, canApprove: Boolean });
const statusExibicao = computed(() => props.campanha?.status === 'Rejeitada' ? 'Reprovada' : props.campanha?.status);
const showAnalysisModal = ref(false);
</script>

<template>
    <AuthenticatedLayout>
        <template #header><Breadcrumb :links="[{ route: route('sgc.gestao.listagem', props.contratos.tipo_contrato), label: 'Gestão de Contratos' }, { route: route('sgc.contratada.produtos.index', [props.contrato, 'fauna']), label: props.contratos.contratada }, { route: '#', label: 'Visualizar campanha simplificada de Fauna' }]" /></template>
        <NavbarContrato :tipo="{ id: props.contrato }"><template #body>
            <div class="card"><div class="card-body">
                <div v-if="campanha.analises?.length" class="alert alert-info mb-3 d-flex justify-content-between align-items-center analysis-trigger" @click="showAnalysisModal = true"><span><i class="bi bi-info-circle me-2"></i>{{ campanha.analises.length }} análise{{ campanha.analises.length !== 1 ? 's' : '' }} registrada{{ campanha.analises.length !== 1 ? 's' : '' }}</span><span class="badge bg-info text-white">Clique para visualizar</span></div>
                <div class="text-center mb-4"><h2 class="mb-2">VISUALIZAR CAMPANHA SIMPLIFICADA DE FAUNA</h2><span class="badge fs-5 px-3 py-2 fw-bold" :class="{ 'bg-success text-white': campanha.status === 'Aprovada', 'bg-danger text-white': campanha.status === 'Rejeitada', 'bg-warning text-dark': campanha.status === 'Em análise', 'bg-secondary text-white': campanha.status === 'Em elaboração' }">{{ statusExibicao }}</span></div>
                <div class="row g-3 mb-4">
                    <div class="col-md-3"><div class="info-box"><span>Campanha</span><strong>{{ campanha.id_campanha || 'N/A' }}</strong></div></div>
                    <div class="col-md-3"><div class="info-box"><span>Empreendimento</span><strong>{{ campanha.cod_emp || 'N/A' }}</strong></div></div>
                    <div class="col-md-3"><div class="info-box"><span>SEI DNIT</span><strong>{{ campanha.sei_dnit || 'N/A' }}</strong></div></div>
                    <div class="col-md-3"><div class="info-box"><span>Subproduto</span><strong>{{ campanha.subproduto || 'N/A' }}</strong></div></div>
                </div>
                <PlanilhasSimplificadasVisualizar :planilhas="campanha.planilhas" />
                <section class="mt-4"><h4 class="section-title">Fotos</h4><div v-if="campanha.fotos?.length" class="photo-grid"><a v-for="foto in campanha.fotos" :key="foto.id" :href="foto.url" target="_blank" class="photo-card"><img v-if="foto.url" :src="foto.url" :alt="foto.nome_arquivo || 'Foto da campanha'"><div v-else class="photo-placeholder">Sem imagem</div><div class="photo-meta"><strong>{{ foto.nome_arquivo || 'Foto' }}</strong><span v-if="foto.data_captura">{{ foto.data_captura }}</span><span v-if="foto.latitude && foto.longitude">{{ foto.latitude }}, {{ foto.longitude }}</span><span v-if="foto.descricao">{{ foto.descricao }}</span></div></a></div><div v-else class="empty-state">Nenhuma foto vinculada.</div></section>
                <section class="mt-4"><h4 class="section-title">Anexos</h4><div v-if="campanha.anexos?.length" class="list-group"><a v-for="anexo in campanha.anexos" :key="anexo.id" :href="anexo.url" target="_blank" class="list-group-item list-group-item-action d-flex justify-content-between"><span>{{ anexo.nome_arquivo }}</span><span class="text-primary">Abrir</span></a></div><div v-else class="empty-state">Nenhum anexo vinculado.</div></section>
                <div class="mt-4 pt-3 border-top"><Link v-if="['Rejeitada', 'Em elaboração'].includes(campanha.status)" :href="route('sgc.contratada.produtos.fauna.simplificada.edit', [props.contrato, 'fauna', campanha.id])" class="btn btn-primary me-2">Editar campanha</Link><Link v-if="props.canApprove" :href="route('sgc.contratada.produtos.fauna.simplificada.analise', [props.contrato, 'fauna', campanha.id])" class="btn btn-success">Ir para análise</Link></div>
            </div></div>
            <div v-if="showAnalysisModal" class="modal-backdrop-custom" @click.self="showAnalysisModal = false"><div class="analysis-modal"><div class="d-flex justify-content-between align-items-center mb-3"><h5 class="mb-0">Histórico de análises</h5><button type="button" class="btn-close" @click="showAnalysisModal = false"></button></div><div v-for="analise in campanha.analises" :key="analise.id" class="analysis-card" :class="analise.status === 'Rejeitada' ? 'analysis-rejected' : 'analysis-approved'"><div class="d-flex justify-content-between gap-3"><strong>Versão {{ analise.versao }} — {{ analise.status === 'Rejeitada' ? 'Reprovada' : analise.status }}</strong><small>{{ analise.created_at }}</small></div><small v-if="analise.fiscal?.name" class="d-block mt-1">Fiscal: {{ analise.fiscal.name }}</small><p v-if="analise.observacoes" class="mb-0 mt-2">{{ analise.observacoes }}</p></div></div></div>
        </template></NavbarContrato>
    </AuthenticatedLayout>
</template>

<style scoped>
.info-box { border: 1px solid #e2e4e6; border-radius: 6px; padding: 12px; min-height: 82px; }.info-box span { display: block; color: #6c757d; font-size: .85rem; margin-bottom: 6px; }.info-box strong { overflow-wrap: anywhere; }.section-title { font-size: 1.05rem; font-weight: 700; margin-bottom: 12px; }.analysis-trigger { cursor: pointer; }.photo-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(190px, 1fr)); gap: 14px; }.photo-card { border: 1px solid #e2e4e6; border-radius: 8px; overflow: hidden; color: inherit; text-decoration: none; background: #fff; }.photo-card img { width: 100%; height: 145px; object-fit: cover; display: block; }.photo-placeholder { height: 145px; display: grid; place-items: center; background: #f1f3f5; color: #6c757d; }.photo-meta { display: flex; flex-direction: column; gap: 3px; padding: 10px; font-size: .8rem; }.photo-meta strong { font-size: .85rem; overflow-wrap: anywhere; }.empty-state { border: 1px dashed #c9ced3; border-radius: 6px; color: #6c757d; padding: 16px; text-align: center; }.modal-backdrop-custom { position: fixed; inset: 0; z-index: 1055; background: rgba(0,0,0,.65); display: grid; place-items: center; padding: 24px; }.analysis-modal { width: min(760px,100%); max-height: 90vh; overflow: auto; background: #fff; border-radius: 8px; padding: 20px; }.analysis-card { border: 1px solid; border-radius: 6px; padding: 12px; margin-bottom: 10px; }.analysis-rejected { background: #fff5f5; border-color: #f1b7b7; color: #842029; }.analysis-approved { background: #f3fbf5; border-color: #b9dfc0; color: #1f6130; }
</style>
