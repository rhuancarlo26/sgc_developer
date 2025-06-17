<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import NavbarContrato from '@/Pages/Sgc/Contratada/NavbarContrato.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import NavButton from '@/Components/NavButton.vue';
import Table from '@/Components/Table.vue';
import { ref, computed } from 'vue';
import vSelect from 'vue-select';
import 'vue-select/dist/vue-select.css';

const props = defineProps({
    contrato: [Number, String],
    produto: String,
    contratos: Object,
    subproduto: [String, null],
    empreendimentos: Array,
    abios: Array,
    profissionais: Array,
});

// Controle da subetapa
const subStep = ref(1);

// Controle do modal
const showModal = ref(false);

// Formulário principal (Subetapa 1/4)
const form = useForm({
    cod_emp: '',
    familia: props.produto === 'Fauna' ? 'Fauna' : '',
});

// Formulário de Dados Gerais (Subetapa 2/4)
const formDadosGerais = useForm({
    id: '',
    data_campanha_inicial: '',
    data_campanha_final: '',
    periodo: '',
    obs: '',
});

// Formulário de Vincular ABIO (Subetapa 2/4)
const formAbio = useForm({
    id_abio: null,
});

// Formulário de Vincular Profissional (Subetapa 2/4)
const formProfissional = useForm({
    profissional: null,
    grupo_faunistico: null,
});

// Formulário de Cadastro de Profissional (Modal)
const formNovoProfissional = useForm({
    profissional: '',
    formacao: '',
});

// Formulário de Pontos de Amostragem (Subetapa 4/4)
const formPontosAmostragem = useForm({
    ponto_coleta: '',
    nome_curso_hidrico: '',
    coordenadas: '',
    bacia: '',
    profundidade: null,
    largura: null,
    tipo_substrato: '',
});

// Controle do checkbox "Não se aplica"
const naoSeAplica = ref(false);

// Dados para tabelas
const abioRecords = ref([]);
const profissionalRecords = ref([]);

// Mapeamento dos ABIOs para o v-select
const abioOptions = computed(() => {
    return props.abios
        .filter(abio => abio.licenca)
        .map(abio => ({
            ...abio,
            label: abio.licenca?.numero_licenca || 'Sem Licença',
        }));
});

// Mapeamento dos Profissionais para o v-select
const profissionalOptions = computed(() => {
    return props.profissionais.map(p => ({
        value: p.profissional,
        label: `${p.profissional} (${p.formacao})`,
    }));
});

// Grupos Faunísticos
const grupoFaunisticoOptions = [
    { value: 'Avifauna', label: 'Avifauna' },
    { value: 'Herpertofauna', label: 'Herpertofauna' },
    { value: 'Mastofauna', label: 'Mastofauna' },
    { value: 'Ictiofauna', label: 'Ictiofauna' },
    { value: 'Bentos', label: 'Bentos' },
];

// Salvar Dados Gerais
const salvarDadosGerais = () => {
    const data = {
        id_campanha: formDadosGerais.id,
        data_campanha_inicial: formDadosGerais.data_campanha_inicial,
        data_campanha_final: formDadosGerais.data_campanha_final,
        periodo: formDadosGerais.periodo,
        observacoes: formDadosGerais.obs,
        id_abio: formAbio.id_abio,
        cod_emp: form.cod_emp,
        subproduto: props.subproduto || '',
        profissionais: profissionalRecords.value.map(p => ({
            profissional: p.profissional,
            grupo_faunistico: p.grupo_faunistico,
        })),
    };

    router.post(route('sgc.contratada.produtos.salvar_campanha', [props.contrato, props.produto.toLowerCase()]), data, {
        onSuccess: () => {
            formDadosGerais.reset();
            formAbio.reset();
            formProfissional.reset();
            profissionalRecords.value = [];
            router.get(route('sgc.contratada.produtos.index', [props.contrato, props.produto.toLowerCase()]));
        },
        onError: (errors) => console.error('Erros ao salvar campanha:', errors),
    });
};

