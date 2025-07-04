<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import NavbarContrato from '@/Pages/Sgc/Contratada/NavbarContrato.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import InputError from '@/Components/InputError.vue';
import NavButton from '@/Components/NavButton.vue';
import { ref } from 'vue';
import DadosGerais from './DadosGerais.vue';
import ModulosAmostragem from './ModulosAmostrais.vue';
import QueloniosCrocodilian from './QueloniosCrocodilianos.vue';
import FaunaCavernic from './FaunaCavernicola.vue';
import Metodologia from './Metodologia.vue';
import FaunaResultados from './FaunaResultados.vue';

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
const resultadosRecords = ref([]);
const anexos = ref({
    anuencia_proprietarios: null,
    registro_fotografico: null,
    dados_secundarios: null,
    art: null,
    ret: null,
    cr: null,
    ctf: null,
    anuencia_colecoes: null,
    oficio_atividades_campo: null,
});

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
    telefone: '',
    cpf: '',
    email: '',
    curriculum_lattes: '',
    funcao: '',
    ctf: '',
    validade: '',
    conselho_de_classe: 'Não',
    numero_de_registro: null,
    status: 'Ativo',
    observacao: '',
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
const formMetodologia = useForm({
    grupo_faunistico: null,
    metodologia: '',
});
const formResultados = useForm({
    planilha: null,
    contrato: props.contrato,
    produto: props.produto,
});

// Tabelas
const abioRecords = ref([]);
const profissionalRecords = ref([]);
const moduloRecords = ref([]);
const pontoRecords = ref([]);
const pontoCavernicolaRecords = ref([]);
const metodologiaRecords = ref([]);

