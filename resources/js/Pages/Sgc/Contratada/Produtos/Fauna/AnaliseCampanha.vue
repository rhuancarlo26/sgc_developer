<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import NavbarContrato from '@/Pages/Sgc/Contratada/NavbarContrato.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import InputError from '@/Components/InputError.vue';
import NavButton from '@/Components/NavButton.vue';
import DadosGeraisVisualizar from './Componentes/DadosGeraisVisualizar.vue';
import ModulosAmostraisVisualizar from './Componentes/ModulosAmostraisVisualizar.vue';
import QueloniosCrocodilianosVisualizar from './Componentes/QueloniosCrocodilianosVisualizar.vue';
import FaunaCavernicolaVisualizar from './Componentes/FaunaCavernicolaVisualizar.vue';
import MetodologiaVisualizar from './Componentes/MetodologiaVisualizar.vue';
import FaunaResultadosVisualizar from './Componentes/FaunaResultadosVisualizar.vue';
import { ref } from 'vue';
import { useForm, router } from '@inertiajs/vue3';

const props = defineProps({
    campanha: { type: Object, default: () => ({}) },
    contrato: { type: [Number, String], required: true },
    produto: { type: String, required: true },
    contratos: { type: Object, required: true },
    canApprove: { type: Boolean, default: false },
    analises: { type: Array, default: () => [] },
});

// Log para depuração
console.log('Props recebidas em AnaliseCampanha.vue:', {
    contrato: props.contrato,
    produto: props.produto,
    campanha: props.campanha,
    contratos: props.contratos,
    canApprove: props.canApprove,
    analises: props.analises,
});

const activeTab = ref('apresentacao');
const subStep = ref(1);

// Mapeamento de etapas para subetapas e abas
const etapas = [
    { value: 'apresentacao_geral', label: 'Apresentação Geral', tab: 'apresentacao', subStep: 1 },
    { value: 'caracterizacao_area', label: 'Caracterização da Área', tab: 'apresentacao', subStep: 2 },
    { value: 'modulos_amostrais', label: 'Módulos Amostrais', tab: 'apresentacao', subStep: 3 },
    { value: 'pontos_quelo_crocod', label: 'Pontos Quelo/Crocod', tab: 'apresentacao', subStep: 4 },
    { value: 'pontos_cavernicola', label: 'Pontos Cavernícola', tab: 'apresentacao', subStep: 5 },
    { value: 'metodologia', label: 'Metodologia', tab: 'metodologia', subStep: null },
    { value: 'resultados', label: 'Resultados', tab: 'resultados', subStep: null },
    { value: 'anexos', label: 'Anexos', tab: 'anexos', subStep: null },
];

// Formulários de análise por etapa
const analiseForms = ref(
    etapas.reduce((acc, etapa) => ({
        ...acc,
        [etapa.value]: useForm({
            etapa: etapa.value,
            status: '',
            observacoes: '',
        }),
    }), {})
);

// Função para obter o status de uma etapa
const getEtapaStatus = (etapaValue) => {
    const analises = Array.isArray(props.analises) ? props.analises : [];
    const analise = analises.find(a => a.etapa === etapaValue);
    return analise ? analise.status : 'Pendente';
};

// Função para determinar o status de uma aba
const getTabStatus = (tab) => {
    const analises = Array.isArray(props.analises) ? props.analises : [];
    const etapasDaAba = etapas.filter(e => e.tab === tab).map(e => e.value);
    const analisesDaAba = analises.filter(a => etapasDaAba.includes(a.etapa));
    
    if (analisesDaAba.length === 0) return 'Pendente';
    if (analisesDaAba.some(a => a.status === 'Rejeitada')) return 'Rejeitada';
    if (analisesDaAba.every(a => a.status === 'Aprovada')) return 'Aprovada';
    return 'Pendente';
};

const setActiveTab = (tab) => {
    activeTab.value = tab;
    if (tab === 'apresentacao') {
        subStep.value = 1;
    }
};

