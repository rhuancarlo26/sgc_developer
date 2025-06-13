```javascript
<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import NavbarContrato from '@/Pages/Sgc/Contratada/NavbarContrato.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import NavButton from '@/Components/NavButton.vue';
import Table from '@/Components/Table.vue';
import { ref, watch, computed } from 'vue';
import vSelect from 'vue-select';
import 'vue-select/dist/vue-select.css';
import { router } from '@inertiajs/vue3';
import ModalModuloAmostral from './ModalModuloAmostral.vue';

const props = defineProps({
    contrato: [Number, String],
    produto: String,
    contratos: Object,
    subproduto: [String, null],
    empreendimentos: Array,
    abios: Array,
});

console.log('Subproduto recebido em Create.vue:', props.subproduto);
console.log('Empreendimentos recebidos:', props.empreendimentos);
console.log('ABIOs recebidos:', JSON.stringify(props.abios, null, 2));

// Controle da aba atual
const currentStep = ref('apresentacao');
const steps = ['apresentacao', 'metodologias', 'equipe', 'resultados', 'anexos'];

// Controle da subetapa na aba Apresentação
const subStep = ref(1);

// Controle do alerta
const showAlert = ref(false);

// Formulário principal (Apresentação, Subetapa 1/4)
const form = useForm({
    cod_emp: '',
    descricao_revisada: props.subproduto || '',
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

// Formulário de Vincular Profissionais (Subetapa 2/4)
const formProfissional = useForm({
    id_profissional: null,
    id_grupo_faunistico: null,
});

// Formulário de Pontos de Quelônios e Crocodilianos (Subetapa 4/4)
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

// Placeholders para outras abas
const formMetodologias = useForm({});
const formEquipe = useForm({});
const formResultados = useForm({});
const formAnexos = useForm({});

// Dados para tabelas
const abioRecords = ref([]);
const profissionalRecords = ref([]);

// Mapeamento dos ABIOs para o v-select
const abioOptions = computed(() => {
    return props.abios
        .filter(abio => abio.licenca)
        .map(abio => ({
            ...abio,
            label: abio.licenca?.numero_licenca || 'Sem Licença'
        }));
});

// Monitorar seleção de ABIO
watch(() => formAbio.id_abio, (newValue) => {
    console.log('ABIO selecionado:', newValue);
});

// Mostrar alerta
const mostrarAlerta = () => {
    console.log('Exibindo alerta de ABIO');
    showAlert.value = true;
    setTimeout(() => {
        showAlert.value = false;
    }, 5000);
};

// Salvar Dados Gerais (placeholder)
const salvarDadosGerais = () => {
    console.log('Salvando Dados Gerais:', formDadosGerais.data());
};

// Salvar ABIO
const salvarAbio = () => {
    formAbio.post(route('sgc.contratada.produtos.abio.store', [props.contrato, props.produto.toLowerCase()]), {
        onSuccess: () => {
            formAbio.reset();
            console.log('ABIO salvo com sucesso');
        },
    });
};

// Excluir ABIO
const excluirAbio = (id) => {
    router.delete(route('sgc.contratada.produtos.abio.delete', [props.contrato, props.produto.toLowerCase(), id]), {
        onSuccess: () => {
            console.log('ABIO excluído com sucesso');
        },
    });
};

// Salvar Profissional (placeholder)
const salvarProfissional = () => {
    console.log('Salvando Profissional:', formProfissional.data());
    profissionalRecords.value.push({
        id: Date.now(),
        rh: { rh: { nome: formProfissional.id_profissional || 'PROF_TEST' } },
        grupo_faunistico: { grupo_faunistico: formProfissional.id_grupo_faunistico || 'GRUPO_TEST' },
    });
    formProfissional.reset();
};

// Excluir Profissional (placeholder)
const excluirProfissional = (id) => {
    console.log('Excluindo Profissional ID:', id);
    profissionalRecords.value = profissionalRecords.value.filter(item => item.id !== id);
};

// Salvar Pontos de Amostragem (placeholder)
const salvarPontosAmostragem = () => {
    console.log('Salvando Pontos de Amostragem:', formPontosAmostragem.data());
};

// Navegar entre subetapas
const nextSubStep = () => {
    if (subStep.value < 4) {
        subStep.value += 1;
    } else {
        nextStep();
    }
};

const prevSubStep = () => {
    if (subStep.value > 1) {
        subStep.value -= 1;
    }
};

// Navegar entre abas
const nextStep = () => {
    const currentIndex = steps.indexOf(currentStep.value);
    if (currentIndex < steps.length - 1) {
        currentStep.value = steps[currentIndex + 1];
        subStep.value = 1;
    } else {
        submit();
    }
};

const prevStep = () => {
    const currentIndex = steps.indexOf(currentStep.value);
    if (currentIndex > 0) {
        currentStep.value = steps[currentIndex - 1];
        subStep.value = 1;
    }
};

// Submissão final
const submit = () => {
    console.log('Enviando formulário completo:', {
        apresentacao: form.data(),
        dadosGerais: formDadosGerais.data(),
        abio: formAbio.data(),
        profissional: formProfissional.data(),
        pontosAmostragem: formPontosAmostragem.data(),
        metodologias: formMetodologias.data(),
        equipe: formEquipe.data(),
        resultados: formResultados.data(),
        anexos: formAnexos.data(),
    });
    form.post(route('sgc.contratada.produtos.store', [props.contrato, props.produto.toLowerCase()]), {
        onSuccess: () => {
            form.reset();
            currentStep.value = 'apresentacao';
            subStep.value = 1;
        },
    });
};

// Referência ao ModalModuloAmostral
const modalModuloAmostral = ref(null);
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
                                <a class="nav-link" :class="{ active: currentStep === 'apresentacao' }" @click="currentStep = 'apresentacao'; subStep = 1" href="#">Apresentação</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" :class="{ active: currentStep === 'metodologias' }" @click="currentStep = 'metodologias'; subStep = 1" href="#">Metodologias</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" :class="{ active: currentStep === 'equipe' }" @click="currentStep = 'equipe'; subStep = 1" href="#">Equipe</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" :class="{ active: currentStep === 'resultados' }" @click="currentStep = 'resultados'; subStep = 1" href="#">Resultados</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" :class="{ active: currentStep === 'anexos' }" @click="currentStep = 'anexos'; subStep = 1" href="#">Anexos</a>
                            </li>
                        </ul>

                        <!-- Conteúdo das abas -->
                        <!-- Aba Apresentação -->
                        <div v-if="currentStep === 'apresentacao'">
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
                                        <div v-if="form.errors.cod_emp" class="text-danger">{{ form.errors.cod_emp }}</div>
                                    </div>
                                    <!-- <div class="mb-3">
                                        <label for="descricao_revisada" class="form-label">Descrição Revisada</label>
                                        <input v-model="form.descricao_revisada" type="text" class="form-control" id="descricao_revisada" required />
                                        <div v-if="form.errors.descricao_revisada" class="text-danger">{{ form.errors.descricao_revisada }}</div>
                                    </div> -->
                                    <div class="mb-3">
                                        <label class="form-label">Vincular ABIO</label>
                                        <button type="button" class="btn btn-primary" @click="mostrarAlerta">Vincular ABIO</button>
                                        <div v-if="showAlert" class="alert alert-info alert-dismissible fade show mt-2" role="alert">
                                            Para prosseguir para a próxima etapa, lembre-se de cadastrar a ABIO no módulo de teste.
                                            <button type="button" class="btn-close" @click="showAlert = false"></button>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="familia" class="form-label">Família</label>
                                        <input v-model="form.familia" type="text" class="form-control" id="familia" :disabled="produto === 'Fauna'" required />
                                        <div v-if="form.errors.familia" class="text-danger">{{ form.errors.familia }}</div>
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
                                            <div class="col-12 col-md-6">
                                                <InputLabel value="Módulos Amostrais" for="modulos" />
                                                <input type="text" id="modulos" value="N/A" disabled class="form-control" />
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
                                                <InputLabel value="Selecionar Profissional" for="id_profissional" />
                                                <v-select v-model="formProfissional.id_profissional" placeholder="Selecione um profissional" class="v-select-custom" />
                                                <InputError :message="formProfissional.errors.id_profissional" />
                                            </div>
                                            <div class="col-12 col-md-5">
                                                <InputLabel value="Grupo Responsável" for="id_grupo_faunistico" />
                                                <v-select v-model="formProfissional.id_grupo_faunistico" placeholder="Selecione um grupo" class="v-select-custom" />
                                                <InputError :message="formProfissional.errors.id_grupo_faunistico" />
                                            </div>
                                            <div class="col-12 col-md-2 d-flex align-items-end">
                                                <NavButton type="button" type-button="success" title="Salvar" class="w-100" @click="salvarProfissional" />
                                            </div>
                                        </div>
                                        <div class="table-responsive">
                                            <Table :columns="['Equipe Técnica', 'Grupo Responsável', 'Ação']" :records="{ data: profissionalRecords, links: [] }">
                                                <template #body="{ item }">
                                                    <tr>
                                                        <td>{{ item.rh?.rh?.nome || 'N/A' }}</td>
                                                        <td>{{ item.grupo_faunistico?.grupo_faunistico || 'N/A' }}</td>
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
                                        <NavButton
                                            type="button"
                                            type-button="primary"
                                            title="Cadastrar Módulo"
                                            @click="modalModuloAmostral.abrirModal()"
                                        />
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <NavButton type="button" type-button="secondary" title="Voltar" @click="prevSubStep" />
                                        <NavButton type="submit" type-button="primary" title="Avançar" />
                                    </div>
                                </form>
                                <ModalModuloAmostral ref="modalModuloAmostral" />
                            </div>

                            <!-- Subetapa 4/4 -->
                            <div v-if="subStep === 4">
                                <form @submit.prevent="nextStep">
                                    <h4 class="mb-3">1.2 Área de Amostragem</h4>
                                    <h5 class="mb-3">1.2.3 Pontos de Quelônios e Crocodilianos</h5>
                                    <div class="mb-4">
                                        <div class="form-check mb-3">
                                            <input
                                                type="checkbox"
                                                class="form-check-input"
                                                id="nao_se_aplica"
                                                v-model="naoSeAplica"
                                            />
                                            <label class="form-check-label" for="nao_se_aplica">Não se aplica</label>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-12 col-md-6">
                                                <InputLabel value="Ponto de Coleta" for="ponto_coleta" />
                                                <input
                                                    type="text"
                                                    id="ponto_coleta"
                                                    class="form-control"
                                                    v-model="formPontosAmostragem.ponto_coleta"
                                                    :disabled="naoSeAplica"
                                                />
                                                <InputError :message="formPontosAmostragem.errors.ponto_coleta" />
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <InputLabel value="Nome do Curso Hídrico" for="nome_curso_hidrico" />
                                                <input
                                                    type="text"
                                                    id="nome_curso_hidrico"
                                                    class="form-control"
                                                    v-model="formPontosAmostragem.nome_curso_hidrico"
                                                    :disabled="naoSeAplica"
                                                />
                                                <InputError :message="formPontosAmostragem.errors.nome_curso_hidrico" />
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-12 col-md-6">
                                                <InputLabel value="Coordenadas" for="coordenadas" />
                                                <input
                                                    type="text"
                                                    id="coordenadas"
                                                    class="form-control"
                                                    v-model="formPontosAmostragem.coordenadas"
                                                    :disabled="naoSeAplica"
                                                />
                                                <InputError :message="formPontosAmostragem.errors.coordenadas" />
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <InputLabel value="Bacia Hidrográfica" for="bacia" />
                                                <input
                                                    type="text"
                                                    id="bacia"
                                                    class="form-control"
                                                    v-model="formPontosAmostragem.bacia"
                                                    :disabled="naoSeAplica"
                                                />
                                                <InputError :message="formPontosAmostragem.errors.bacia" />
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-12 col-md-6">
                                                <InputLabel value="Profundidade (m)" for="profundidade" />
                                                <input
                                                    type="number"
                                                    step="any"
                                                    id="profundidade"
                                                    class="form-control"
                                                    v-model="formPontosAmostragem.profundidade"
                                                    :disabled="naoSeAplica"
                                                />
                                                <InputError :message="formPontosAmostragem.errors.profundidade" />
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <InputLabel value="Largura (m)" for="largura" />
                                                <input
                                                    type="number"
                                                    step="any"
                                                    id="largura"
                                                    class="form-control"
                                                    v-model="formPontosAmostragem.largura"
                                                    :disabled="naoSeAplica"
                                                />
                                                <InputError :message="formPontosAmostragem.errors.largura" />
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-12">
                                                <InputLabel value="Tipo de Substrato" for="tipo_substrato" />
                                                <input
                                                    type="text"
                                                    id="tipo_substrato"
                                                    class="form-control"
                                                    v-model="formPontosAmostragem.tipo_substrato"
                                                    :disabled="naoSeAplica"
                                                />
                                                <InputError :message="formPontosAmostragem.errors.tipo_substrato" />
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col d-flex justify-content-end">
                                                <NavButton
                                                    type="button"
                                                    type-button="success"
                                                    title="Salvar"
                                                    @click="salvarPontosAmostragem"
                                                />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <NavButton type="button" type-button="secondary" title="Voltar" @click="prevSubStep" />
                                        <NavButton type="submit" type-button="primary" title="Avançar" />
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Aba Metodologias -->
                        <div v-if="currentStep === 'metodologias'">
                            <form @submit.prevent="nextStep">
                                <h4 class="mb-3">Metodologias</h4>
                                <p>Em desenvolvimento. Adicione aqui as metodologias utilizadas.</p>
                                <div class="d-flex justify-content-between">
                                    <NavButton type="button" type-button="secondary" title="Voltar" @click="prevStep" />
                                    <NavButton type="submit" type-button="primary" title="Avançar" />
                                </div>
                            </form>
                        </div>

                        <!-- Aba Equipe -->
                        <div v-if="currentStep === 'equipe'">
                            <form @submit.prevent="nextStep">
                                <h4 class="mb-3">Equipe</h4>
                                <p>Em desenvolvimento. Liste os membros da equipe.</p>
                                <div class="d-flex justify-content-between">
                                    <NavButton type="button" type-button="secondary" title="Voltar" @click="prevStep" />
                                    <NavButton type="submit" type-button="primary" title="Avançar" />
                                </div>
                            </form>
                        </div>

                        <!-- Aba Resultados -->
                        <div v-if="currentStep === 'resultados'">
                            <form @submit.prevent="nextStep">
                                <h4 class="mb-3">Resultados</h4>
                                <p>Em desenvolvimento. Insira os resultados obtidos.</p>
                                <div class="d-flex justify-content-between">
                                    <NavButton type="button" type-button="secondary" title="Voltar" @click="prevStep" />
                                    <NavButton type="submit" type-button="primary" title="Avançar" />
                                </div>
                            </form>
                        </div>

                        <!-- Aba Anexos -->
                        <div v-if="currentStep === 'anexos'">
                            <form @submit.prevent="submit">
                                <h4 class="mb-3">Anexos</h4>
                                <p>Em desenvolvimento. Adicione arquivos relevantes.</p>
                                <div class="d-flex justify-content-between">
                                    <NavButton type="button" type-button="secondary" title="Voltar" @click="prevStep" />
                                    <NavButton type="submit" type-button="primary" title="Finalizar" />
                                </div>
                            </form>
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
.nav-tabs .nav-link {
    cursor: pointer;
}
.alert-dismissible {
    position: relative;
    padding-right: 4rem;
}
</style>
```