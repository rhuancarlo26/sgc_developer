<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import NavbarContrato from '@/Pages/Sgc/Contratada/NavbarContrato.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import NavButton from '@/Components/NavButton.vue';
import { ref } from 'vue';
import DadosGerais from './DadosGerais.vue';
import ModulosAmostragem from './ModulosAmostrais.vue';
import QueloniosCrocodilian from './QueloniosCrocodilianos.vue';
import FaunaCavernic from './FaunaCavernicola.vue';

// Props
const props = defineProps({
    contrato: [Number, String],
    produto: String,
    contratos: Object,
    subproduto: [String, null],
    empreendimentos: Array,
    abios: {
        type: Array,
        default: () => []
    },
    profissionais: {
        type: Array,
        default: () => []
    },
});

// Log para depurar as props recebidas do backend
console.log('Create.vue props:', { abios: props.abios, profissionais: props.profissionais });

// Estado
const activeTab = ref('apresentacao');
const subStep = ref(1);
const showModalProfissional = ref(false);
const naoSeAplica = ref(false);

// Formulários
const form = useForm({
    cod_emp: '',
    familia: props.produto === 'Fauna' ? 'Fauna' : '',
});
const formDadosGerais = useForm({
    id: '',
    data_campanha_inicial: '',
    data_campanha_final: '',
    periodo: '',
    obs: '',
});
const formAbio = useForm({
    id_abio: null,
});
const formProfissional = useForm({
    profissional: null,
    grupo_faunistico: null,
});
const formNovoProfissional = useForm({
    profissional: '',
    formacao: '',
});
const formModuloAmostral = useForm({
    data_cadastro: null,
    tamanho_modulo: null,
    uf: null,
    municipio: null,
    bioma: null,
    fitofisionomia: null,
    latitude_inicial: null,
    longitude_inicial: null,
    latitude_final: null,
    longitude_final: null,
    obs: null,
    arquivo: null,
});
const formPontosAmostragem = useForm({
    ponto_de_coleta: '',
    nome_curso_hidrico: '',
    coordenadas: '',
    bacia: '',
    profundidade: null,
    largura: null,
    tipo_substrato: '',
});
const formPontosCavernicola = useForm({
    cavidade: '',
    latitude: null,
    longitude: null,
    distancia_eixo_rodovia: null,
    formacao_associada: '',
    temperatura_media_interna: null,
    temperatura_media_externa: null,
    umidade_relativa_interna: null,
    umidade_relativa_externa: null,
});

// Tabelas
const abioRecords = ref([]);
const profissionalRecords = ref([]);
const moduloRecords = ref([]);
const pontoRecords = ref([]);
const pontoCavernicolaRecords = ref([]);

// Funções
const salvarDadosGerais = () => {
    const data = {
        id_campanha: formDadosGerais.id,
        data_campanha_inicial: formDadosGerais.data_campanha_inicial,
        data_campanha_final: formDadosGerais.data_campanha_final,
        periodo: formDadosGerais.periodo,
        observacoes: formDadosGerais.obs,
        id_abio: abioRecords.value.map(a => a.id), // Enviar array de IDs de ABIOs
        cod_emp: form.cod_emp,
        subproduto: props.subproduto || '',
        nao_se_aplica: naoSeAplica.value,
        profissionais: profissionalRecords.value.map(p => ({
            profissional: p.profissional,
            grupo_faunistico: p.grupo_faunistico,
        })),
        modulos_amostrais: moduloRecords.value,
        pontos_quelo_crocod: pontoRecords.value,
        pontos_cavernicola: pontoCavernicolaRecords.value,
    };
    console.log('Enviando dados para salvar campanha:', JSON.stringify(data, null, 2));
    router.post(route('sgc.contratada.produtos.salvar_campanha', [props.contrato, props.produto.toLowerCase()]), data, {
        preserveState: true,
        onSuccess: () => {
            formDadosGerais.reset();
            formAbio.reset();
            formProfissional.reset();
            profissionalRecords.value = [];
            abioRecords.value = []; // Resetar abioRecords após salvamento
            moduloRecords.value = [];
            pontoRecords.value = [];
            pontoCavernicolaRecords.value = [];
            router.get(route('sgc.contratada.produtos.index', [props.contrato, props.produto.toLowerCase()]));
        },
        onError: (errors) => console.error('Erros ao salvar campanha:', errors),
    });
};