// Salvar ABIO
const salvarAbio = () => {
    formAbio.post(route('sgc.contratada.produtos.abio.store', [props.contrato, props.produto.toLowerCase()]), {
        onSuccess: () => {
            const abio = props.abios.find(a => a.id === formAbio.id_abio);
            if (abio) {
                abioRecords.value.push({
                    id: abio.id,
                    abio: { licenca: { numero_licenca: abio.licenca?.numero_licenca } },
                });
            }
            formAbio.reset();
        },
        onError: (errors) => console.error('Erros ao salvar ABIO:', errors),
    });
};

// Excluir ABIO
const excluirAbio = (id) => {
    router.delete(route('sgc.contratada.produtos.abio.destroy', [props.contrato, props.produto.toLowerCase(), id]), {
        onSuccess: () => {
            abioRecords.value = abioRecords.value.filter(item => item.id !== id);
        },
        onError: (errors) => console.error('Erros ao excluir ABIO:', errors),
    });
};

// Salvar Novo Profissional (Modal)
const salvarNovoProfissional = () => {
    formNovoProfissional.post(route('sgc.contratada.produtos.profissional.store', [props.contrato, props.produto.toLowerCase()]), {
        onSuccess: () => {
            props.profissionais.push({
                id: Date.now(), // Substituir por ID real retornado
                profissional: formNovoProfissional.profissional,
                formacao: formNovoProfissional.formacao,
            });
            formNovoProfissional.reset();
            showModal.value = false;
        },
        onError: (errors) => console.error('Erros ao salvar profissional:', errors),
    });
};

// Vincular Profissional
const vincularProfissional = () => {
    if (formProfissional.profissional && formProfissional.grupo_faunistico) {
        const profissionalData = props.profissionais.find(p => p.profissional === formProfissional.profissional);
        profissionalRecords.value.push({
            id: Date.now(),
            profissional: formProfissional.profissional,
            formacao: profissionalData?.formacao || 'N/A',
            grupo_faunistico: formProfissional.grupo_faunistico,
        });
        formProfissional.reset();
    }
};

// Excluir Profissional
const excluirProfissional = (id) => {
    profissionalRecords.value = profissionalRecords.value.filter(item => item.id !== id);
};

// Navegar entre subetapas
const nextSubStep = () => {
    if (subStep.value < 4) {
        subStep.value += 1;
    }
};

const prevSubStep = () => {
    if (subStep.value > 1) {
        subStep.value -= 1;
    }
};
</script>

