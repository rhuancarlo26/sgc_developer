<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import NavLink from '@/Components/NavLink.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import { IconClipboardList, IconCalendar, IconNotes, IconDeviceAnalytics, IconPlane, IconLayoutDashboard } from '@tabler/icons-vue';
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { useToast } from 'vue-toastification';
import CardFotos from '../../Modulos/Importador/Components/CardFotos.vue';
import CardAnexos from '../../Modulos/Importador/Components/CardAnexos.vue';
import PlanilhasSimplificadas from './PlanilhasSimplificadas.vue';

const props = defineProps({
    contrato: [String, Number], produto: { type: String, default: 'Fauna' }, campanha: { type: Object, default: null },
    contratos: { type: Object, default: () => ({}) }, modulos: { type: Array, default: () => [] },
    empreendimentos: { type: Array, default: () => [] }, subproduto: { type: [String, Number], default: null },
});

const tiposPlanilha = ['terrestre', 'aquatica', 'cavernicola'];
const planilhasIniciais = Object.fromEntries(tiposPlanilha.map((tipo) => {
    const existente = props.campanha?.planilhas?.[tipo] ?? null;
    return [tipo, { modulo_id: existente?.modulo_id ?? null, arquivo: null, remover: false, existente }];
}));

const toast = useToast();
const fotosRef = ref(null);
const anexosRef = ref(null);
const planilhasRef = ref(null);
const showAnalysisModal = ref(false);
const errors = ref({ cod_emp: '', id_campanha: '' });
const form = useForm({
    contrato_id: props.contrato, cod_emp: props.campanha?.cod_emp ?? '', id_campanha: props.campanha?.id_campanha ?? null,
    sei_dnit: props.campanha?.sei_dnit ?? '', subproduto: props.campanha?.subproduto ?? props.subproduto,
    planilhas: planilhasIniciais, fotos: props.campanha?.fotos ?? [], anexos: props.campanha?.anexos ?? [], enviar_analise: false,
});

const validar = () => {
    errors.value = { cod_emp: '', id_campanha: '' };
    if (!form.cod_emp) errors.value.cod_emp = 'Selecione o empreendimento para continuar.';
    if (!form.id_campanha || Number(form.id_campanha) < 1) errors.value.id_campanha = 'Informe o ID da campanha para continuar.';
    if (Object.values(errors.value).some(Boolean)) { toast.error('Preencha os campos obrigatórios antes de salvar.'); return false; }
    if (planilhasRef.value?.validarCampos?.() || fotosRef.value?.validarCampos?.() || anexosRef.value?.validarCampos?.()) { toast.error('Revise os dados das planilhas, fotos e anexos antes de salvar.'); return false; }
    return true;
};

const salvar = (enviarAnalise) => {
    if (!validar()) return;
    form.enviar_analise = enviarAnalise;
    const url = props.campanha
        ? route('sgc.contratada.produtos.fauna.simplificada.update', [props.contrato, 'fauna', props.campanha.id])
        : route('sgc.contratada.produtos.store', [props.contrato, 'fauna']);
    form.post(url, { forceFormData: true, preserveState: true, onSuccess: () => toast.success(enviarAnalise ? 'Campanha enviada para análise.' : 'Rascunho salvo com sucesso.') });
};
</script>