const prevSubStep = () => {
    if (subStep.value > 1) {
        subStep.value -= 1;
    }
};

// Log para verificar inicialização
console.log('Inicializando analiseForms:', Object.keys(analiseForms.value));

const salvarAnalise = (etapa) => {
    const form = analiseForms.value[etapa];
    if (!form) {
        console.error('Erro: Formulário não encontrado para a etapa', etapa);
        alert('Erro: Formulário não encontrado para a etapa ' + etapa);
        return;
    }

    if (form.status === 'Rejeitada' && !form.observacoes.trim()) {
        form.errors.observacoes = 'Observações são obrigatórias para rejeição.';
        return;
    }

    if (!props.contrato || !props.produto || !props.campanha || !props.campanha.id) {
        console.error('Erro: Props não definidas em salvarAnalise', {
            contrato: props.contrato,
            produto: props.produto,
            campanha: props.campanha,
        });
        alert('Erro: Dados da campanha não estão disponíveis. Tente recarregar a página.');
        return;
    }

    console.log('Enviando análise para etapa:', {
        etapa: form.etapa,
        status: form.status,
        observacoes: form.observacoes,
    });

    form.post(
        route('sgc.contratada.produtos.salvarAnalise', [props.contrato, props.produto.toLowerCase(), props.campanha.id]),
        {
            onSuccess: () => {
                form.reset('status', 'observacoes');
                router.reload({ only: ['analises'] });
                alert(`Análise da etapa ${etapas.find(e => e.value === etapa)?.label || etapa} salva com sucesso!`);
            },
            onError: (errors) => {
                console.error('Erro ao salvar análise da etapa', { etapa, errors });
                alert('Erro ao salvar análise: ' + (Object.values(errors).join(', ') || 'Verifique os dados e tente novamente.'));
            },
        }
    );
};

const aprovarEtapa = (etapa) => {
    analiseForms.value[etapa].status = 'Aprovada';
    analiseForms.value[etapa].observacoes = '';
    salvarAnalise(etapa);
};

