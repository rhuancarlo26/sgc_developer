```vue
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
import QueloniosCrocodilian from './QueloniosCrocodilianos.vue';
import FaunaCavernic from './FaunaCavernicola.vue';
import Metodologia from './Metodologia.vue';
import FaunaResultados from './FaunaResultados.vue';

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
                                <QueloniosCrocodilian
                                    v-if="subStep === 4"
                                    :nao-se-aplica="campanha.nao_se_aplica"
                                    :ponto-records="campanha.pontos_quelo_crocod || []"
                                    :read-only="true"
                                    @next="subStep = 5"
                                    @prev="prevSubStep"
                                >
                                    <template #footer>
                                        <h4 class="text-center mt-4" style="font-weight: bold; color: #6c757d;">{{ subStep }}/5</h4>
                                    </template>
                                </QueloniosCrocodilian>
                                <FaunaCavernic
                                    v-if="subStep === 5"
                                    :nao-se-aplica="campanha.nao_se_aplica"
                                    :ponto-cavernicola-records="campanha.pontos_cavernicola || []"
                                    :read-only="true"
                                    @next="setActiveTab('metodologia')"
                                    @prev="prevSubStep"
                                >
                                    <template #footer>
                                        <h4 class="text-center mt-4" style="font-weight: bold; color: #6c757d;">{{ subStep }}/5</h4>
                                    </template>
                                </FaunaCavernic>
                            </div>
                            <div v-if="activeTab === 'metodologia'" class="tab-pane fade" :class="{ 'show active': activeTab === 'metodologia' }">
                                <Metodologia
                                    :metodologia-records="campanha.metodologias || []"
                                    :read-only="true"
                                    @prev="setActiveTab('apresentacao')"
                                    @next="setActiveTab('resultados')"
                                />
                            </div>
                            <div v-if="activeTab === 'resultados'" class="tab-pane fade" :class="{ 'show active': activeTab === 'resultados' }">
                                <FaunaResultados
                                    :resultados-records="campanha.resultados || []"
                                    :consideracoes="campanha.consideracoes"
                                    :read-only="true"
                                    @prev="setActiveTab('metodologia')"
                                    @next="setActiveTab('anexos')"
                                />
                            </div>
                            <div v-if="activeTab === 'anexos'" class="tab-pane fade" :class="{ 'show active': activeTab === 'anexos' }">
                                <h4>ANEXOS</h4>
                                <div class="row">
                                    <div class="col-md-6 mb-3" v-for="(anexo, key) in campanha.anexos" :key="key">
                                        <label class="form-label">{{ key.replace('_', ' ').toUpperCase() }}</label>
                                        <div>
                                            <a v-if="anexo" :href="'/storage/' + anexo.caminho" target="_blank" class="btn btn-link">Visualizar</a>
                                            <span v-else>Nenhum arquivo</span>
                                        </div>
                                    </div>
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
</style>
```