<template>
    <AuthenticatedLayout>
        <Head :title="`Cadastrar ${produto} - Contrato ${contrato}`" />

        <template #header>
            <Breadcrumb
                :links="[
                    { route: route('sgc.gestao.listagem', contratos.tipo_contrato), label: `Gestão de Contratos` },
                    { route: route('sgc.contratada.produtos.index', [contrato, produto.toLowerCase()]), label: contratos.contratada },
                    { route: '#', label: `Cadastrar ${produto}` },
                ]"
            />
        </template>

        <NavbarContrato :tipo="{ id: contrato }">
            <template #body>
                <div class="card">
                    <div class="card-body">
                        <h2 class="text-center mb-4">CADASTRAR {{ produto.toUpperCase() }}</h2>

                        <!-- Navegação por subetapas -->
                        <h5 class="mb-3">Etapa {{ subStep }}/4</h5>

                        <!-- Subetapa 1/4 -->
                        <div v-if="subStep === 1">
                            <form @submit.prevent="nextSubStep">
                                <div class="mb-3">
                                    <label for="cod_emp" class="form-label">Empreendimento</label>
                                    <select v-model="form.cod_emp" class="form-select" id="cod_emp" required>
                                        <option value="">Selecione um empreendimento</option>
                                        <option v-for="emp in props.empreendimentos" :key="emp" :value="emp">{{ emp }}</option>
                                    </select>
                                    <InputError :message="form.errors.cod_emp" />
                                </div>
                                <div class="mb-3">
                                    <label for="familia" class="form-label">Família</label>
                                    <input v-model="form.familia" type="text" class="form-control" id="familia" :disabled="produto === 'Fauna'" required />
                                    <InputError :message="form.errors.familia" />
                                </div>
                                <div class="d-flex justify-content-end">
                                    <NavButton type="submit" type-button="primary" title="Avançar" />
                                </div>
                            </form>
                        </div>

                        <!-- Subetapa 2/4 -->
                        <div v-if="subStep === 2">
                            <form @submit.prevent="nextSubStep">
                                <!-- Dados Gerais -->
                                <h4 class="mb-3">DADOS GERAIS</h4>
                                <div class="mb-4">
                                    <div class="row mb-3">
                                        <div class="col-12 col-md-6">
                                            <InputLabel value="ID Campanha" for="id" />
                                            <input type="text" id="id" class="form-control" v-model="formDadosGerais.id" disabled />
                                            <InputError :message="formDadosGerais.errors.id" />
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-12 col-md-6">
                                            <InputLabel value="Data Inicial" for="data_campanha_inicial" />
                                            <input type="date" id="data_campanha_inicial" class="form-control" v-model="formDadosGerais.data_campanha_inicial" />
                                            <InputError :message="formDadosGerais.errors.data_campanha_inicial" />
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <InputLabel value="Data Final" for="data_campanha_final" />
                                            <input type="date" id="data_campanha_final" class="form-control" v-model="formDadosGerais.data_campanha_final" />
                                            <InputError :message="formDadosGerais.errors.data_campanha_final" />
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-12">
                                            <InputLabel value="Período" for="periodo" />
                                            <div class="d-flex align-items-center">
                                                <label class="form-check form-check-inline me-3">
                                                    <input class="form-check-input" type="radio" name="periodo" value="Seca" v-model="formDadosGerais.periodo" />
                                                    <span class="form-check-label">Seca</span>
                                                </label>
                                                <label class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="periodo" value="Chuva" v-model="formDadosGerais.periodo" />
                                                    <span class="form-check-label">Chuva</span>
                                                </label>
                                            </div>
                                            <InputError :message="formDadosGerais.errors.periodo" />
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-12">
                                            <InputLabel value="Observações" for="obs" />
                                            <textarea id="obs" class="form-control" v-model="formDadosGerais.obs" rows="5"></textarea>
                                            <InputError :message="formDadosGerais.errors.obs" />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col d-flex justify-content-end">
                                            <NavButton type="button" type-button="success" title="Salvar" @click="salvarDadosGerais" />
                                        </div>
                                    </div>
                                </div>

                                <!-- Vincular ABIO -->
                                <h4 class="mb-3">VINCULAR ABIO</h4>
                                <div class="mb-4">
                                    <div class="row mb-3">
                                        <div class="col-12 col-md-10">
                                            <InputLabel value="N° ABIO Vigente" for="id_abio" />
                                            <v-select
                                                :options="abioOptions"
                                                :reduce="a => a.id"
                                                v-model="formAbio.id_abio"
                                                placeholder="Selecione um ABIO"
                                                class="v-select-custom"
                                            >
                                                <template #no-options>
                                                    Nenhum registro encontrado.
                                                </template>
                                            </v-select>
                                            <InputError :message="formAbio.errors.id_abio" />
                                        </div>
                                        <div class="col-12 col-md-2 d-flex align-items-end">
                                            <NavButton type="button" type-button="success" title="Salvar" class="w-100" @click="salvarAbio" />
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <Table :columns="['ABIOs Vigentes', 'Ação']" :records="{ data: abioRecords, links: [] }">
                                            <template #body="{ item }">
                                                <tr>
                                                    <td>{{ item.abio?.licenca?.numero_licenca || 'N/A' }}</td>
                                                    <td class="text-center" style="min-width: 100px;">
                                                        <NavButton @click="excluirAbio(item.id)" type-button="danger" title="Excluir">
                                                            <i class="bi bi-trash"></i>
                                                        </NavButton>
                                                    </td>
                                                </tr>
                                            </template>
                                        </Table>
                                    </div>
                                </div>

                                <!-- Vincular Profissionais -->
                                <h4 class="mb-3">VINCULAR PROFISSIONAIS</h4>
                                <div class="mb-4">
                                    <div class="row mb-3">
                                        <div class="col-12 col-md-5">
                                            <InputLabel value="Selecionar Profissional" for="profissional" />
                                            <v-select
                                                v-model="formProfissional.profissional"
                                                :options="profissionalOptions"
                                                :reduce="p => p.value"
                                                placeholder="Selecione um profissional"
                                                class="v-select-custom"
                                            />
                                            <InputError :message="formProfissional.errors.profissional" />
                                        </div>
                                        <div class="col-12 col-md-5">
                                            <InputLabel value="Grupo Responsável" for="grupo_faunistico" />
                                            <v-select
                                                v-model="formProfissional.grupo_faunistico"
                                                :options="grupoFaunisticoOptions"
                                                :reduce="g => g.value"
                                                placeholder="Selecione um grupo"
                                                class="v-select-custom"
                                            />
                                            <InputError :message="formProfissional.errors.grupo_faunistico" />
                                        </div>
                                        <div class="col-12 col-md-2 d-flex align-items-end">
                                            <NavButton type="button" type-button="success" title="Vincular" class="w-100" @click="vincularProfissional" />
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-12">
                                            <NavButton type="button" type-button="primary" title="Cadastrar Profissional" @click="showModal = true" />
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <Table :columns="['Equipe Técnica', 'Formação', 'Grupo Responsável', 'Ação']" :records="{ data: profissionalRecords, links: [] }">
                                            <template #body="{ item }">
                                                <tr>
                                                    <td>{{ item.profissional || 'N/A' }}</td>
                                                    <td>{{ item.formacao || 'N/A' }}</td>
                                                    <td>{{ item.grupo_faunistico || 'N/A' }}</td>
                                                    <td class="text-center" style="min-width: 100px;">
                                                        <NavButton @click="excluirProfissional(item.id)" type-button="danger" title="Excluir">
                                                            <i class="bi bi-trash"></i>
                                                        </NavButton>
                                                    </td>
                                                </tr>
                                            </template>
                                        </Table>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between">
                                    <NavButton type="button" type-button="secondary" title="Voltar" @click="prevSubStep" />
                                    <NavButton type="submit" type-button="primary" title="Avançar" />
                                </div>
                            </form>
                        </div>

                        <!-- Subetapa 3/4 -->
                        <div v-if="subStep === 3">
                            <form @submit.prevent="nextSubStep">
                                <h4 class="mb-3">MÓDULOS AMOSTRAIS</h4>
                                <div class="mb-4">
                                    <p>Funcionalidade em desenvolvimento.</p>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <NavButton type="button" type-button="secondary" title="Voltar" @click="prevSubStep" />
                                    <NavButton type="submit" type-button="primary" title="Avançar" />
                                </div>
                            </form>
                        </div>

                        <!-- Subetapa 4/4 -->
                        <div v-if="subStep === 4">
                            <form @submit.prevent="nextSubStep">
                                <h4 class="mb-3">1.2 Área de Amostragem</h4>
                                <h5 class="mb-3">1.2.3 Pontos de Quelônios e Crocodilianos</h5>
                                <div class="mb-4">
                                    <div class="form-check mb-3">
                                        <input type="checkbox" class="form-check-input" id="nao_se_aplica" v-model="naoSeAplica" />
                                        <label class="form-check-label" for="nao_se_aplica">Não se aplica</label>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-12 col-md-6">
                                            <InputLabel value="Ponto de Coleta" for="ponto_coleta" />
                                            <input type="text" id="ponto_coleta" class="form-control" v-model="formPontosAmostragem.ponto_coleta" :disabled="naoSeAplica" />
                                            <InputError :message="formPontosAmostragem.errors.ponto_coleta" />
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <InputLabel value="Nome do Curso Hídrico" for="nome_curso_hidrico" />
                                            <input type="text" id="nome_curso_hidrico" class="form-control" v-model="formPontosAmostragem.nome_curso_hidrico" :disabled="naoSeAplica" />
                                            <InputError :message="formPontosAmostragem.errors.nome_curso_hidrico" />
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-12 col-md-6">
                                            <InputLabel value="Coordenadas" for="coordenadas" />
                                            <input type="text" id="coordenadas" class="form-control" v-model="formPontosAmostragem.coordenadas" :disabled="naoSeAplica" />
                                            <InputError :message="formPontosAmostragem.errors.coordenadas" />
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <InputLabel value="Bacia Hidrográfica" for="bacia" />
                                            <input type="text" id="bacia" class="form-control" v-model="formPontosAmostragem.bacia" :disabled="naoSeAplica" />
                                            <InputError :message="formPontosAmostragem.errors.bacia" />
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-12 col-md-6">
                                            <InputLabel value="Profundidade (m)" for="profundidade" />
                                            <input type="number" step="any" id="profundidade" class="form-control" v-model="formPontosAmostragem.profundidade" :disabled="naoSeAplica" />
                                            <InputError :message="formPontosAmostragem.errors.profundidade" />
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <InputLabel value="Largura (m)" for="largura" />
                                            <input type="number" step="any" id="largura" class="form-control" v-model="formPontosAmostragem.largura" :disabled="naoSeAplica" />
                                            <InputError :message="formPontosAmostragem.errors.largura" />
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-12">
                                            <InputLabel value="Tipo de Substrato" for="tipo_substrato" />
                                            <input type="text" id="tipo_substrato" class="form-control" v-model="formPontosAmostragem.tipo_substrato" :disabled="naoSeAplica" />
                                            <InputError :message="formPontosAmostragem.errors.tipo_substrato" />
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <NavButton type="button" type-button="secondary" title="Voltar" @click="prevSubStep" />
                                    <NavButton type="submit" type-button="primary" title="Finalizar" />
                                </div>
                            </form>
                        </div>

                        <!-- Modal para Cadastrar Profissional -->
                        <div v-if="showModal" class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Cadastrar Profissional</h5>
                                        <button type="button" class="btn-close" @click="showModal = false"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form @submit.prevent="salvarNovoProfissional">
                                            <div class="mb-3">
                                                <InputLabel value="Nome do Profissional" for="profissional" />
                                                <input v-model="formNovoProfissional.profissional" type="text" class="form-control" id="profissional" required />
                                                <InputError :message="formNovoProfissional.errors.profissional" />
                                            </div>
                                            <div class="mb-3">
                                                <InputLabel value="Formação" for="formacao" />
                                                <input v-model="formNovoProfissional.formacao" type="text" class="form-control" id="formacao" required />
                                                <InputError :message="formNovoProfissional.errors.formacao" />
                                            </div>
                                            <div class="d-flex justify-content-end">
                                                <NavButton type="button" type-button="secondary" title="Cancelar" @click="showModal = false" class="me-2" />
                                                <NavButton type="submit" type-button="primary" title="Salvar" />
                                            </div>
                                        </form>
                                    </div>
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
.v-select-custom {
    width: 100%;
}
.v-select-custom :deep(.vs__dropdown-toggle) {
    border: 1px solid #ced4da;
    border-radius: 0.25rem;
    padding: 0.375rem 0.75rem;
}
.v-select-custom :deep(.vs__selected) {
    margin: 2px;
}
.table-responsive {
    margin-bottom: 1rem;
}
.modal {
    z-index: 1050;
}
</style>