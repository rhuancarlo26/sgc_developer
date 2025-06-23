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

// Controle da aba ativa
const activeTab = ref('apresentacao');

// Controle da subetapa dentro da aba Apresentação
const subStep = ref(1);

// Controle do modal de profissional
const showModalProfissional = ref(false);

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

// Formulário de Módulo Amostral (Subetapa 3/4)
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

// Formulário de Pontos de Amostragem (Subetapa 4/4)
const formPontosAmostragem = useForm({
    ponto_de_coleta: '',
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
const moduloRecords = ref([]);
const pontoRecords = ref([]);

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

// Biomas
const biomas = [
    'Caatinga',
    'Cerrado',
    'Floresta Amazônica',
    'Mata Atlântica',
    'Mata de Araucária',
    'Mata de Cocais',
    'Pampa',
    'Pantanal',
    'Zonas Litorâneas',
];

// UFs
const ufs = [
    { uf: 'AC' }, { uf: 'AL' }, { uf: 'AP' }, { uf: 'AM' }, { uf: 'BA' },
    { uf: 'CE' }, { uf: 'DF' }, { uf: 'ES' }, { uf: 'GO' }, { uf: 'MA' },
    { uf: 'MT' }, { uf: 'MS' }, { uf: 'MG' }, { uf: 'PA' }, { uf: 'PB' },
    { uf: 'PR' }, { uf: 'PE' }, { uf: 'PI' }, { uf: 'RJ' }, { uf: 'RN' },
    { uf: 'RS' }, { uf: 'RO' }, { uf: 'RR' }, { uf: 'SC' }, { uf: 'SP' },
    { uf: 'SE' }, { uf: 'TO' },
];

// Municípios (carregados dinamicamente)
const municipios = ref([]);

// Carregar municípios via API IBGE
const getLocalizacao = async () => {
    if (!formModuloAmostral.uf) {
        municipios.value = [];
        formModuloAmostral.municipio = null;
        return;
    }

    const url = `https://servicodados.ibge.gov.br/api/v1/localidades/estados/${formModuloAmostral.uf}/municipios`;

    try {
        const response = await fetch(url);
        const data = await response.json();
        municipios.value = data.map(municipio => municipio.nome);
    } catch (e) {
        console.error('Erro ao carregar municípios:', e);
    }
};

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
        nao_se_aplica: naoSeAplica.value,
        profissionais: profissionalRecords.value.map(p => ({
            profissional: p.profissional,
            grupo_faunistico: p.grupo_faunistico,
        })),
        modulos_amostrais: moduloRecords.value.map(m => ({
            data_cadastro: m.data_cadastro,
            tamanho_modulo: m.tamanho_modulo,
            uf: m.uf,
            municipio: m.municipio,
            bioma: m.bioma,
            fitofisionomia: m.fitofisionomia,
            latitude_inicial: m.latitude_inicial,
            longitude_inicial: m.longitude_inicial,
            latitude_final: m.latitude_final,
            longitude_final: m.longitude_final,
            obs: m.obs,
            arquivo: m.arquivo,
        })),
        pontos_quelo_crocod: pontoRecords.value.map(p => ({
            ponto_de_coleta: p.ponto_de_coleta,
            nome_curso_hidrico: p.nome_curso_hidrico,
            coordenadas: p.coordenadas,
            bacia: p.bacia,
            profundidade: p.profundidade,
            largura: p.largura,
            tipo_substrato: p.tipo_substrato,
        })),
    };

    router.post(route('sgc.contratada.produtos.salvar_campanha', [props.contrato, props.produto.toLowerCase()]), data, {
        preserveState: true,
        onSuccess: () => {
            formDadosGerais.reset();
            formAbio.reset();
            formProfissional.reset();
            profissionalRecords.value = [];
            moduloRecords.value = [];
            pontoRecords.value = [];
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
            showModalProfissional.value = false;
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

// Adicionar Módulo Amostral
const adicionarModulo = () => {
    if (formModuloAmostral.data_cadastro && formModuloAmostral.tamanho_modulo && formModuloAmostral.uf && formModuloAmostral.municipio && formModuloAmostral.bioma) {
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

// Excluir Módulo
const excluirModulo = (id) => {
    moduloRecords.value = moduloRecords.value.filter(item => item.id !== id);
};

// Adicionar Ponto de Quelônio ou Crocodiliano
const adicionarPonto = () => {
    if (!naoSeAplica.value && (
        formPontosAmostragem.ponto_de_coleta &&
        formPontosAmostragem.nome_curso_hidrico &&
        formPontosAmostragem.bacia &&
        formPontosAmostragem.largura)
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
    }
};

// Excluir Ponto
const excluirPonto = (id) => {
    pontoRecords.value = pontoRecords.value.filter(item => item.id !== id);
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

// Alternar entre abas
const setActiveTab = (tab) => {
    activeTab.value = tab;
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

                        <!-- Navegação por abas -->
                        <ul class="nav nav-tabs mb-4">
                            <li class="nav-item">
                                <a
                                    class="nav-link"
                                    :class="{ 'active': activeTab === 'apresentacao' }"
                                    href="#"
                                    @click.prevent="setActiveTab('apresentacao')"
                                >Apresentação</a>
                            </li>
                            <li class="nav-item">
                                <a
                                    class="nav-link"
                                    :class="{ 'active': activeTab === 'metodologia' }"
                                    href="#"
                                    @click.prevent="setActiveTab('metodologia')"
                                >Metodologia</a>
                            </li>
                            <li class="nav-item">
                                <a
                                    class="nav-link"
                                    :class="{ 'active': activeTab === 'equipe' }"
                                    href="#"
                                    @click.prevent="setActiveTab('equipe')"
                                >Equipe</a>
                            </li>
                            <li class="nav-item">
                                <a
                                    class="nav-link"
                                    :class="{ 'active': activeTab === 'resultados' }"
                                    href="#"
                                    @click.prevent="setActiveTab('resultados')"
                                >Resultados</a>
                            </li>
                            <li class="nav-item">
                                <a
                                    class="nav-link"
                                    :class="{ 'active': activeTab === 'anexos' }"
                                    href="#"
                                    @click.prevent="setActiveTab('anexos')"
                                >Anexos</a>
                            </li>
                        </ul>

                        <!-- Conteúdo das abas -->
                        <div class="tab-content">
                            <!-- Aba Apresentação -->
                            <div v-if="activeTab === 'apresentacao'" class="tab-pane fade show active">
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
                                                    <NavButton type="button" type-button="primary" title="Cadastrar Profissional" @click="showModalProfissional = true" />
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
                                            <div class="row mb-4">
                                                <div class="col-12 col-md-6">
                                                    <InputLabel value="ID Módulo Amostral" for="id_modulo" />
                                                    <input disabled type="text" class="form-control" :value="formModuloAmostral.id" />
                                                    <InputError :message="formModuloAmostral.errors.id" />
                                                </div>
                                                <div class="col-12 col-md-6">
                                                    <InputLabel value="Data" for="data_cadastro" />
                                                    <input type="date" class="form-control" v-model="formModuloAmostral.data_cadastro" required />
                                                    <InputError :message="formModuloAmostral.errors.data_cadastro" />
                                                </div>
                                            </div>
                                            <div class="row mb-4">
                                                <div class="col-12 col-md-6">
                                                    <InputLabel value="Selecionar o tamanho do Módulo Amostral" for="tamanho_modulo" />
                                                    <div>
                                                        <label class="form-check form-check-inline me-3">
                                                            <input class="form-check-input" type="radio" name="tamanho_modulo" value="5" v-model="formModuloAmostral.tamanho_modulo" required />
                                                            <span class="form-check-label">5km</span>
                                                        </label>
                                                        <label class="form-check form-check-inline me-3">
                                                            <input class="form-check-input" type="radio" name="tamanho_modulo" value="4" v-model="formModuloAmostral.tamanho_modulo" />
                                                            <span class="form-check-label">4km</span>
                                                        </label>
                                                        <label class="form-check form-check-inline me-3">
                                                            <input class="form-check-input" type="radio" name="tamanho_modulo" value="3" v-model="formModuloAmostral.tamanho_modulo" />
                                                            <span class="form-check-label">3km</span>
                                                        </label>
                                                        <label class="form-check form-check-inline me-3">
                                                            <input class="form-check-input" type="radio" name="tamanho_modulo" value="2" v-model="formModuloAmostral.tamanho_modulo" />
                                                            <span class="form-check-label">2km</span>
                                                        </label>
                                                        <label class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio" name="tamanho_modulo" value="1" v-model="formModuloAmostral.tamanho_modulo" />
                                                            <span class="form-check-label">1km</span>
                                                        </label>
                                                    </div>
                                                    <InputError :message="formModuloAmostral.errors.tamanho_modulo" />
                                                </div>
                                                <div class="col-12 col-md-6 d-flex align-items-end">
                                                    <InputLabel :value="`Nº parcelas: ${formModuloAmostral.tamanho_modulo || 0} parcela(s)`" />
                                                </div>
                                            </div>
                                            <div class="row mb-4">
                                                <div class="col-12 col-md-4">
                                                    <InputLabel value="UF" for="uf" />
                                                    <v-select
                                                        @option:selected="getLocalizacao"
                                                        :options="ufs"
                                                        label="uf"
                                                        v-model="formModuloAmostral.uf"
                                                        :reduce="uf => uf.uf"
                                                        placeholder="Selecione uma UF"
                                                        required
                                                    >
                                                        <template #no-options>
                                                            Nenhum registro encontrado.
                                                        </template>
                                                    </v-select>
                                                    <InputError :message="formModuloAmostral.errors.uf" />
                                                </div>
                                                <div class="col-12 col-md-4">
                                                    <InputLabel value="Municípios" for="municipio" />
                                                    <v-select
                                                        :options="municipios"
                                                        v-model="formModuloAmostral.municipio"
                                                        placeholder="Selecione um município"
                                                        required
                                                    >
                                                        <template #no-options>
                                                            Nenhum registro encontrado.
                                                        </template>
                                                    </v-select>
                                                    <InputError :message="formModuloAmostral.errors.municipio" />
                                                </div>
                                                <div class="col-12 col-md-4">
                                                    <InputLabel value="Bioma" for="bioma" />
                                                    <v-select
                                                        :options="biomas"
                                                        v-model="formModuloAmostral.bioma"
                                                        placeholder="Selecione um bioma"
                                                        required
                                                    >
                                                        <template #no-options>
                                                            Nenhum registro encontrado.
                                                        </template>
                                                    </v-select>
                                                    <InputError :message="formModuloAmostral.errors.bioma" />
                                                </div>
                                            </div>
                                            <div class="row mb-4">
                                                <div class="col-12">
                                                    <InputLabel value="Fitofisionomia" for="fitofisionomia" />
                                                    <textarea class="form-control" id="fitofisionomia" rows="5" v-model="formModuloAmostral.fitofisionomia"></textarea>
                                                    <InputError :message="formModuloAmostral.errors.fitofisionomia" />
                                                </div>
                                            </div>
                                            <div class="row mb-4">
                                                <div class="col-12 col-md-6">
                                                    <InputLabel value="Latitude inicial" for="latitude_inicial" />
                                                    <input type="number" step="any" class="form-control" id="latitude_inicial" v-model="formModuloAmostral.latitude_inicial" />
                                                    <InputError :message="formModuloAmostral.errors.latitude_inicial" />
                                                </div>
                                                <div class="col-12 col-md-6">
                                                    <InputLabel value="Longitude inicial" for="longitude_inicial" />
                                                    <input type="number" step="any" class="form-control" id="longitude_inicial" v-model="formModuloAmostral.longitude_inicial" />
                                                    <InputError :message="formModuloAmostral.errors.longitude_inicial" />
                                                </div>
                                            </div>
                                            <div class="row mb-4">
                                                <div class="col-12 col-md-6">
                                                    <InputLabel value="Latitude final" for="latitude_final" />
                                                    <input type="number" step="any" class="form-control" id="latitude_final" v-model="formModuloAmostral.latitude_final" />
                                                    <InputError :message="formModuloAmostral.errors.latitude_final" />
                                                </div>
                                                <div class="col-12 col-md-6">
                                                    <InputLabel value="Longitude final" for="longitude_final" />
                                                    <input type="number" step="any" class="form-control" id="longitude_final" v-model="formModuloAmostral.longitude_final" />
                                                    <InputError :message="formModuloAmostral.errors.longitude_final" />
                                                </div>
                                            </div>
                                            <div class="row mb-4">
                                                <div class="col-12">
                                                    <InputLabel value="Shapefile" for="arquivo" />
                                                    <input @input="formModuloAmostral.arquivo = $event.target.files[0]" type="file" class="form-control" id="arquivo" accept=".shp,.zip" />
                                                    <InputError :message="formModuloAmostral.errors.arquivo" />
                                                </div>
                                            </div>
                                            <div class="row mb-4">
                                                <div class="col-12">
                                                    <InputLabel value="Observações" for="obs_modulo" />
                                                    <textarea id="obs_modulo" rows="5" class="form-control" v-model="formModuloAmostral.obs"></textarea>
                                                    <InputError :message="formModuloAmostral.errors.obs" />
                                                </div>
                                            </div>
                                            <div class="row mb-4">
                                                <div class="col d-flex justify-content-end">
                                                    <NavButton type="button" type-button="success" title="Adicionar Módulo" @click="adicionarModulo" />
                                                </div>
                                            </div>
                                            <div class="table-responsive">
                                                <Table :columns="['Data', 'Tamanho', 'UF', 'Município', 'Bioma', 'Ação']" :records="{ data: moduloRecords, links: [] }">
                                                    <template #body="{ item }">
                                                        <tr>
                                                            <td>{{ item.data_cadastro || 'N/A' }}</td>
                                                            <td>{{ item.tamanho_modulo ? `${item.tamanho_modulo}km` : 'N/A' }}</td>
                                                            <td>{{ item.uf || 'N/A' }}</td>
                                                            <td>{{ item.municipio || 'N/A' }}</td>
                                                            <td>{{ item.bioma || 'N/A' }}</td>
                                                            <td class="text-center" style="min-width: 100px;">
                                                                <NavButton @click="excluirModulo(item.id)" type-button="danger" title="Excluir">
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
                                                    <InputLabel value="Ponto de Coleta" for="ponto_de_coleta" />
                                                    <input type="text" id="ponto_de_coleta" class="form-control" v-model="formPontosAmostragem.ponto_de_coleta" :disabled="naoSeAplica" :required="!naoSeAplica" />
                                                    <InputError :message="formPontosAmostragem.errors.ponto_de_coleta" />
                                                </div>
                                                <div class="col-12 col-md-6">
                                                    <InputLabel value="Nome do Curso Hídrico" for="nome_curso_hidrico" />
                                                    <input type="text" id="nome_curso_hidrico" class="form-control" v-model="formPontosAmostragem.nome_curso_hidrico" :disabled="naoSeAplica" :required="!naoSeAplica" />
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
                                                    <input type="text" id="bacia" class="form-control" v-model="formPontosAmostragem.bacia" :disabled="naoSeAplica" :required="!naoSeAplica" />
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
                                                    <input type="number" step="any" id="largura" class="form-control" v-model="formPontosAmostragem.largura" :disabled="naoSeAplica" :required="!naoSeAplica" />
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
                                            <div class="row mb-4">
                                                <div class="col d-flex justify-content-end">
                                                    <NavButton type="button" type-button="success" title="Adicionar Ponto" @click="adicionarPonto" :disabled="naoSeAplica" />
                                                </div>
                                            </div>
                                            <div class="table-responsive">
                                                <Table :columns="['Ponto de Coleta', 'Curso Hídrico', 'Bacia', 'Largura (m)', 'Ação']" :records="{ data: pontoRecords, links: [] }">
                                                    <template #body="{ item }">
                                                        <tr>
                                                            <td>{{ item.ponto_de_coleta || 'N/A' }}</td>
                                                            <td>{{ item.nome_curso_hidrico || 'N/A' }}</td>
                                                            <td>{{ item.bacia || 'N/A' }}</td>
                                                            <td>{{ item.largura || 'N/A' }}</td>
                                                            <td class="text-center" style="min-width: 100px;">
                                                                <NavButton @click="excluirPonto(item.id)" type-button="danger" title="Excluir">
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
                                            <NavButton type="submit" type-button="primary" title="Finalizar" @click="salvarDadosGerais" />
                                        </div>
                                    </form>
                                </div>

                                <!-- Modal para Cadastrar Profissional -->
                                <div v-if="showModalProfissional" class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Cadastrar Profissional</h5>
                                                <button type="button" class="btn-close" @click="showModalProfissional = false"></button>
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
                                                        <NavButton type="button" type-button="secondary" title="Cancelar" @click="showModalProfissional = false" class="me-2" />
                                                        <NavButton type="submit" type-button="primary" title="Salvar" />
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Aba Metodologia -->
                            <div v-if="activeTab === 'metodologia'" class="tab-pane fade show active">
                                <h4 class="mb-3">METODOLOGIA</h4>
                                <p>Funcionalidade em desenvolvimento.</p>
                            </div>

                            <!-- Aba Equipe -->
                            <div v-if="activeTab === 'equipe'" class="tab-pane fade show active">
                                <h4 class="mb-3">EQUIPE</h4>
                                <p>Funcionalidade em desenvolvimento.</p>
                            </div>

                            <!-- Aba Resultados -->
                            <div v-if="activeTab === 'resultados'" class="tab-pane fade show active">
                                <h4 class="mb-3">RESULTADOS</h4>
                                <p>Funcionalidade em desenvolvimento.</p>
                            </div>

                            <!-- Aba Anexos -->
                            <div v-if="activeTab === 'anexos'" class="tab-pane fade show active">
                                <h4 class="mb-3">ANEXOS</h4>
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