const vincularAbio = () => {
    if (formAbio.id_abio) {
        const abioData = props.abios.find(a => a.id === formAbio.id_abio);
        if (abioData) {
            abioRecords.value.push({
                id: abioData.id,
                abio: {
                    licenca: { numero_licenca: abioData.licenca?.numero_licenca || 'N/A' },
                },
            });
            formAbio.reset();
        }
    }
};

const excluirAbio = (id) => {
    abioRecords.value = abioRecords.value.filter(item => item.id !== id);
};

const salvarNovoProfissional = () => {
    formNovoProfissional.post(route('sgc.contratada.produtos.profissional.store', [props.contrato, props.produto.toLowerCase()]), {
        onSuccess: () => {
            props.profissionais.push({
                id: Date.now(),
                profissional: formNovoProfissional.profissional,
                formacao: formNovoProfissional.formacao,
            });
            formNovoProfissional.reset();
            showModalProfissional.value = false;
        },
        onError: (errors) => console.error('Erro ao salvar profissional:', errors),
    });
};

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

const excluirProfissional = (id) => {
    profissionalRecords.value = profissionalRecords.value.filter(item => item.id !== id);
};

const adicionarModulo = () => {
    if (
        formModuloAmostral.data_cadastro &&
        formModuloAmostral.tamanho_modulo &&
        formModuloAmostral.uf &&
        formModuloAmostral.municipio &&
        formModuloAmostral.bioma
    ) {
        moduloRecords.value.push({
            id: Date.now(),
            data_cadastro: formModuloAmostral.data_cadastro,
            tamanho_modulo: formModuloAmostral.tamanho_modulo,
            uf: formModuloAmostral.uf,
            municipio: formModuloAmostral.municipio,
            bioma: formModuloAmostral.bioma,
            fitofisionomia: formModuloAmostral.fitofisionomia,
            latitude_inicial: formModuloAmostral.latitude_inicial,
            longitude_inicial: formModuloAmostral.longitude_inicial,
            latitude_final: formModuloAmostral.latitude_final,
            longitude_final: formModuloAmostral.longitude_final,
            obs: formModuloAmostral.obs,
            arquivo: formModuloAmostral.arquivo,
        });
        formModuloAmostral.reset();
    }
};

const excluirModulo = (id) => {
    moduloRecords.value = moduloRecords.value.filter(item => item.id !== id);
};

const adicionarPonto = () => {
    console.log('adicionarPonto chamado', {
        naoSeAplica: naoSeAplica.value,
        formPontosAmostragem: {
            ponto_de_coleta: formPontosAmostragem.ponto_de_coleta,
            nome_curso_hidrico: formPontosAmostragem.nome_curso_hidrico,
            bacia: formPontosAmostragem.bacia,
            largura: formPontosAmostragem.largura,
            coordenadas: formPontosAmostragem.coordenadas,
            profundidade: formPontosAmostragem.profundidade,
            tipo_substrato: formPontosAmostragem.tipo_substrato,
        },
    });

    if (
        !naoSeAplica.value &&
        formPontosAmostragem.ponto_de_coleta?.trim() &&
        formPontosAmostragem.nome_curso_hidrico?.trim() &&
        formPontosAmostragem.bacia?.trim() &&
        formPontosAmostragem.largura != null
    ) {
        pontoRecords.value.push({
            id: Date.now(),
            ponto_de_coleta: formPontosAmostragem.ponto_de_coleta,
            nome_curso_hidrico: formPontosAmostragem.nome_curso_hidrico,
            coordenadas: formPontosAmostragem.coordenadas,
            bacia: formPontosAmostragem.bacia,
            profundidade: formPontosAmostragem.profundidade,
            largura: formPontosAmostragem.largura,
            tipo_substrato: formPontosAmostragem.tipo_substrato,
        });
        formPontosAmostragem.reset();
    } else {
        alert('Preencha todos os campos obrigatórios: Ponto de Coleta, Nome do Curso Hídrico, Bacia Hidrográfica e Largura.');
    }
};

const excluirPonto = (id) => {
    pontoRecords.value = pontoRecords.value.filter(p => p.id !== id);
};

