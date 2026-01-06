<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import NavbarContrato from '@/Pages/Sgc/Contratada/NavbarContrato.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import InputError from '@/Components/InputError.vue';
import NavButton from '@/Components/NavButton.vue';
import { ref, onMounted, watch } from 'vue';
import DadosGerais from './DadosGerais.vue';
import ModulosAmostragem from './ModulosAmostrais.vue';
import QueloniosCrocodilian from './QueloniosCrocodilianos.vue';
import FaunaCavernic from './FaunaCavernicola.vue';
import Metodologia from './Metodologia.vue';
import FaunaResultados from './FaunaResultados.vue';
import { IconScanEye, IconTrash } from "@tabler/icons-vue";

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
    campanhaId: [Number, String, null],
    draftData: Object,
});

console.log(props.draftData)
// Log para depurar as props recebidas do backend
console.log('Create.vue props:', { abios: props.abios, profissionais: props.profissionais, campanhaId: props.campanhaId, draftData: props.draftData });

// Estado - Inicializar com dados do draft, se disponíveis
const activeTab = ref('apresentacao');
const subStep = ref(1);
const showModalProfissional = ref(false);
const naoSeAplicaQuelonios = ref(props.draftData?.nao_se_aplica_quelo ?? false);
const naoSeAplicaCavernicola = ref(props.draftData?.nao_se_aplica_cavernicola ?? false);
const resultadosRecords = ref(props.draftData?.resultados ?? []);
const anexos = ref(props.draftData?.anexos ?? {
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
    cod_emp: props.draftData?.cod_emp ?? '',
    familia: props.produto === 'Fauna' ? 'Fauna' : props.draftData?.subproduto ?? '',
});
const formDadosGerais = useForm({
    id: props.campanhaId || '',
    data_campanha_inicial: props.draftData?.data_ini ?? '',
    data_campanha_final: props.draftData?.data_fim ?? '',
    periodo: props.draftData?.periodo ?? '',
    obs: props.draftData?.observacoes ?? '',
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
    latitude: '',
    longitude: '',
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

// Tabelas - Inicializar com dados do draft
const abioRecords = ref(props.draftData?.abios ?? []);
const profissionalRecords = ref(props.draftData?.profissionais ?? []);
const moduloRecords = ref(props.draftData?.modulos_amostrais ?? []);
const pontoRecords = ref(props.draftData?.pontos_quelo_crocod ?? []);
const pontoCavernicolaRecords = ref(props.draftData?.pontos_cavernicola ?? []);
const metodologiaRecords = ref(props.draftData?.metodologias ?? []);

// Funções
const salvarDadosGerais = (consideracoesData = {}) => {
    const formData = new FormData();
    formData.append('id_campanha', formDadosGerais.id || props.campanhaId || '');
    formData.append('data_campanha_inicial', formDadosGerais.data_campanha_inicial || '');
    formData.append('data_campanha_final', formDadosGerais.data_campanha_final || '');
    formData.append('periodo', formDadosGerais.periodo || '');
    formData.append('observacoes', formDadosGerais.obs || '');
    formData.append('cod_emp', form.cod_emp || '');
    formData.append('subproduto', props.subproduto || form.familia || '');
    formData.append('nao_se_aplica_quelo', naoSeAplicaQuelonios.value ? '1' : '0');
    formData.append('nao_se_aplica_cavernicola', naoSeAplicaCavernicola.value ? '1' : '0');
    formData.append('consideracoes', formResultados.consideracoes || '');
    formData.append('status', 'Em análise');

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
        formData.append(`pontos_quelo_crocod[${index}][latitude]`, ponto.latitude || '');
        formData.append(`pontos_quelo_crocod[${index}][longitude]`, ponto.longitude || '');
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

    // Enviar até 3 planilhas separadas
    if (formResultados.planilha_terrestre) {
        formData.append('planilha_terrestre', formResultados.planilha_terrestre);
    }
    if (formResultados.planilha_aquatica) {
        formData.append('planilha_aquatica', formResultados.planilha_aquatica);
    }
    if (formResultados.planilha_cavernicola) {
        formData.append('planilha_cavernicola', formResultados.planilha_cavernicola);
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
            alert('Campanha salva com sucesso!');
            router.get(route('sgc.contratada.produtos.index', [props.contrato, props.produto.toLowerCase()]));
        },
        onError: (errors) => {
            console.error('Erros ao salvar campanha:', errors);
            let errorMessage = 'Erro ao salvar campanha: ';
            if (Object.keys(errors).some(key => key.startsWith('anexos.'))) {
                errorMessage += 'Um ou mais anexos estão inválidos. Verifique o formato (PDF, JPG, JPEG, PNG) e o tamanho (máximo 10MB).';
            } else {
                errorMessage += errors.error || 'Verifique os dados e tente novamente.';
            }
            alert(errorMessage);
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
        !naoSeAplicaQuelonios.value &&
        formPontosAmostragem.ponto_de_coleta?.trim() &&
        formPontosAmostragem.nome_curso_hidrico?.trim() &&
        formPontosAmostragem.bacia?.trim() &&
        formPontosAmostragem.largura != null
    ) {
        pontoRecords.value.push({
            id: Date.now(),
            ponto_de_coleta: formPontosAmostragem.ponto_de_coleta,
            nome_curso_hidrico: formPontosAmostragem.nome_curso_hidrico,
            latitude: formPontosAmostragem.latitude,
            longitude: formPontosAmostragem.longitude,
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
        !naoSeAplicaCavernicola.value &&
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
    if (subStep.value === 4 && !naoSeAplicaQuelonios.value && pontoRecords.value.length === 0) {
        alert('Adicione ao menos um ponto de quelônio ou crocodiliano antes de avançar, ou marque "Não se aplica".');
        return;
    }
    if (subStep.value === 5 && !naoSeAplicaCavernicola.value && pontoCavernicolaRecords.value.length === 0) {
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

// Funções anexo
const formatAnexoLabel = (tipo) => {
    console.log('Formatando label para:', tipo);
    return tipo
        .replace(/_/g, ' ')
        .split(' ')
        .map(word => word.charAt(0).toUpperCase() + word.slice(1))
        .join(' ');
};

const excluirAnexo = (tipo) => {
    console.log('Excluindo anexo:', tipo, anexos.value[tipo]);
    anexos.value[tipo] = null;
    console.log('Anexos após exclusão:', anexos.value);
    anexos.value = { ...anexos.value };
};

const updateAnexo = (tipo, file) => {
    console.log('Atualizando anexo:', tipo, file);
    anexos.value[tipo] = file || null;
    console.log('Anexos após atualização:', anexos.value);
    anexos.value = { ...anexos.value };
};

const anexosLabels = {
    anuencia_proprietarios: 'Anuência dos Proprietários',
    registro_fotografico: 'Registro Fotográfico',
    dados_secundarios: 'Dados Secundários',
    art: 'ART',
    ret: 'RET',
    cr: 'CR',
    ctf: 'CTF',
    anuencia_colecoes: 'Anuência de Coleções',
    oficio_atividades_campo: 'Ofício de Atividades de Campo'
};

// MODAL DE PREVIEW
const previewFile = ref(null);
const previewUrl = ref(null);
const previewType = ref(null);

const openPreview = (tipo) => {
    const file = anexos.value[tipo];
    if (!file) return;

    previewFile.value = file;

    if (file.type.includes("image")) {
        previewType.value = "image";
        previewUrl.value = URL.createObjectURL(file);
    } else if (file.type === "application/pdf") {
        previewType.value = "pdf";
        previewUrl.value = URL.createObjectURL(file);
    }
};

const closePreview = () => {
    previewFile.value = null;
    previewUrl.value = null;
    previewType.value = null;
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
                                        'Amazônia',
                                        'Caatinga',
                                        'Cerrado',
                                        'Mata Atlântica',
                                        'Pampa',
                                        'Pantanal',
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
                                    v-model:naoSeAplica="naoSeAplicaQuelonios"
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
                                    v-model:naoSeAplica="naoSeAplicaCavernicola"
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
                                    @next="setActiveTab('resultados')"
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
                                    @next="setActiveTab('anexos')"
                                />
                            </div>
                            <div v-if="activeTab === 'anexos'" class="tab-pane fade show active">
                                <h3 class="text-center mb-4 fw-bold">Anexos</h3>

                                <form @submit.prevent="salvarAnexos">
                                    <div class="row g-4">

                                        <!-- CARD DE UPLOAD PROFISSIONAL -->
                                        <div v-for="(label, tipo) in anexosLabels" :key="tipo" class="col-md-6">
                                            <div class="anexo-card shadow-sm p-3 rounded bg-white">

                                                <!-- Título -->
                                                <label class="form-label fw-semibold">{{ label }}</label>

                                                <!-- INPUT -->
                                                <input
                                                    type="file"
                                                    class="form-control"
                                                    :id="tipo"
                                                    accept=".pdf,.jpg,.jpeg,.png"
                                                    @change="updateAnexo(tipo, $event.target.files[0])"
                                                />

                                                <InputError :message="form.errors[`anexos.${tipo}`]" />

                                                <!-- SELECIONADO -->
                                                <div v-if="anexos[tipo]" class="mt-3 selected-box">

                                                    <!-- Header -->
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <span class="small fw-semibold">{{ anexos[tipo]?.name }}</span>

                                                        <div class="actions-icons">
                                                            <button
                                                                type="button"
                                                                class="icon-btn"
                                                                @click="openPreview(tipo)"
                                                                title="Visualizar"
                                                            >
                                                                <IconScanEye />
                                                            </button>

                                                            <button
                                                                type="button"
                                                                class="icon-btn text-danger"
                                                                @click="excluirAnexo(tipo)"
                                                                title="Excluir"
                                                            >
                                                                <IconTrash />
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>

                                    <div v-if="!Object.keys(anexos).some(k => anexos[k])"
                                        class="alert alert-info text-center mt-4">
                                        Nenhum anexo selecionado.
                                    </div>

                                    <div class="d-flex justify-content-between mt-4">
                                        <button class="btn btn-outline-secondary px-4" @click="setActiveTab('resultados')">
                                            Voltar
                                        </button>
                                        <button type="submit" class="btn btn-primary px-4 fw-semibold">
                                            Salvar
                                        </button>
                                    </div>
                                </form>

                                <!-- MODAL DE PREVIEW -->
                                <div v-if="previewFile" class="preview-modal" @click="closePreview">
                                    <div class="preview-content" @click.stop>
                                        <button class="btn-close preview-close" @click="closePreview"></button>

                                        <!-- IMG -->
                                        <img
                                            v-if="previewType === 'image'"
                                            :src="previewUrl"
                                            class="preview-image"
                                        />

                                        <!-- PDF -->
                                        <iframe
                                            v-if="previewType === 'pdf'"
                                            :src="previewUrl"
                                            class="preview-pdf"
                                        ></iframe>
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

.anexo-card {
    border: 1px solid #f1f1f1;
    transition: 0.2s ease-in-out;
}
.anexo-card:hover {
    border-color: #dedede;
    box-shadow: 0 4px 14px rgba(0,0,0,0.06);
}

.selected-box {
    background: #fafafa;
    border-radius: 6px;
    padding: 10px;
}

.preview-modal {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.65);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 999999;
    padding: 20px;
}

.preview-content {
    background: #fff;
    padding: 15px;
    border-radius: 8px;
    position: relative;
    max-width: 90vw;
    max-height: 90vh;
    overflow: hidden;
}

.preview-image {
    max-width: 100%;
    max-height: 80vh;
    object-fit: contain;
}

.preview-pdf {
    width: 80vw;
    height: 80vh;
    border: none;
}

.preview-close {
    position: absolute;
    top: 10px;
    right: 10px;
}

.actions-icons {
    display: flex;
    align-items: center;
    gap: 8px;
}

.icon-btn {
    background: #ffffff;
    border: 1px solid #dcdcdc;
    padding: 6px 8px;
    border-radius: 6px;
    cursor: pointer;
    transition: 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
}

.icon-btn i {
    font-size: 16px;
    color: #555;
}

.icon-btn:hover {
    background: #f2f2f2;
    border-color: #bbbbbb;
}

.icon-btn.text-danger i {
    color: #c0392b;
}

.icon-btn.text-danger:hover {
    background: #ffe9e9;
    border-color: #ffb3b3;
}


</style>
