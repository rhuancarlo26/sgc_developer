<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import NavbarContrato from '@/Pages/Sgc/Contratada/NavbarContrato.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import PlanilhasSimplificadasVisualizar from './PlanilhasSimplificadasVisualizar.vue';
const props = defineProps({ campanha: Object, campanha_id: [Number, String], contrato: [Number, String], produto: String, contratos: Object, canApprove: Boolean });
const statusExibicao = computed(() => props.campanha?.status === 'Rejeitada' ? 'Reprovada' : props.campanha?.status);
</script>

<template>
    <AuthenticatedLayout>
        <template #header><Breadcrumb :links="[{ route: route('sgc.gestao.listagem', props.contratos.tipo_contrato), label: 'Gestão de Contratos' }, { route: route('sgc.contratada.produtos.index', [props.contrato, 'fauna']), label: props.contratos.contratada }, { route: '#', label: 'Visualizar campanha simplificada de Fauna' }]" /></template>
        <NavbarContrato :tipo="{ id: props.contrato }"><template #body>
            <div class="card"><div class="card-body">
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
        </template></NavbarContrato>
    </AuthenticatedLayout>
</template>

<style scoped>
.info-box { border: 1px solid #e2e4e6; border-radius: 6px; padding: 12px; min-height: 82px; }.info-box span { display: block; color: #6c757d; font-size: .85rem; margin-bottom: 6px; }.info-box strong { overflow-wrap: anywhere; }.section-title { font-size: 1.05rem; font-weight: 700; margin-bottom: 12px; }.photo-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(190px, 1fr)); gap: 14px; }.photo-card { border: 1px solid #e2e4e6; border-radius: 8px; overflow: hidden; color: inherit; text-decoration: none; background: #fff; }.photo-card img { width: 100%; height: 145px; object-fit: cover; display: block; }.photo-placeholder { height: 145px; display: grid; place-items: center; background: #f1f3f5; color: #6c757d; }.photo-meta { display: flex; flex-direction: column; gap: 3px; padding: 10px; font-size: .8rem; }.photo-meta strong { font-size: .85rem; overflow-wrap: anywhere; }.empty-state { border: 1px dashed #c9ced3; border-radius: 6px; color: #6c757d; padding: 16px; text-align: center; }
</style>