const rejeitarEtapa = (etapa) => {
    if (!analiseForms.value[etapa].observacoes.trim()) {
        analiseForms.value[etapa].errors.observacoes = 'Observações são obrigatórias para rejeição.';
        return;
    }
    analiseForms.value[etapa].status = 'Rejeitada';
    salvarAnalise(etapa);
};
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <Breadcrumb
                :links="[
                    { route: route('sgc.gestao.listagem', contratos?.tipo_contrato), label: 'Gestão de Contratos' },
                    { route: route('sgc.contratada.produtos.index', [props.contrato, props.produto?.toLowerCase()]), label: contratos?.contratada },
                    { route: '#', label: `Análise de Campanha ${props.campanha?.id || ''}` },
                ]"
            />
        </template>
        <NavbarContrato :tipo="{ id: props.contrato }">
            <template #body>
                <div class="card">
                    <div class="card-body">
                        <h2 class="text-center mb-4">ANÁLISE DE CAMPANHA {{ props.produto?.toUpperCase() || '' }}</h2>
                        <h4 class="mb-3">Status: {{ props.campanha?.status || 'Não informado' }}</h4>
                        <ul class="nav nav-tabs mb-4">
                            <li class="nav-item">
                                <a
                                    class="nav-link d-flex align-items-center"
                                    :class="{ active: activeTab === 'apresentacao' }"
                                    @click.prevent="setActiveTab('apresentacao')"
                                >
                                    Apresentação
                                    <i
                                        v-if="getTabStatus('apresentacao') === 'Aprovada'"
                                        class="bi bi-check-circle-fill ms-2 text-success"
                                    ></i>
                                    <i
                                        v-else-if="getTabStatus('apresentacao') === 'Rejeitada'"
                                        class="bi bi-x-circle-fill ms-2 text-danger"
                                    ></i>
                                    <i
                                        v-else
                                        class="bi bi-hourglass-split ms-2 text-warning"
                                    ></i>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a
                                    class="nav-link d-flex align-items-center"
                                    :class="{ active: activeTab === 'metodologia' }"
                                    @click.prevent="setActiveTab('metodologia')"
                                >
                                    Metodologia
                                    <i
                                        v-if="getTabStatus('metodologia') === 'Aprovada'"
                                        class="bi bi-check-circle-fill ms-2 text-success"
                                    ></i>
                                    <i
                                        v-else-if="getTabStatus('metodologia') === 'Rejeitada'"
                                        class="bi bi-x-circle-fill ms-2 text-danger"
                                    ></i>
                                    <i
                                        v-else
                                        class="bi bi-hourglass-split ms-2 text-warning"
                                    ></i>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a
                                    class="nav-link d-flex align-items-center"
                                    :class="{ active: activeTab === 'resultados' }"
                                    @click.prevent="setActiveTab('resultados')"
                                >
                                    Resultados
                                    <i
                                        v-if="getTabStatus('resultados') === 'Aprovada'"
                                        class="bi bi-check-circle-fill ms-2 text-success"
                                    ></i>
                                    <i
                                        v-else-if="getTabStatus('resultados') === 'Rejeitada'"
                                        class="bi bi-x-circle-fill ms-2 text-danger"
                                    ></i>
                                    <i
                                        v-else
                                        class="bi bi-hourglass-split ms-2 text-warning"
                                    ></i>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a
                                    class="nav-link d-flex align-items-center"
                                    :class="{ active: activeTab === 'anexos' }"
                                    @click.prevent="setActiveTab('anexos')"
                                >
                                    Anexos
                                    <i
                                        v-if="getTabStatus('anexos') === 'Aprovada'"
                                        class="bi bi-check-circle-fill ms-2 text-success"
                                    ></i>
                                    <i
                                        v-else-if="getTabStatus('anexos') === 'Rejeitada'"
                                        class="bi bi-x-circle-fill ms-2 text-danger"
                                    ></i>
                                    <i
                                        v-else
                                        class="bi bi-hourglass-split ms-2 text-warning"
                                    ></i>
                                </a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <!-- Aba Apresentação -->
                            <div v-if="activeTab === 'apresentacao'" class="tab-pane fade" :class="{ 'show active': activeTab === 'apresentacao' }">
                                <div v-if="subStep === 1">
                                    <h4 class="text-center mb-3" style="font-weight: bold;">APRESENTAÇÃO GERAL</h4>
                                    <div v-if="getEtapaStatus('apresentacao_geral') === 'Aprovada'" class="status-container">
                                        <span class="badge bg-success text-white">Aprovado</span>
                                    </div>
                                    <div v-else-if="getEtapaStatus('apresentacao_geral') === 'Rejeitada'" class="status-container">
                                        <span class="badge bg-danger text-white">Rejeitada</span>
                                    </div>
                                    <div v-else class="status-container">
                                        <span class="badge bg-warning text-white">Pendente</span>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Empreendimento</label>
                                        <input type="text" class="form-control" :value="props.campanha?.cod_emp || 'Não informado'" disabled />
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Família</label>
                                        <input type="text" class="form-control" :value="props.campanha?.familia || 'Fauna'" disabled />
                                    </div>
                                    <div v-if="props.canApprove && props.campanha?.status === 'Em análise'" class="mt-4">
                                        <h4 class="text-center">ANÁLISE DA ETAPA</h4>
                                        <form @submit.prevent="rejeitarEtapa('apresentacao_geral')">
                                            <div class="mb-3">
                                                <label for="observacoes_apresentacao" class="form-label">Observações (obrigatório para rejeição)</label>
                                                <textarea
                                                    v-model="analiseForms.apresentacao_geral.observacoes"
                                                    id="observacoes_apresentacao"
                                                    class="form-control"
                                                    rows="4"
                                                    placeholder="Digite observações (obrigatório para rejeição)"
                                                ></textarea>
                                                <InputError :message="analiseForms.apresentacao_geral.errors.observacoes" />
                                            </div>
                                            <div class="d-flex justify-content-end gap-2">
                                                <NavButton type="submit" type-button="danger" title="Rejeitar" />
                                                <NavButton type="button" type-button="success" title="Aprovar" @click="aprovarEtapa('apresentacao_geral')" />
                                            </div>
                                        </form>
                                    </div>
                                    <div class="d-flex justify-content-end mt-4">
                                        <NavButton type="button" type-button="primary" title="Avançar" @click="subStep = 2" />
                                    </div>
                                    <h4 class="text-center mt-4" style="font-weight: bold; color: #6c757d;">{{ subStep }}/5</h4>
                                </div>
                                <div v-if="subStep === 2">
                                    <div v-if="getEtapaStatus('caracterizacao_area') === 'Aprovada'" class="status-container">
                                        <span class="badge bg-success text-white">Aprovado</span>
                                    </div>
                                    <div v-else-if="getEtapaStatus('caracterizacao_area') === 'Rejeitada'" class="status-container">
                                        <span class="badge bg-danger text-white">Rejeitada</span>
                                    </div>
                                    <div v-else class="status-container">
                                        <span class="badge bg-warning text-white">Pendente</span>
                                    </div>
                                    <DadosGeraisVisualizar
                                        :campanha="props.campanha"
                                        :abio-records="props.campanha?.abios || []"
                                        :profissional-records="props.campanha?.profissionais || []"
                                        :sub-step="subStep"
                                        @next="subStep = 3"
                                        @prev="prevSubStep"
                                    />
                                    <div v-if="props.canApprove && props.campanha?.status === 'Em análise'" class="mt-4">
                                        <h4 class="text-center">ANÁLISE DA ETAPA</h4>
                                        <form @submit.prevent="rejeitarEtapa('caracterizacao_area')">
                                            <div class="mb-3">
                                                <label for="observacoes_caracterizacao" class="form-label">Observações (obrigatório para rejeição)</label>
                                                <textarea
                                                    v-model="analiseForms.caracterizacao_area.observacoes"
                                                    id="observacoes_caracterizacao"
                                                    class="form-control"
                                                    rows="4"
                                                    placeholder="Digite observações (obrigatório para rejeição)"
                                                ></textarea>
                                                <InputError :message="analiseForms.caracterizacao_area.errors.observacoes" />
                                            </div>
                                            <div class="d-flex justify-content-end gap-2">
                                                <NavButton type="submit" type-button="danger" title="Rejeitar" />
                                                <NavButton type="button" type-button="success" title="Aprovar" @click="aprovarEtapa('caracterizacao_area')" />
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <div v-if="subStep === 3">
                                    <div v-if="getEtapaStatus('modulos_amostrais') === 'Aprovada'" class="status-container">
                                        <span class="badge bg-success text-white">Aprovado</span>
                                    </div>
                                    <div v-else-if="getEtapaStatus('modulos_amostrais') === 'Rejeitada'" class="status-container">
                                        <span class="badge bg-danger text-white">Rejeitada</span>
                                    </div>
                                    <div v-else class="status-container">
                                        <span class="badge bg-warning text-white">Pendente</span>
                                    </div>
                                    <ModulosAmostraisVisualizar
                                        :form-modulo-amostral="props.campanha?.formModuloAmostral || {}"
                                        :modulo-records="props.campanha?.modulos_amostrais || []"
                                        :sub-step="subStep"
                                        @next="subStep = 4"
                                        @prev="prevSubStep"
                                    />
                                    <div v-if="props.canApprove && props.campanha?.status === 'Em análise'" class="mt-4">
                                        <h4 class="text-center">ANÁLISE DA ETAPA</h4>
                                        <form @submit.prevent="rejeitarEtapa('modulos_amostrais')">
                                            <div class="mb-3">
                                                <label for="observacoes_modulos" class="form-label">Observações (obrigatório para rejeição)</label>
                                                <textarea
                                                    v-model="analiseForms.modulos_amostrais.observacoes"
                                                    id="observacoes_modulos"
                                                    class="form-control"
                                                    rows="4"
                                                    placeholder="Digite observações (obrigatório para rejeição)"
                                                ></textarea>
                                                <InputError :message="analiseForms.modulos_amostrais.errors.observacoes" />
                                            </div>
                                            <div class="d-flex justify-content-end gap-2">
                                                <NavButton type="submit" type-button="danger" title="Rejeitar" />
                                                <NavButton type="button" type-button="success" title="Aprovar" @click="aprovarEtapa('modulos_amostrais')" />
                                            </div>
                                        </form>
                                    </div>
                                    <h4 class="text-center mt-4" style="font-weight: bold; color: #6c757d;">{{ subStep }}/5</h4>
                                </div>
                                <div v-if="subStep === 4">
                                    <div v-if="getEtapaStatus('pontos_quelo_crocod') === 'Aprovada'" class="status-container">
                                        <span class="badge bg-success text-white">Aprovado</span>
                                    </div>
                                    <div v-else-if="getEtapaStatus('pontos_quelo_crocod') === 'Rejeitada'" class="status-container">
                                        <span class="badge bg-danger text-white">Rejeitada</span>
                                    </div>
                                    <div v-else class="status-container">
                                        <span class="badge bg-warning text-white">Pendente</span>
                                    </div>
                                    <QueloniosCrocodilianosVisualizar
                                        :formPontosAmostragem="props.campanha?.formPontosAmostragem || {}"
                                        :naoSeAplica="props.campanha?.nao_se_aplica"
                                        :subStep="subStep"
                                        @next="subStep = 5"
                                        @prev="prevSubStep"
                                    />
                                    <div v-if="props.canApprove && props.campanha?.status === 'Em análise'" class="mt-4">
                                        <h4 class="text-center">ANÁLISE DA ETAPA</h4>
                                        <form @submit.prevent="rejeitarEtapa('pontos_quelo_crocod')">
                                            <div class="mb-3">
                                                <label for="observacoes_quelo" class="form-label">Observações (obrigatório para rejeição)</label>
                                                <textarea
                                                    v-model="analiseForms.pontos_quelo_crocod.observacoes"
                                                    id="observacoes_quelo"
                                                    class="form-control"
                                                    rows="4"
                                                    placeholder="Digite observações (obrigatório para rejeição)"
                                                ></textarea>
                                                <InputError :message="analiseForms.pontos_quelo_crocod.errors.observacoes" />
                                            </div>
                                            <div class="d-flex justify-content-end gap-2">
                                                <NavButton type="submit" type-button="danger" title="Rejeitar" />
                                                <NavButton type="button" type-button="success" title="Aprovar" @click="aprovarEtapa('pontos_quelo_crocod')" />
                                            </div>
                                        </form>
                                    </div>
                                    <h4 class="text-center mt-4" style="font-weight: bold; color: #6c757d;">{{ subStep }}/5</h4>
                                </div>
                                <div v-if="subStep === 5">
                                    <div v-if="getEtapaStatus('pontos_cavernicola') === 'Aprovada'" class="status-container">
                                        <span class="badge bg-success text-white">Aprovado</span>
                                    </div>
                                    <div v-else-if="getEtapaStatus('pontos_cavernicola') === 'Rejeitada'" class="status-container">
                                        <span class="badge bg-danger text-white">Rejeitada</span>
                                    </div>
                                    <div v-else class="status-container">
                                        <span class="badge bg-warning text-white">Pendente</span>
                                    </div>
                                    <FaunaCavernicolaVisualizar
                                        :formPontosCavernicola="props.campanha?.formPontosCavernicola || {}"
                                        :naoSeAplica="props.campanha?.nao_se_aplica"
                                        :subStep="subStep"
                                        @next="setActiveTab('metodologia')"
                                        @prev="prevSubStep"
                                    />
                                    <div v-if="props.canApprove && props.campanha?.status === 'Em análise'" class="mt-4">
                                        <h4 class="text-center">ANÁLISE DA ETAPA</h4>
                                        <form @submit.prevent="rejeitarEtapa('pontos_cavernicola')">
                                            <div class="mb-3">
                                                <label for="observacoes_cavernicola" class="form-label">Observações (obrigatório para rejeição)</label>
                                                <textarea
                                                    v-model="analiseForms.pontos_cavernicola.observacoes"
                                                    id="observacoes_cavernicola"
                                                    class="form-control"
                                                    rows="4"
                                                    placeholder="Digite observações (obrigatório para rejeição)"
                                                ></textarea>
                                                <InputError :message="analiseForms.pontos_cavernicola.errors.observacoes" />
                                            </div>
                                            <div class="d-flex justify-content-end gap-2">
                                                <NavButton type="submit" type-button="danger" title="Rejeitar" />
                                                <NavButton type="button" type-button="success" title="Aprovar" @click="aprovarEtapa('pontos_cavernicola')" />
                                            </div>
                                        </form>
                                    </div>
                                    <h4 class="text-center mt-4" style="font-weight: bold; color: #6c757d;">{{ subStep }}/5</h4>
                                </div>
                            </div>
                            <!-- Aba Metodologia -->
                            <div v-if="activeTab === 'metodologia'" class="tab-pane fade" :class="{ 'show active': activeTab === 'metodologia' }">
                                <div v-if="getEtapaStatus('metodologia') === 'Aprovada'" class="status-container">
                                    <span class="badge bg-success text-white">Aprovado</span>
                                </div>
                                <div v-else-if="getEtapaStatus('metodologia') === 'Rejeitada'" class="status-container">
                                    <span class="badge bg-danger text-white">Rejeitada</span>
                                </div>
                                <div v-else class="status-container">
                                    <span class="badge bg-warning text-white">Pendente</span>
                                </div>
                                <MetodologiaVisualizar
                                    :formMetodologia="props.campanha?.formMetodologia || {}"
                                    @prev="setActiveTab('apresentacao')"
                                    @next="setActiveTab('resultados')"
                                />
                                <div v-if="props.canApprove && props.campanha?.status === 'Em análise'" class="mt-4">
                                    <h4 class="text-center">ANÁLISE DA ETAPA</h4>
                                    <form @submit.prevent="rejeitarEtapa('metodologia')">
                                        <div class="mb-3">
                                            <label for="observacoes_metodologia" class="form-label">Observações (obrigatório para rejeição)</label>
                                            <textarea
                                                v-model="analiseForms.metodologia.observacoes"
                                                id="observacoes_metodologia"
                                                class="form-control"
                                                rows="4"
                                                placeholder="Digite observações (obrigatório para rejeição)"
                                            ></textarea>
                                            <InputError :message="analiseForms.metodologia.errors.observacoes" />
                                        </div>
                                        <div class="d-flex justify-content-end gap-2">
                                            <NavButton type="submit" type-button="danger" title="Rejeitar" />
                                            <NavButton type="button" type-button="success" title="Aprovar" @click="aprovarEtapa('metodologia')" />
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <!-- Aba Resultados -->
                            <div v-if="activeTab === 'resultados'" class="tab-pane fade" :class="{ 'show active': activeTab === 'resultados' }">
                                <div v-if="getEtapaStatus('resultados') === 'Aprovada'" class="status-container">
                                    <span class="badge bg-success text-white">Aprovado</span>
                                </div>
                                <div v-else-if="getEtapaStatus('resultados') === 'Rejeitada'" class="status-container">
                                    <span class="badge bg-danger text-white">Rejeitada</span>
                                </div>
                                <div v-else class="status-container">
                                    <span class="badge bg-warning text-white">Pendente</span>
                                </div>
                                <FaunaResultadosVisualizar
                                    :resultados="props.campanha?.resultados || []"
                                    :consideracoes="props.campanha?.consideracoes"
                                    @prev="setActiveTab('metodologia')"
                                    @next="setActiveTab('anexos')"
                                />
                                <div v-if="props.canApprove && props.campanha?.status === 'Em análise'" class="mt-4">
                                    <h4 class="text-center">ANÁLISE DA ETAPA</h4>
                                    <form @submit.prevent="rejeitarEtapa('resultados')">
                                        <div class="mb-3">
                                            <label for="observacoes_resultados" class="form-label">Observações (obrigatório para rejeição)</label>
                                            <textarea
                                                v-model="analiseForms.resultados.observacoes"
                                                id="observacoes_resultados"
                                                class="form-control"
                                                rows="4"
                                                placeholder="Digite observações (obrigatório para rejeição)"
                                            ></textarea>
                                            <InputError :message="analiseForms.resultados.errors.observacoes" />
                                        </div>
                                        <div class="d-flex justify-content-end gap-2">
                                            <NavButton type="submit" type-button="danger" title="Rejeitar" />
                                            <NavButton type="button" type-button="success" title="Aprovar" @click="aprovarEtapa('resultados')" />
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <!-- Aba Anexos -->
                            <div v-if="activeTab === 'anexos'" class="tab-pane fade" :class="{ 'show active': activeTab === 'anexos' }">
                                <h4 class="text-center mb-3" style="font-weight: bold;">ANEXOS</h4>
                                <div v-if="getEtapaStatus('anexos') === 'Aprovada'" class="status-container">
                                    <span class="badge bg-success text-white">Aprovado</span>
                                </div>
                                <div v-else-if="getEtapaStatus('anexos') === 'Rejeitada'" class="status-container">
                                    <span class="badge bg-danger text-white">Rejeitada</span>
                                </div>
                                <div v-else class="status-container">
                                    <span class="badge bg-warning text-white">Pendente</span>
                                </div>
                                <div v-if="props.campanha?.anexos && props.campanha.anexos.length > 0" class="overflow-x-auto mb-6">
                                    <table class="min-w-full bg-white border border-gray-300">
                                        <thead>
                                            <tr class="bg-gray-100">
                                                <th class="py-2 px-4 border-b text-left">ID</th>
                                                <th class="py-2 px-4 border-b text-left">Tipo de Anexo</th>
                                                <th class="py-2 px-4 border-b text-left">Nome do Arquivo</th>
                                                <th class="py-2 px-4 border-b text-left">Data de Criação</th>
                                                <th class="py-2 px-4 border-b text-left">Ação</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="anexo in props.campanha.anexos" :key="anexo.id" class="hover:bg-gray-50">
                                                <td class="py-2 px-4 border-b">{{ anexo.id || 'Não informado' }}</td>
                                                <td class="py-2 px-4 border-b">{{ anexo.tipo_anexo ? anexo.tipo_anexo.replace('_', ' ').toUpperCase() : 'Não informado' }}</td>
                                                <td class="py-2 px-4 border-b">{{ anexo.nome_arquivo || 'Não informado' }}</td>
                                                <td class="py-2 px-4 border-b">{{ anexo.created_at || 'Não informado' }}</td>
                                                <td class="py-2 px-4 border-b">
                                                    <a v-if="anexo.caminho" :href="'/storage/' + anexo.caminho" target="_blank" class="btn btn-link">Visualizar</a>
                                                    <span v-else>Nenhum arquivo</span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div v-else class="alert alert-info text-center">
                                    Nenhum anexo disponível.
                                </div>
                                <div v-if="props.canApprove && props.campanha?.status === 'Em análise'" class="mt-4">
                                    <h4 class="text-center">ANÁLISE DA ETAPA</h4>
                                    <form @submit.prevent="rejeitarEtapa('anexos')">
                                        <div class="mb-3">
                                            <label for="observacoes_anexos" class="form-label">Observações (obrigatório para rejeição)</label>
                                            <textarea
                                                v-model="analiseForms.anexos.observacoes"
                                                id="observacoes_anexos"
                                                class="form-control"
                                                rows="4"
                                                placeholder="Digite observações (obrigatório para rejeição)"
                                            ></textarea>
                                            <InputError :message="analiseForms.anexos.errors.observacoes" />
                                        </div>
                                        <div class="d-flex justify-content-end gap-2">
                                            <NavButton type="submit" type-button="danger" title="Rejeitar" />
                                            <NavButton type="button" type-button="success" title="Aprovar" @click="aprovarEtapa('anexos')" />
                                        </div>
                                    </form>
                                </div>
                                <div class="d-flex justify-content-between mt-4">
                                    <NavButton type="button" type-button="secondary" title="Voltar" @click="setActiveTab('resultados')" />
                                    <NavButton
                                        type="button"
                                        type-button="primary"
                                        title="Voltar à Lista"
                                        @click="router.get(route('sgc.contratada.produtos.index', [props.contrato, props.produto?.toLowerCase()]))"
                                    />
                                </div>
                            </div>
                        </div>
                        <!-- Tabela de Análises Realizadas -->
                        <div class="mt-4">
                            <h4>Análises Realizadas</h4>
                            <div v-if="props.analises && props.analises.length > 0" class="overflow-x-auto">
                                <table class="min-w-full bg-white border border-gray-300">
                                    <thead>
                                        <tr class="bg-gray-100">
                                            <th class="py-2 px-4 border-b text-left">Etapa</th>
                                            <th class="py-2 px-4 border-b text-left">Status</th>
                                            <th class="py-2 px-4 border-b text-left">Observações</th>
                                            <th class="py-2 px-4 border-b text-left">Data</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="analise in props.analises" :key="analise.id" class="hover:bg-gray-50">
                                            <td class="py-2 px-4 border-b">{{ etapas.find(e => e.value === analise.etapa)?.label || analise.etapa }}</td>
                                            <td class="py-2 px-4 border-b" :class="{ 'text-success': analise.status === 'Aprovada', 'text-danger': analise.status === 'Rejeitada' }">
                                                {{ analise.status || 'Não informado' }}
                                            </td>
                                            <td class="py-2 px-4 border-b">{{ analise.comentario || 'Não informado' }}</td>
                                            <td class="py-2 px-4 border-b">{{ analise.created_at || 'Não informado' }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div v-else class="alert alert-info text-center">
                                Nenhuma análise realizada.
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </NavbarContrato>
    </AuthenticatedLayout>
</template>

<style scoped>
.card {
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}
.nav-tabs .nav-link {
    color: #6c757d;
    font-weight: 500;
}
.nav-tabs .nav-link.active {
    color: #007bff;
    border-bottom: 2px solid #007bff;
}
.nav-tabs .nav-link i {
    font-size: 0.9rem;
}
.tab-content {
    padding: 20px;
}
table {
    border-collapse: collapse;
}
th, td {
    padding: 0.5rem 1rem;
    border: 1px solid #dee2e6;
}
thead {
    background-color: #f8f9fa;
}
tr:hover {
    background-color: #f1f5f9;
}
.alert-info {
    font-size: 1rem;
    padding: 1rem;
    border-radius: 6px;
    background-color: #e7f1ff;
    color: #084298;
}
.status-indicator {
    font-size: 1.1rem;
    font-weight: bold;
}
.bi-check-circle-fill, .bi-x-circle-fill, .bi-hourglass-split {
    font-size: 1rem;
}
.status-container {
    text-align: center;
    margin-bottom: 4rem;
}
</style>