// Funções
const salvarDadosGerais = (consideracoesData = {}) => {
    const formData = new FormData();
    formData.append('id_campanha', formDadosGerais.id || '');
    formData.append('data_campanha_inicial', formDadosGerais.data_campanha_inicial || '');
    formData.append('data_campanha_final', formDadosGerais.data_campanha_final || '');
    formData.append('periodo', formDadosGerais.periodo || '');
    formData.append('observacoes', formDadosGerais.obs || '');
    formData.append('cod_emp', form.cod_emp || '');
    formData.append('subproduto', props.subproduto || '');
    formData.append('nao_se_aplica', naoSeAplica.value ? '1' : '0');
    formData.append('consideracoes', consideracoesData.consideracoes || '');

    // Adicionar ABIOs
    abioRecords.value.forEach((abio, index) => {
        formData.append(`id_abio[${index}]`, abio.id);
    });

    // Adicionar Profissionais
    profissionalRecords.value.forEach((prof, index) => {
        formData.append(`profissionais[${index}][profissional]`, prof.profissional || '');
        formData.append(`profissionais[${index}][grupo_faunistico]`, prof.grupo_faunistico || '');
    });

    // Adicionar Módulos Amostrais
    moduloRecords.value.forEach((modulo, index) => {
        formData.append(`modulos_amostrais[${index}][data_cadastro]`, modulo.data_cadastro || '');
        formData.append(`modulos_amostrais[${index}][tamanho_modulo]`, modulo.tamanho_modulo || '');
        formData.append(`modulos_amostrais[${index}][uf]`, modulo.uf || '');
        formData.append(`modulos_amostrais[${index}][municipio]`, modulo.municipio || '');
        formData.append(`modulos_amostrais[${index}][bioma]`, modulo.bioma || '');
        formData.append(`modulos_amostrais[${index}][fitofisionomia]`, modulo.fitofisionomia || '');
        formData.append(`modulos_amostrais[${index}][latitude_inicial]`, modulo.latitude_inicial || '');
        formData.append(`modulos_amostrais[${index}][longitude_inicial]`, modulo.longitude_inicial || '');
        formData.append(`modulos_amostrais[${index}][latitude_final]`, modulo.latitude_final || '');
        formData.append(`modulos_amostrais[${index}][longitude_final]`, modulo.longitude_final || '');
        formData.append(`modulos_amostrais[${index}][obs]`, modulo.obs || '');
        if (modulo.arquivo) {
            formData.append(`modulos_amostrais[${index}][arquivo]`, modulo.arquivo);
        }
    });

    // Adicionar Pontos Quelônios/Crocodinianos
    pontoRecords.value.forEach((ponto, index) => {
        formData.append(`pontos_quelo_crocod[${index}][ponto_de_coleta]`, ponto.ponto_de_coleta || '');
        formData.append(`pontos_quelo_crocod[${index}][nome_curso_hidrico]`, ponto.nome_curso_hidrico || '');
        formData.append(`pontos_quelo_crocod[${index}][coordenadas]`, ponto.coordenadas || '');
        formData.append(`pontos_quelo_crocod[${index}][bacia]`, ponto.bacia || '');
        formData.append(`pontos_quelo_crocod[${index}][profundidade]`, ponto.profundidade || '');
        formData.append(`pontos_quelo_crocod[${index}][largura]`, ponto.largura || '');
        formData.append(`pontos_quelo_crocod[${index}][tipo_substrato]`, ponto.tipo_substrato || '');
    });

    // Adicionar Pontos Cavernícolas
    pontoCavernicolaRecords.value.forEach((ponto, index) => {
        formData.append(`pontos_cavernicola[${index}][cavidade]`, ponto.cavidade || '');
        formData.append(`pontos_cavernicola[${index}][latitude]`, ponto.latitude || '');
        formData.append(`pontos_cavernicola[${index}][longitude]`, ponto.longitude || '');
        formData.append(`pontos_cavernicola[${index}][distancia_eixo_rodovia]`, ponto.distancia_eixo_rodovia || '');
        formData.append(`pontos_cavernicola[${index}][formacao_associada]`, ponto.formacao_associada || '');
        formData.append(`pontos_cavernicola[${index}][temperatura_media_interna]`, ponto.temperatura_media_interna || '');
        formData.append(`pontos_cavernicola[${index}][temperatura_media_externa]`, ponto.temperatura_media_externa || '');
        formData.append(`pontos_cavernicola[${index}][umidade_relativa_interna]`, ponto.umidade_relativa_interna || '');
        formData.append(`pontos_cavernicola[${index}][umidade_relativa_externa]`, ponto.umidade_relativa_externa || '');
    });

    // Adicionar Metodologias
    metodologiaRecords.value.forEach((metodo, index) => {
        formData.append(`metodologias[${index}][grupo_faunistico]`, metodo.grupo_faunistico || '');
        formData.append(`metodologias[${index}][metodologia]`, metodo.metodologia || '');
    });

    // Adicionar Resultados (planilha)
    if (formResultados.planilha) {
        formData.append('planilha', formResultados.planilha);
    }

    // Adicionar Anexos
    Object.entries(anexos.value).forEach(([key, file]) => {
        if (file) {
            formData.append(`anexos[${key}]`, file);
        }
    });

    // Log para depuração
    const formDataEntries = {};
    for (let [key, value] of formData.entries()) {
        formDataEntries[key] = value instanceof File ? { name: value.name, size: value.size } : value;
    }
    console.log('Enviando FormData para salvar campanha:', formDataEntries);

    router.post(route('sgc.contratada.produtos.salvar_campanha', [props.contrato, props.produto.toLowerCase()]), formData, {
        preserveState: true,
        forceFormData: true,
        onSuccess: () => {
            formDadosGerais.reset();
            formAbio.reset();
            formProfissional.reset();
            formNovoProfissional.reset();
            formModuloAmostral.reset();
            formPontosAmostragem.reset();
            formPontosCavernicola.reset();
            formMetodologia.reset();
            formResultados.reset();
            profissionalRecords.value = [];
            abioRecords.value = [];
            moduloRecords.value = [];
            pontoRecords.value = [];
            pontoCavernicolaRecords.value = [];
            metodologiaRecords.value = [];
            resultadosRecords.value = [];
            anexos.value = {
                anuencia_proprietarios: null,
                registro_fotografico: null,
                dados_secundarios: null,
                art: null,
                ret: null,
                cr: null,
                ctf: null,
                anuencia_colecoes: null,
                oficio_atividades_campo: null,
            };
            router.get(route('sgc.contratada.produtos.index', [props.contrato, props.produto.toLowerCase()]));
        },
        onError: (errors) => {
            console.error('Erros ao salvar campanha:', errors);
            alert('Erro ao salvar campanha: ' + (errors.error || 'Verifique os dados e tente novamente.'));
        },
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
                telefone: formNovoProfissional.telefone,
                cpf: formNovoProfissional.cpf,
                email: formNovoProfissional.email,
                curriculum_lattes: formNovoProfissional.curriculum_lattes,
                funcao: formNovoProfissional.funcao,
                ctf: formNovoProfissional.ctf,
                validade: formNovoProfissional.validade,
                conselho_de_classe: formNovoProfissional.conselho_de_classe,
                numero_de_registro: formNovoProfissional.numero_de_registro,
                status: formNovoProfissional.status,
                observacao: formNovoProfissional.observacao,
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

const adicionarMetodologia = () => {
    if (formMetodologia.grupo_faunistico && formMetodologia.metodologia) {
        metodologiaRecords.value.push({
            id: Date.now(),
            grupo_faunistico: formMetodologia.grupo_faunistico,
            metodologia: formMetodologia.metodologia,
        });
        formMetodologia.reset();
    }
};

const excluirMetodologia = (id) => {
    metodologiaRecords.value = metodologiaRecords.value.filter(item => item.id !== id);
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
        alert('Adicione pelo menos um ponto de fauna cavernícola antes de avançar, ou marque "Não se aplica".');
        return;
    }
    if (subStep.value < 5) {
        subStep.value += 1;
    } else {
        setActiveTab('metodologia');
    }
};

const prevSubStep = () => {
    if (subStep.value > 1) {
        subStep.value -= 1;
    }
};

const setActiveTab = (tab) => {
    activeTab.value = tab;
    if (tab === 'apresentacao') {
        subStep.value = 5; // Volta para a última subetapa (Fauna Cavernícola)
    }
};

const salvarAnexos = () => {
    salvarDadosGerais();
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
                                <!-- Subetapa 1/5 -->
                                <div v-if="subStep === 1">
                                    <h4 class="mb-3" style="text-align: center;">APRESENTAÇÃO</h4>
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
                                        <h4 class="text-center mt-4" style="font-weight: bold; color: #6c757d;">{{ subStep }}/5</h4>
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
                                >
                                    <template #footer>
                                        <h4 class="text-center mt-4" style="font-weight: bold; color: #6c757d;">{{ subStep }}/5</h4>
                                    </template>
                                </DadosGerais>
                                <ModulosAmostragem
                                    v-if="subStep === 3"
                                    :form-modulo-amostral="formModuloAmostral"
                                    :ufs="[
                                        { uf: 'AC' },{ uf: 'AL' },{ uf: 'AP' },{ uf: 'AM' },{ uf: 'BA' },{ uf: 'CE' },{ uf: 'DF' },{ uf: 'ES' },{ uf: 'GO' },{ uf: 'MA' },{ uf: 'MT' },{ uf: 'MS' },{ uf: 'MG' },
                                        { uf: 'PA' },{ uf: 'PB' },{ uf: 'PR' },{ uf: 'PE' },{ uf: 'PI' },{ uf: 'RJ' },{ uf: 'RN' },{ uf: 'RS' },{ uf: 'RO' },{ uf: 'RR' },{ uf: 'SC' },{ uf: 'SP' },{ uf: 'SE' },{ uf: 'TO' },
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
                                >
                                    <template #footer>
                                        <h4 class="text-center mt-4" style="font-weight: bold; color: #6c757d;">{{ subStep }}/5</h4>
                                    </template>
                                </ModulosAmostragem>
                                <QueloniosCrocodilian
                                    v-if="subStep === 4"
                                    v-model:naoSeAplica="naoSeAplica"
                                    :form-pontos-amostragem="formPontosAmostragem"
                                    :ponto-records="pontoRecords"
                                    @adicionar-ponto="adicionarPonto"
                                    @excluir-ponto="excluirPonto"
                                    @next="nextSubStep"
                                    @prev="prevSubStep"
                                >
                                    <template #footer>
                                        <h4 class="text-center mt-4" style="font-weight: bold; color: #6c757d;">{{ subStep }}/5</h4>
                                    </template>
                                </QueloniosCrocodilian>
                                <FaunaCavernic
                                    v-if="subStep === 5"
                                    v-model:naoSeAplica="naoSeAplica"
                                    :form-pontos-cavernicola="formPontosCavernicola"
                                    :ponto-cavernicola-records="pontoCavernicolaRecords"
                                    @adicionar-ponto-cavernicola="adicionarPontoCavernicola"
                                    @excluir-ponto-cavernicola="excluirPontoCavernicola"
                                    @next="nextSubStep"
                                    @prev="prevSubStep"
                                >
                                    <template #footer>
                                        <h4 class="text-center mt-4" style="font-weight: bold; color: #6c757d;">{{ subStep }}/5</h4>
                                    </template>
                                </FaunaCavernic>
                            </div>
                            <div v-if="activeTab === 'metodologia'" class="tab-pane fade" :class="{ 'show active': activeTab === 'metodologia' }">
                                <Metodologia
                                    :form-metodologia="formMetodologia"
                                    :metodologia-records="metodologiaRecords"
                                    @adicionar-metodologia="adicionarMetodologia"
                                    @excluir-metodologia="excluirMetodologia"
                                    @salvar="salvarDadosGerais"
                                    @prev="setActiveTab('apresentacao')"
                                />
                            </div>
                            <div v-if="activeTab === 'resultados'" class="tab-pane fade" :class="{ 'show active': activeTab === 'resultados' }">
                                <FaunaResultados
                                    :form-resultados="formResultados"
                                    :resultados-records.sync="resultadosRecords"
                                    :id-campanha="formDadosGerais.id || props.contrato"
                                    @update:resultadosRecords="resultadosRecords = $event"
                                    @prev="setActiveTab('metodologia')"
                                    @salvar="salvarDadosGerais"
                                />
                            </div>
                            <div v-if="activeTab === 'anexos'" class="tab-pane fade" :class="{ 'show active': activeTab === 'anexos' }">
                                <h4>ANEXOS</h4>
                                <form @submit.prevent="salvarAnexos">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="anuencia_proprietarios" class="form-label">Anuência dos Proprietários</label>
                                            <input
                                                type="file"
                                                class="form-control"
                                                id="anuencia_proprietarios"
                                                accept=".pdf,.jpg,.jpeg,.png"
                                                @change="anexos.anuencia_proprietarios = $event.target.files[0]"
                                            />
                                            <InputError :message="form.errors['anexos.anuencia_proprietarios']" />
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="registro_fotografico" class="form-label">Registro Fotográfico</label>
                                            <input
                                                type="file"
                                                class="form-control"
                                                id="registro_fotografico"
                                                accept=".pdf,.jpg,.jpeg,.png"
                                                @change="anexos.registro_fotografico = $event.target.files[0]"
                                            />
                                            <InputError :message="form.errors['anexos.registro_fotografico']" />
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="dados_secundarios" class="form-label">Dados Secundários</label>
                                            <input
                                                type="file"
                                                class="form-control"
                                                id="dados_secundarios"
                                                accept=".pdf,.jpg,.jpeg,.png"
                                                @change="anexos.dados_secundarios = $event.target.files[0]"
                                            />
                                            <InputError :message="form.errors['anexos.dados_secundarios']" />
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="art" class="form-label">ART</label>
                                            <input
                                                type="file"
                                                class="form-control"
                                                id="art"
                                                accept=".pdf,.jpg,.jpeg,.png"
                                                @change="anexos.art = $event.target.files[0]"
                                            />
                                            <InputError :message="form.errors['anexos.art']" />
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="ret" class="form-label">RET</label>
                                            <input
                                                type="file"
                                                class="form-control"
                                                id="ret"
                                                accept=".pdf,.jpg,.jpeg,.png"
                                                @change="anexos.ret = $event.target.files[0]"
                                            />
                                            <InputError :message="form.errors['anexos.ret']" />
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="cr" class="form-label">CR</label>
                                            <input
                                                type="file"
                                                class="form-control"
                                                id="cr"
                                                accept=".pdf,.jpg,.jpeg,.png"
                                                @change="anexos.cr = $event.target.files[0]"
                                            />
                                            <InputError :message="form.errors['anexos.cr']" />
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="ctf" class="form-label">CTF</label>
                                            <input
                                                type="file"
                                                class="form-control"
                                                id="ctf"
                                                accept=".pdf,.jpg,.jpeg,.png"
                                                @change="anexos.ctf = $event.target.files[0]"
                                            />
                                            <InputError :message="form.errors['anexos.ctf']" />
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="anuencia_colecoes" class="form-label">Anuência de Coleções</label>
                                            <input
                                                type="file"
                                                class="form-control"
                                                id="anuencia_colecoes"
                                                accept=".pdf,.jpg,.jpeg,.png"
                                                @change="anexos.anuencia_colecoes = $event.target.files[0]"
                                            />
                                            <InputError :message="form.errors['anexos.anuencia_colecoes']" />
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="oficio_atividades_campo" class="form-label">Ofício de Atividades de Campo</label>
                                            <input
                                                type="file"
                                                class="form-control"
                                                id="oficio_atividades_campo"
                                                accept=".pdf,.jpg,.jpeg,.png"
                                                @change="anexos.oficio_atividades_campo = $event.target.files[0]"
                                            />
                                            <InputError :message="form.errors['anexos.oficio_atividades_campo']" />
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between mt-4">
                                        <NavButton type="button" type-button="secondary" title="Voltar" @click="setActiveTab('resultados')" />
                                        <NavButton type="submit" type-button="primary" title="Salvar" />
                                    </div>
                                </form>
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