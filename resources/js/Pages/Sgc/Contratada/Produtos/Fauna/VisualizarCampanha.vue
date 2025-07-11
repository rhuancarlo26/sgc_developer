<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import NavbarContrato from '@/Pages/Sgc/Contratada/NavbarContrato.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import InputError from '@/Components/InputError.vue';
import NavButton from '@/Components/NavButton.vue';
import { ref } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import DadosGeraisVisualizar from './Componentes/DadosGeraisVisualizar.vue';
import ModulosAmostraisVisualizar from './Componentes/ModulosAmostraisVisualizar.vue';
import QueloniosCrocodilianosVisualizar from './Componentes/QueloniosCrocodilianosVisualizar.vue';
import FaunaCavernicolaVisualizar from './Componentes/FaunaCavernicolaVisualizar.vue';
import MetodologiaVisualizar from './Componentes/MetodologiaVisualizar.vue';
import FaunaResultadosVisualizar from './Componentes/FaunaResultadosVisualizar.vue';

defineProps({
    campanha: {
        type: Object,
        default: () => ({}),
    },
    contrato: [Number, String],
    produto: String,
    contratos: Object,
    canApprove: Boolean,
});

const activeTab = ref('apresentacao');
const subStep = ref(1);
const formAprovacao = useForm({
    status: '',
    observacoes: '',
});

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

const aprovarCampanha = () => {
    formAprovacao.status = 'Aprovada';
    formAprovacao.observacoes = '';
    salvarAprovacao();
};

const rejeitarCampanha = () => {
    if (!formAprovacao.observacoes.trim()) {
        formAprovacao.errors.observacoes = 'Observações são obrigatórias para rejeição.';
        return;
    }
    formAprovacao.status = 'Rejeitada';
    salvarAprovacao();
};