const adicionarPontoCavernicola = () => {
    if (
        !naoSeAplica.value &&
        formPontosCavernicola.cavidade &&
        formPontosCavernicola.latitude !== null &&
        formPontosCavernicola.longitude !== null &&
        formPontosCavernicola.distancia_eixo_rodovia !== null &&
        formPontosCavernicola.formacao_associada
    ) {
        pontoCavernicolaRecords.value.push({
            id: Date.now(),
            cavidade: formPontosCavernicola.cavidade,
            latitude: formPontosCavernicola.latitude,
            longitude: formPontosCavernicola.longitude,
            distancia_eixo_rodovia: formPontosCavernicola.distancia_eixo_rodovia,
            formacao_associada: formPontosCavernicola.formacao_associada,
            temperatura_media_interna: formPontosCavernicola.temperatura_media_interna,
            temperatura_media_externa: formPontosCavernicola.temperatura_media_externa,
            umidade_relativa_interna: formPontosCavernicola.umidade_relativa_interna,
            umidade_relativa_externa: formPontosCavernicola.umidade_relativa_externa,
        });
        formPontosCavernicola.reset();
    }
};

const excluirPontoCavernicola = (id) => {
    pontoCavernicolaRecords.value = pontoCavernicolaRecords.value.filter(p => p.id !== id);
};

const nextSubStep = () => {
    if (subStep.value === 3 && moduloRecords.value.length === 0) {
        alert('Adicione ao menos um módulo amostral antes de avançar.');
        return;
    }
    if (subStep.value === 4 && !naoSeAplica.value && pontoRecords.value.length === 0) {
        alert('Adicione ao menos um ponto de quelônio ou crocodiliano antes de avançar, ou marque "Não se aplica".');
        return;
    }
    if (subStep.value === 5 && !naoSeAplica.value && pontoCavernicolaRecords.value.length === 0) {
        alert('Adicione pelo menos um ponto de fauna cavernícola antes de finalizar, ou marque "Não se aplica".');
        return;
    }
    if (subStep.value < 5) {
        subStep.value += 1;
    }
};

const prevSubStep = () => {
    if (subStep.value > 1) {
        subStep.value -= 1;
    }
};