<template>
    <AuthenticatedLayout>
        <template #header><Breadcrumb :links="[{ route: route('sgc.gestao.listagem', props.contratos.tipo_contrato), label: 'Gestão de Contratos' }, { route: route('sgc.contratada.produtos.index', [props.contrato, 'fauna']), label: props.contratos.contratada }, { route: '#', label: props.campanha ? 'Editar campanha simplificada de Fauna' : 'Cadastrar campanha simplificada de Fauna' }]" /></template>
        <div class="d-flex">
            <aside class="me-3 menu-column"><div class="card"><div class="card-body p-2"><ul class="navbar-nav mb-0">
                <li><NavLink :route-name="'sgc.contratada.relatorios.index'" :param="props.contrato" title="Relatório de Coordenação" :icon="IconClipboardList" /></li><li><NavLink :route-name="'sgc.contratada.produtos.index'" :param="[props.contrato, 'fauna']" title="Produtos" :icon="IconLayoutDashboard" /></li><li><NavLink :route-name="'sgc.contratada.cronograma.index'" :param="props.contrato" title="Cronograma Físico" :icon="IconCalendar" /></li><li><NavLink :route-name="'sgc.contratada.quantitativos.index'" :param="props.contrato" title="Quantitativos" :icon="IconDeviceAnalytics" /></li><li><NavLink :route-name="'sgc.gestao.listagemDav'" :param="props.contrato" title="DAV" :icon="IconPlane" /></li><li><NavLink :route-name="'sgc.contratada.ficha.index'" :param="props.contrato" title="Ficha Contratual" :icon="IconNotes" /></li>
            </ul></div></div></aside>
            <main class="flex-fill content-column">
                <div v-if="props.campanha?.analises?.length" class="alert alert-info mb-3 d-flex justify-content-between align-items-center analysis-trigger" @click="showAnalysisModal = true"><span><i class="bi bi-info-circle me-2"></i>{{ props.campanha.analises.length }} análise{{ props.campanha.analises.length !== 1 ? 's' : '' }} registrada{{ props.campanha.analises.length !== 1 ? 's' : '' }}</span><span class="badge bg-info text-white">Clique para visualizar</span></div>
                <div class="card mb-3"><div class="card-body text-center"><h2 class="mb-2">{{ props.campanha ? 'EDITAR' : 'CADASTRAR' }} CAMPANHA SIMPLIFICADA DE FAUNA</h2><p class="text-muted mb-0 fs-5">{{ form.subproduto || 'Subproduto não informado' }}</p></div></div>
                <div class="card mb-3"><div class="card-header"><h3 class="my-0">Informações Gerais</h3></div><div class="card-body"><div class="row"><div class="col-md-6 mb-3"><label class="form-label fw-semibold">Empreendimento <span class="text-danger">*</span></label><select v-model="form.cod_emp" class="form-select" :class="{ 'is-invalid': errors.cod_emp || form.errors.cod_emp }" @change="errors.cod_emp = ''"><option value="" disabled>Selecione um empreendimento</option><option v-for="empreendimento in props.empreendimentos" :key="empreendimento" :value="empreendimento">{{ empreendimento }}</option></select><small v-if="errors.cod_emp || form.errors.cod_emp" class="invalid-feedback d-block">{{ errors.cod_emp || form.errors.cod_emp }}</small></div><div class="col-md-6 mb-3"><label class="form-label fw-semibold">ID da Campanha <span class="text-danger">*</span></label><input v-model.number="form.id_campanha" type="number" min="1" class="form-control" :class="{ 'is-invalid': errors.id_campanha || form.errors.id_campanha }" @input="errors.id_campanha = ''"><small v-if="errors.id_campanha || form.errors.id_campanha" class="invalid-feedback d-block">{{ errors.id_campanha || form.errors.id_campanha }}</small></div><div class="col-md-6 mb-3"><label class="form-label fw-semibold">SEI DNIT</label><input v-model="form.sei_dnit" type="text" class="form-control" placeholder="Informe o número SEI DNIT"></div></div></div></div>
                <form class="d-flex flex-column gap-4" @submit.prevent="salvar(false)"><PlanilhasSimplificadas ref="planilhasRef" :form="form" :modulos="props.modulos" :contrato="props.contrato" /><CardFotos ref="fotosRef" :form="form" /><CardAnexos ref="anexosRef" :form="form" /><div class="card"><div class="card-body d-flex justify-content-end gap-2"><button type="button" class="btn btn-light" :disabled="form.processing" @click="salvar(false)">Salvar Rascunho</button><button type="button" class="btn btn-primary" :disabled="form.processing" @click="salvar(true)">Enviar para Análise</button></div></div></form>
            </main>
        </div>
        <div v-if="showAnalysisModal" class="modal-backdrop-custom" @click.self="showAnalysisModal = false"><div class="analysis-modal"><div class="d-flex justify-content-between align-items-center mb-3"><h5 class="mb-0">Histórico de análises</h5><button type="button" class="btn-close" @click="showAnalysisModal = false"></button></div><div v-for="analise in props.campanha.analises" :key="analise.id" class="analysis-card" :class="analise.status === 'Rejeitada' ? 'analysis-rejected' : 'analysis-approved'"><div class="d-flex justify-content-between gap-3"><strong>Versão {{ analise.versao }} — {{ analise.status === 'Rejeitada' ? 'Reprovada' : analise.status }}</strong><small>{{ analise.created_at }}</small></div><small v-if="analise.fiscal?.name" class="d-block mt-1">Fiscal: {{ analise.fiscal.name }}</small><p v-if="analise.observacoes" class="mb-0 mt-2">{{ analise.observacoes }}</p></div></div></div>
    </AuthenticatedLayout>
</template>

<style scoped>.menu-column { width: 200px; min-width: 120px; flex: 0 0 auto; }.content-column { min-width: 0; }.analysis-trigger { cursor: pointer; }.modal-backdrop-custom { position: fixed; inset: 0; z-index: 1055; background: rgba(0,0,0,.65); display: grid; place-items: center; padding: 24px; }.analysis-modal { width: min(760px,100%); max-height: 90vh; overflow: auto; background: #fff; border-radius: 8px; padding: 20px; }.analysis-card { border: 1px solid; border-radius: 6px; padding: 12px; margin-bottom: 10px; }.analysis-rejected { background: #fff5f5; border-color: #f1b7b7; color: #842029; }.analysis-approved { background: #f3fbf5; border-color: #b9dfc0; color: #1f6130; }</style>