const salvarAprovacao = () => {
    formAprovacao.post(route('sgc.contratada.produtos.approve', [campanha.id]), {
        onSuccess: () => {
            formAprovacao.reset();
            alert('Campanha atualizada com sucesso!');
            router.get(route('sgc.contratada.produtos.index', [contrato, produto.toLowerCase()]));
        },
        onError: (errors) => {
            console.error('Erro ao salvar aprovação:', errors);
            alert('Erro ao salvar aprovação: ' + (errors.error || 'Verifique os dados e tente novamente.'));
        },
    });
};
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <Breadcrumb
                :links="[
                    { route: route('sgc.gestao.listagem', contratos.tipo_contrato), label: 'Gestão de Contratos' },
                    { route: route('sgc.contratada.produtos.index', [contrato, produto.toLowerCase()]), label: contratos.contratada },
                    { route: '#', label: `Visualizar Campanha ${campanha.id_campanha || campanha.id}` },
                ]"
            />
        </template>
        <NavbarContrato :tipo="{ id: contrato }">
            <template #body>
                <div class="card">
                    <div class="card-body">
                        <h2 class="text-center mb-4">VISUALIZAR CAMPANHA {{ produto.toUpperCase() }}</h2>
                        <h4 class="mb-3">Status: {{ campanha.status }}</h4>
                        <ul class="nav nav-tabs mb-4">
                            <li class="nav-item">
                                <a
                                    class="nav-link"
                                    :class="{ active: activeTab === 'apresentacao' }"
                                    @click.prevent="setActiveTab('apresentacao')"
                                    >Apresentação</a
                                >
                            </li>
                            <li class="nav-item">
                                <a
                                    class="nav-link"
                                    :class="{ active: activeTab === 'metodologia' }"
                                    @click.prevent="setActiveTab('metodologia')"
                                    >Metodologia</a
                                >
                            </li>
                            <li class="nav-item">
                                <a
                                    class="nav-link"
                                    :class="{ active: activeTab === 'resultados' }"
                                    @click.prevent="setActiveTab('resultados')"
                                    >Resultados</a
                                >
                            </li>
                            <li class="nav-item">
                                <a
                                    class="nav-link"
                                    :class="{ active: activeTab === 'anexos' }"
                                    @click.prevent="setActiveTab('anexos')"
                                    >Anexos</a
                                >
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div v-if="activeTab === 'apresentacao'" class="tab-pane fade" :class="{ 'show active': activeTab === 'apresentacao' }">
                                <div v-if="subStep === 1">
                                    <h4 class="text-center mb-3" style="font-weight: bold;">APRESENTAÇÃO</h4>
                                    <div class="mb-3">
                                        <label class="form-label">Empreendimento</label>
                                        <input type="text" class="form-control" :value="campanha.cod_emp" disabled />
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Família</label>
                                        <input type="text" class="form-control" :value="campanha.familia || 'Fauna'" disabled />
                                    </div>
                                    <div class="d-flex justify-content-end">
                                        <button class="btn btn-primary" @click="subStep = 2">Avançar</button>
                                    </div>
                                    <h4 class="text-center mt-4" style="font-weight: bold; color: #6c757d;">{{ subStep }}/5</h4>
                                </div>
                                <DadosGeraisVisualizar
                                    v-if="subStep === 2"
                                    :campanha="campanha"
                                    :abio-records="campanha.abios || []"
                                    :profissional-records="campanha.profissionais || []"
                                    :sub-step="subStep"
                                    @next="subStep = 3"
                                    @prev="prevSubStep"
                                />
                                <ModulosAmostraisVisualizar
                                    v-if="subStep === 3"
                                    :form-modulo-amostral="campanha.formModuloAmostral || {}"
                                    :modulo-records="campanha.modulos_amostrais || []"
                                    :sub-step="subStep"
                                    @next="subStep = 4"
                                    @prev="prevSubStep"
                                />
                                <QueloniosCrocodilianosVisualizar
                                    v-if="subStep === 4"
                                    :formPontosAmostragem="campanha.formPontosAmostragem || {}"
                                    :naoSeAplica="campanha.nao_se_aplica"
                                    :subStep="subStep"
                                    @next="subStep = 5"
                                    @prev="prevSubStep"
                                >
                                    <template #footer>
                                        <h4 class="text-center mt-4" style="font-weight: bold; color: #6c757d;">{{ subStep }}/5</h4>
                                    </template>
                                </QueloniosCrocodilianosVisualizar>
                                <FaunaCavernicolaVisualizar
                                    v-if="subStep === 5"
                                    :formPontosCavernicola="campanha.formPontosCavernicola || {}"
                                    :naoSeAplica="campanha.nao_se_aplica"
                                    :subStep="subStep"
                                    @next="setActiveTab('metodologia')"
                                    @prev="prevSubStep"
                                >
                                    <template #footer>
                                        <h4 class="text-center mt-4" style="font-weight: bold; color: #6c757d;">{{ subStep }}/5</h4>
                                    </template>
                                </FaunaCavernicolaVisualizar>
                            </div>
                            <div v-if="activeTab === 'metodologia'" class="tab-pane fade" :class="{ 'show active': activeTab === 'metodologia' }">
                                <MetodologiaVisualizar
                                    :formMetodologia="campanha.formMetodologia || {}"
                                    @prev="setActiveTab('apresentacao')"
                                    @next="setActiveTab('resultados')"
                                />
                            </div>
                            <div v-if="activeTab === 'resultados'" class="tab-pane fade" :class="{ 'show active': activeTab === 'resultados' }">
                                <FaunaResultadosVisualizar
                                    :resultados="campanha.resultados || []"
                                    :consideracoes="campanha.consideracoes"
                                    @prev="setActiveTab('metodologia')"
                                    @next="setActiveTab('anexos')"
                                />
                            </div>
                            <div v-if="activeTab === 'anexos'" class="tab-pane fade" :class="{ 'show active': activeTab === 'anexos' }">
                                <h4 class="mb-3" style="text-align: center;">ANEXOS</h4>
                                <div v-if="campanha.anexos && campanha.anexos.length > 0" class="overflow-x-auto mb-6">
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
                                            <tr v-for="anexo in campanha.anexos" :key="anexo.id" class="hover:bg-gray-50">
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
                                <div v-if="canApprove && campanha.status === 'Em análise'" class="mt-4">
                                    <h4>APROVAÇÃO</h4>
                                    <form @submit.prevent="rejeitarCampanha" v-if="campanha.status === 'Em análise'">
                                        <div class="mb-3">
                                            <label for="observacoes" class="form-label">Observações (obrigatório para rejeição)</label>
                                            <textarea
                                                v-model="formAprovacao.observacoes"
                                                id="observacoes"
                                                class="form-control"
                                                rows="4"
                                                placeholder="Digite observações (obrigatório para rejeição)"
                                            ></textarea>
                                            <InputError :message="formAprovacao.errors.observacoes" />
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <NavButton type="button" type-button="success" title="Aprovar" @click="aprovarCampanha" />
                                            <NavButton type="submit" type-button="danger" title="Rejeitar" />
                                        </div>
                                    </form>
                                </div>
                                <div class="d-flex justify-content-between mt-4">
                                    <NavButton type="button" type-button="secondary" title="Voltar" @click="setActiveTab('resultados')" />
                                    <NavButton
                                        type="button"
                                        type-button="primary"
                                        title="Voltar à Lista"
                                        @click="router.get(route('sgc.contratada.produtos.index', [contrato, produto.toLowerCase()]))"
                                    />
                                </div>
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
</style>