const setActiveTab = (tab) => {
    activeTab.value = tab;
};
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <Breadcrumb
                :links="[
                    { route: route('sgc.gestao.listagem', props.contratos.tipo_contrato), label: 'Gestão de Contratos' },
                    { route: route('sgc.contratada.produtos.index', [props.contrato, props.produto.toLowerCase()]), label: props.contratos.contratada },
                    { route: '#', label: `Cadastrar ${props.produto}` },
                ]"
            />
        </template>
        <NavbarContrato :tipo="{ id: props.contrato }">
            <template #body>
                <div class="card">
                    <div class="card-body">
                        <h2 class="text-center mb-4">CADASTRAR {{ props.produto.toUpperCase() }}</h2>
                        <ul class="nav nav-tabs mb-4">
                            <li class="nav-item">
                                <a
                                    class="nav-link"
                                    :class="{ 'active': activeTab === 'apresentacao' }"
                                    @click.prevent="setActiveTab('apresentacao')"
                                    >Apresentação</a
                                >
                            </li>
                            <li class="nav-item">
                                <a
                                    class="nav-link"
                                    :class="{ 'active': activeTab === 'metodologia' }"
                                    @click.prevent="setActiveTab('metodologia')"
                                    >Metodologia</a
                                >
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" :class="{ 'active': activeTab === 'equipe' }" @click.prevent="setActiveTab('equipe')"
                                    >Equipe</a
                                >
                            </li>
                            <li class="nav-item">
                                <a
                                    class="nav-link"
                                    :class="{ 'active': activeTab === 'resultados' }"
                                    @click.prevent="setActiveTab('resultados')"
                                    >Resultados</a
                                >
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" :class="{ 'active': activeTab === 'anexos' }" @click.prevent="setActiveTab('anexos')"
                                    >Anexos</a
                                >
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div v-if="activeTab === 'apresentacao'" class="tab-pane fade show active">
                                <h5 class="mb-3">Etapa {{ subStep }}/5</h5>
                                <!-- Subetapa 1/5 -->
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
                                            <input
                                                v-model="form.familia"
                                                type="text"
                                                class="form-control"
                                                id="familia"
                                                :disabled="props.produto === 'Fauna'"
                                                required
                                            />
                                            <InputError :message="form.errors.familia" />
                                        </div>
                                        <div class="d-flex justify-content-end">
                                            <NavButton type="submit" type-button="primary" title="Avançar" />
                                        </div>
                                    </form>
                                </div>
                                <DadosGerais
                                    v-if="subStep === 2"
                                    :form-dados-gerais="formDadosGerais"
                                    :form-abio="formAbio"
                                    :form-profissional="formProfissional"
                                    :form-novo-profissional="formNovoProfissional"
                                    :abios="props.abios"
                                    :profissionais="props.profissionais"
                                    :abio-records="abioRecords"
                                    :profissional-records="profissionalRecords"
                                    @vincular-abio="vincularAbio"
                                    @excluir-abio="excluirAbio"
                                    @salvar-novo-profissional="salvarNovoProfissional"
                                    @vincular-profissional="vincularProfissional"
                                    @excluir-profissional="excluirProfissional"
                                    @next="nextSubStep"
                                    @prev="prevSubStep"
                                />
                                <ModulosAmostragem
                                    v-if="subStep === 3"
                                    :form-modulo-amostral="formModuloAmostral"
                                    :ufs="[
                                        { uf: 'AC' },{ uf: 'AL' },{ uf: 'AP' },{ uf: 'AM' },{ uf: 'BA' },{ uf: 'CE' },{ uf: 'DF' },{ uf: 'ES' },{ uf: 'GO' },{ uf: 'MA' },{ uf: 'MT' },{ uf: 'MS' },{ uf: 'MG' },{ uf: 'PA' },
                                        { uf: 'PB' },{ uf: 'PR' },{ uf: 'PE' },{ uf: 'PI' },{ uf: 'RJ' },{ uf: 'RN' },{ uf: 'RS' },{ uf: 'RO' },{ uf: 'RR' },{ uf: 'SC' },{ uf: 'SP' },{ uf: 'SE' },{ uf: 'TO' },
                                    ]"
                                    :biomas="[
                                        'Caatinga',
                                        'Cerrado',
                                        'Floresta Amazônica',
                                        'Mata Atlântica',
                                        'Mata de Araucária',
                                        'Mata de Cocais',
                                        'Pampa',
                                        'Pantanal',
                                        'Zonas Litorâneas',
                                    ]"
                                    :modulo-records="moduloRecords"
                                    @adicionar-modulo="adicionarModulo"
                                    @excluir-modulo="excluirModulo"
                                    @next="nextSubStep"
                                    @prev="prevSubStep"
                                />
                                <QueloniosCrocodilian
                                    v-if="subStep === 4"
                                    v-model:naoSeAplica="naoSeAplica"
                                    :form-pontos-amostragem="formPontosAmostragem"
                                    :ponto-records="pontoRecords"
                                    @adicionar-ponto="adicionarPonto"
                                    @excluir-ponto="excluirPonto"
                                    @next="nextSubStep"
                                    @prev="prevSubStep"
                                />
                                <FaunaCavernic
                                    v-if="subStep === 5"
                                    v-model:naoSeAplica="naoSeAplica"
                                    :form-pontos-cavernicola="formPontosCavernicola"
                                    :ponto-cavernicola-records="pontoCavernicolaRecords"
                                    @adicionar-ponto-cavernicola="adicionarPontoCavernicola"
                                    @excluir-ponto-cavernicola="excluirPontoCavernicola"
                                    @salvar="salvarDadosGerais"
                                    @prev="prevSubStep"
                                />
                            </div>
                            <div v-if="activeTab === 'metodologia'" class="tab-pane fade show active">
                                <h4>METODOLOGIA</h4>
                                <p>Funcionalidade em desenvolvimento.</p>
                            </div>
                            <div v-if="activeTab === 'equipe'" class="tab-pane fade show active">
                                <h4>EQUIPE</h4>
                                <p>Funcionalidade em desenvolvimento.</p>
                            </div>
                            <div v-if="activeTab === 'resultados'" class="tab-pane fade show active">
                                <h4>RESULTADOS</h4>
                                <p>Funcionalidade em desenvolvimento.</p>
                            </div>
                            <div v-if="activeTab === 'anexos'" class="tab-pane fade show active">
                                <h4>ANEXOS</h4>
                                <p>Funcionalidade em desenvolvimento.</p>
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
.nav-tabs {
    border-bottom: 1px solid #dee2e6;
}
.nav-tabs .nav-link {
    color: #495057;
    padding: 0.75rem 1.5rem;
}
.nav-tabs .nav-link.active {
    color: #0d6efd;
    border-color: #dee2e6 #dee2e6 #fff;
    background-color: #fff;
}
.tab-content {
    padding: 1rem 0;
}
</style>