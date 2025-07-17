<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import NavbarContrato from '@/Pages/Sgc/Contratada/NavbarContrato.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import InputError from '@/Components/InputError.vue';
import NavButton from '@/Components/NavButton.vue';
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import DadosGeraisEditar from './Componentes/DadosGeraisEditar.vue';
import ModulosAmostraisEditar from './Componentes/ModulosAmostraisEditar.vue';
import QueloniosCrocodilianosEditar from './Componentes/QueloniosCrocodilianosEditar.vue';
import FaunaCavernicolaEditar from './Componentes/FaunaCavernicolaEditar.vue';
import MetodologiaEditar from './Componentes/MetodologiaEditar.vue';
import ResultadosEditar from './Componentes/FaunaResultadosEditar.vue';

const props = defineProps({
  campanha: {
    type: Object,
    default: () => ({}),
  },
  contrato: [Number, String],
  produto: String,
  contratos: Object,
  empreendimentos: Array,
  abios: {
    type: Array,
    default: () => [],
  },
  profissionais: {
    type: Array,
    default: () => [],
  },
});

const activeTab = ref('apresentacao');
const subStep = ref(1);

const form = useForm({
  cod_emp: props.campanha.cod_emp || '',
  subproduto: props.campanha.subproduto || '',
  data_campanha_inicial: props.campanha.data_ini || '',
  data_campanha_final: props.campanha.data_fim || '',
  periodo: props.campanha.periodo || '',
  obs: props.campanha.observacoes || '',
  abio: { id_abio: null },
  profissional: { profissional: null, grupo_faunistico: null },
  abios: props.campanha.abios?.map(abio => ({
    id: abio.id,
    abio: props.abios.find(a => a.id === abio.id) || { licenca: { numero_licenca: 'N/A' } },
  })) || [],
  profissionais: props.campanha.profissionais?.map(prof => ({
    id: prof.id,
    profissional: prof.profissional || '',
    grupo_faunistico: prof.grupo_faunistico || '',
    formacao: prof.formacao || '',
  })) || [],
  modulos_amostrais: props.campanha.modulos_amostrais?.map(modulo => ({
    data_cadastro: modulo.data_cadastro || '',
    tamanho_modulo: modulo.tamanho_modulo || '',
    uf: modulo.uf || '',
    municipio: modulo.municipio || '',
    bioma: modulo.bioma || '',
    fitofisionomia: modulo.fitofisionomia || '',
    latitude_inicial: modulo.latitude_inicial || null,
    longitude_inicial: modulo.longitude_inicial || null,
    latitude_final: modulo.latitude_final || null,
    longitude_final: modulo.longitude_final || null,
    obs: modulo.obs || '',
    arquivo: null,
  })) || [],
  pontos_quelo_crocod: props.campanha.pontos_quelo_crocod?.map(ponto => ({
    ponto_de_coleta: ponto.ponto_de_coleta || '',
    nome_curso_hidrico: ponto.nome_curso_hidrico || '',
    coordenadas: ponto.coordenadas || '',
    bacia: ponto.bacia || '',
    profundidade: ponto.profundidade || null,
    largura: ponto.largura || null,
    tipo_substrato: ponto.tipo_substrato || '',
  })) || [],
  pontos_cavernicola: props.campanha.pontos_cavernicola?.map(ponto => ({
    cavidade: ponto.cavidade || '',
    latitude: ponto.latitude || null,
    longitude: ponto.longitude || null,
    distancia_eixo_rodovia: ponto.distancia_eixo_rodovia || null,
    formacao_associada: ponto.formacao_associada || '',
    temperatura_media_interna: ponto.temperatura_media_interna || null,
    temperatura_media_externa: ponto.temperatura_media_externa || null,
    umidade_relativa_interna: ponto.umidade_relativa_interna || null,
    umidade_relativa_externa: ponto.umidade_relativa_externa || null,
  })) || [],
  nao_se_aplica: props.campanha.nao_se_aplica || false,
  metodologias: props.campanha.metodologias?.map(met => ({
    grupo_faunistico: met.grupo_faunistico || '',
    metodologia: met.metodologia || '',
  })) || [],
  resultados: props.campanha.resultados?.map(res => ({
    modulo: res.modulo || '',
    parcela: res.parcela || '',
    id_armadilha: res.id_armadilha || '',
    grupo_amostrado: res.grupo_amostrado || '',
    data_registro: res.data_registro || '',
    hora_registro: res.hora_registro || '',
    categoria: res.categoria || '',
    classe: res.classe || '',
    ordem: res.ordem || '',
    familia: res.familia || '',
    genero: res.genero || '',
    especie: res.especie || '',
    nome_comum: res.nome_comum || '',
    sexo: res.sexo || '',
    faixa_etaria: res.faixa_etaria || '',
    qnt_individuos: res.qnt_individuos || null,
    num_marcacao: res.num_marcacao || '',
    coletado: res.coletado || '',
    num_tombamento: res.num_tombamento || '',
    dados_biometricos: res.dados_biometricos || '',
    comp_total: res.comp_total || null,
    cabeca: res.cabeca || null,
    cauda: res.cauda || null,
    femur: res.femur || null,
    orelha: res.orelha || null,
    peso: res.peso || null,
    status_conservacao_federal: res.status_conservacao_federal || '',
    status_conservacao_iucn: res.status_conservacao_iucn || '',
  })) || [],
  consideracoes: props.campanha.consideracoes || '',
  planilha: null,
  anexos: {
    anuencia_proprietarios: null,
    registro_fotografico: null,
    dados_secundarios: null,
    art: null,
    ret: null,
    cr: null,
    ctf: null,
    anuencia_colecoes: null,
    oficio_atividades_campo: null,
  },
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

const vincularAbio = () => {
  if (form.abio.id_abio) {
    const abio = props.abios.find(a => a.id === form.abio.id_abio);
    if (abio) {
      form.abios.push({ id: abio.id, abio });
      form.abio.id_abio = null;
    }
  }
};

const excluirAbio = (id) => {
  form.abios = form.abios.filter(a => a.id !== id);
};

const vincularProfissional = () => {
  if (form.profissional.profissional && form.profissional.grupo_faunistico) {
    const prof = props.profissionais.find(p => p.profissional === form.profissional.profissional);
    form.profissionais.push({
      id: Date.now(), // ID temporário para renderização
      profissional: form.profissional.profissional,
      grupo_faunistico: form.profissional.grupo_faunistico,
      formacao: prof?.formacao || 'N/A',
    });
    form.profissional.profissional = null;
    form.profissional.grupo_faunistico = null;
  }
};

const excluirProfissional = (id) => {
  form.profissionais = form.profissionais.filter(p => p.id !== id);
};

const salvarNovoProfissional = (novoProfissional) => {
  form.post(route('profissionais.store'), {
    data: novoProfissional,
    onSuccess: () => {
      form.profissionais.push({
        id: Date.now(), // ID temporário
        profissional: novoProfissional.profissional,
        grupo_faunistico: form.profissional.grupo_faunistico || '',
        formacao: novoProfissional.formacao || 'N/A',
      });
    },
  });
};

const anexoTipos = [
  'anuencia_proprietarios',
  'registro_fotografico',
  'dados_secundarios',
  'art',
  'ret',
  'cr',
  'ctf',
  'anuencia_colecoes',
  'oficio_atividades_campo',
];

const formatAnexoLabel = (tipo) => {
  return tipo
    .replace(/_/g, ' ')
    .split(' ')
    .map(word => word.charAt(0).toUpperCase() + word.slice(1))
    .join(' ');
};

const submitForm = () => {
  const formData = new FormData();
  formData.append('_method', 'PUT');
  formData.append('cod_emp', form.cod_emp);
  formData.append('subproduto', form.subproduto);
  if (form.data_campanha_inicial) formData.append('data_campanha_inicial', form.data_campanha_inicial);
  if (form.data_campanha_final) formData.append('data_campanha_final', form.data_campanha_final);
  if (form.periodo) formData.append('periodo', form.periodo);
  if (form.obs) formData.append('obs', form.obs);
  if (form.nao_se_aplica) formData.append('nao_se_aplica', form.nao_se_aplica);
  if (form.consideracoes) formData.append('consideracoes', form.consideracoes);
  if (form.planilha) formData.append('planilha', form.planilha);

  form.abios.forEach((abio, index) => {
    formData.append(`abios[${index}][id]`, abio.id);
  });

  form.profissionais.forEach((prof, index) => {
    formData.append(`profissionais[${index}][profissional]`, prof.profissional);
    formData.append(`profissionais[${index}][grupo_faunistico]`, prof.grupo_faunistico);
    if (prof.formacao) formData.append(`profissionais[${index}][formacao]`, prof.formacao);
  });

  form.modulos_amostrais.forEach((modulo, index) => {
    if (modulo.data_cadastro) formData.append(`modulos_amostrais[${index}][data_cadastro]`, modulo.data_cadastro);
    if (modulo.tamanho_modulo) formData.append(`modulos_amostrais[${index}][tamanho_modulo]`, modulo.tamanho_modulo);
    if (modulo.uf) formData.append(`modulos_amostrais[${index}][uf]`, modulo.uf);
    if (modulo.municipio) formData.append(`modulos_amostrais[${index}][municipio]`, modulo.municipio);
    if (modulo.bioma) formData.append(`modulos_amostrais[${index}][bioma]`, modulo.bioma);
    if (modulo.fitofisionomia) formData.append(`modulos_amostrais[${index}][fitofisionomia]`, modulo.fitofisionomia);
    if (modulo.latitude_inicial) formData.append(`modulos_amostrais[${index}][latitude_inicial]`, modulo.latitude_inicial);
    if (modulo.longitude_inicial) formData.append(`modulos_amostrais[${index}][longitude_inicial]`, modulo.longitude_inicial);
    if (modulo.latitude_final) formData.append(`modulos_amostrais[${index}][latitude_final]`, modulo.latitude_final);
    if (modulo.longitude_final) formData.append(`modulos_amostrais[${index}][longitude_final]`, modulo.longitude_final);
    if (modulo.obs) formData.append(`modulos_amostrais[${index}][obs]`, modulo.obs);
    if (modulo.arquivo) formData.append(`modulos_amostrais[${index}][arquivo]`, modulo.arquivo);
  });

  form.pontos_quelo_crocod.forEach((ponto, index) => {
    if (ponto.ponto_de_coleta) formData.append(`pontos_quelo_crocod[${index}][ponto_de_coleta]`, ponto.ponto_de_coleta);
    if (ponto.nome_curso_hidrico) formData.append(`pontos_quelo_crocod[${index}][nome_curso_hidrico]`, ponto.nome_curso_hidrico);
    if (ponto.coordenadas) formData.append(`pontos_quelo_crocod[${index}][coordenadas]`, ponto.coordenadas);
    if (ponto.bacia) formData.append(`pontos_quelo_crocod[${index}][bacia]`, ponto.bacia);
    if (ponto.profundidade) formData.append(`pontos_quelo_crocod[${index}][profundidade]`, ponto.profundidade);
    if (ponto.largura) formData.append(`pontos_quelo_crocod[${index}][largura]`, ponto.largura);
    if (ponto.tipo_substrato) formData.append(`pontos_quelo_crocod[${index}][tipo_substrato]`, ponto.tipo_substrato);
  });

  form.pontos_cavernicola.forEach((ponto, index) => {
    if (ponto.cavidade) formData.append(`pontos_cavernicola[${index}][cavidade]`, ponto.cavidade);
    if (ponto.latitude) formData.append(`pontos_cavernicola[${index}][latitude]`, ponto.latitude);
    if (ponto.longitude) formData.append(`pontos_cavernicola[${index}][longitude]`, ponto.longitude);
    if (ponto.distancia_eixo_rodovia) formData.append(`pontos_cavernicola[${index}][distancia_eixo_rodovia]`, ponto.distancia_eixo_rodovia);
    if (ponto.formacao_associada) formData.append(`pontos_cavernicola[${index}][formacao_associada]`, ponto.formacao_associada);
    if (ponto.temperatura_media_interna) formData.append(`pontos_cavernicola[${index}][temperatura_media_interna]`, ponto.temperatura_media_interna);
    if (ponto.temperatura_media_externa) formData.append(`pontos_cavernicola[${index}][temperatura_media_externa]`, ponto.temperatura_media_externa);
    if (ponto.umidade_relativa_interna) formData.append(`pontos_cavernicola[${index}][umidade_relativa_interna]`, ponto.umidade_relativa_interna);
    if (ponto.umidade_relativa_externa) formData.append(`pontos_cavernicola[${index}][umidade_relativa_externa]`, ponto.umidade_relativa_externa);
  });

  form.metodologias.forEach((met, index) => {
    if (met.grupo_faunistico) formData.append(`metodologias[${index}][grupo_faunistico]`, met.grupo_faunistico);
    if (met.metodologia) formData.append(`metodologias[${index}][metodologia]`, met.metodologia);
  });

  form.resultados.forEach((res, index) => {
    if (res.modulo) formData.append(`resultados[${index}][modulo]`, res.modulo);
    if (res.parcela) formData.append(`resultados[${index}][parcela]`, res.parcela);
    if (res.id_armadilha) formData.append(`resultados[${index}][id_armadilha]`, res.id_armadilha);
    if (res.grupo_amostrado) formData.append(`resultados[${index}][grupo_amostrado]`, res.grupo_amostrado);
    if (res.data_registro) formData.append(`resultados[${index}][data_registro]`, res.data_registro);
    if (res.hora_registro) formData.append(`resultados[${index}][hora_registro]`, res.hora_registro);
    if (res.categoria) formData.append(`resultados[${index}][categoria]`, res.categoria);
    if (res.classe) formData.append(`resultados[${index}][classe]`, res.classe);
    if (res.ordem) formData.append(`resultados[${index}][ordem]`, res.ordem);
    if (res.familia) formData.append(`resultados[${index}][familia]`, res.familia);
    if (res.genero) formData.append(`resultados[${index}][genero]`, res.genero);
    if (res.especie) formData.append(`resultados[${index}][especie]`, res.especie);
    if (res.nome_comum) formData.append(`resultados[${index}][nome_comum]`, res.nome_comum);
    if (res.sexo) formData.append(`resultados[${index}][sexo]`, res.sexo);
    if (res.faixa_etaria) formData.append(`resultados[${index}][faixa_etaria]`, res.faixa_etaria);
    if (res.qnt_individuos) formData.append(`resultados[${index}][qnt_individuos]`, res.qnt_individuos);
    if (res.num_marcacao) formData.append(`resultados[${index}][num_marcacao]`, res.num_marcacao);
    if (res.coletado) formData.append(`resultados[${index}][coletado]`, res.coletado);
    if (res.num_tombamento) formData.append(`resultados[${index}][num_tombamento]`, res.num_tombamento);
    if (res.dados_biometricos) formData.append(`resultados[${index}][dados_biometricos]`, res.dados_biometricos);
    if (res.comp_total) formData.append(`resultados[${index}][comp_total]`, res.comp_total);
    if (res.cabeca) formData.append(`resultados[${index}][cabeca]`, res.cabeca);
    if (res.cauda) formData.append(`resultados[${index}][cauda]`, res.cauda);
    if (res.femur) formData.append(`resultados[${index}][femur]`, res.femur);
    if (res.orelha) formData.append(`resultados[${index}][orelha]`, res.orelha);
    if (res.peso) formData.append(`resultados[${index}][peso]`, res.peso);
    if (res.status_conservacao_federal) formData.append(`resultados[${index}][status_conservacao_federal]`, res.status_conservacao_federal);
    if (res.status_conservacao_iucn) formData.append(`resultados[${index}][status_conservacao_iucn]`, res.status_conservacao_iucn);
  });

  Object.keys(form.anexos).forEach(tipo => {
    if (form.anexos[tipo]) {
      formData.append(`anexos[${tipo}]`, form.anexos[tipo]);
    }
  });

  form.put(route('sgc.contratada.produtos.update', [props.contrato, props.produto, props.campanha.id]), {
    preserveState: true,
    onSuccess: () => {
      form.reset('planilha', 'anexos');
      form.anexos = {
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
    },
  });
};
</script>

<template>
  <AuthenticatedLayout>
    <template #header>
      <Breadcrumb
        :links="[
          { route: route('sgc.contratada.produtos.index', [contrato, produto.toLowerCase()]), label: contratos.contratada },
          { route: '#', label: `Editar Campanha ${props.campanha.id_campanha || props.campanha.id}` },
        ]"
      />
    </template>
    <NavbarContrato :tipo="{ id: contrato }">
      <template #body>
        <div class="card">
          <div class="card-body">
            <h2 class="text-center mb-4">EDITAR CAMPANHA {{ props.produto.toUpperCase() }}</h2>
            <h4 class="mb-3">Status: {{ props.campanha.status || 'Rejeitada' }}</h4>

            <!-- Exibir análises (motivos da rejeição) -->
            <div v-if="props.campanha.analises?.length" class="mb-6">
              <h4 class="mb-3" style="font-weight: bold;">Motivos da Rejeição</h4>
              <div class="alert alert-info">
                <ul class="list-disc pl-5">
                  <li v-for="analise in props.campanha.analises" :key="analise.id">
                    {{ analise.etapa }}: {{ analise.observacoes || 'Sem observações' }}
                  </li>
                </ul>
              </div>
            </div>

            <ul class="nav nav-tabs mb-4">
              <li class="nav-item">
                <a
                  class="nav-link"
                  :class="{ active: activeTab === 'apresentacao' }"
                  @click.prevent="setActiveTab('apresentacao')"
                >Apresentação</a>
              </li>
              <li class="nav-item">
                <a
                  class="nav-link"
                  :class="{ active: activeTab === 'metodologia' }"
                  @click.prevent="setActiveTab('metodologia')"
                >Metodologia</a>
              </li>
              <li class="nav-item">
                <a
                  class="nav-link"
                  :class="{ active: activeTab === 'resultados' }"
                  @click.prevent="setActiveTab('resultados')"
                >Resultados</a>
              </li>
              <li class="nav-item">
                <a
                  class="nav-link"
                  :class="{ active: activeTab === 'anexos' }"
                  @click.prevent="setActiveTab('anexos')"
                >Anexos</a>
              </li>
            </ul>

            <form @submit.prevent="submitForm" enctype="multipart/form-data">
              <div class="tab-content">
                <div v-if="activeTab === 'apresentacao'" class="tab-pane fade" :class="{ 'show active': activeTab === 'apresentacao' }">
                  <div v-if="subStep === 1">
                    <h4 class="text-center mb-3" style="font-weight: bold;">APRESENTAÇÃO</h4>
                    <div class="mb-3">
                        <label for="cod_emp" class="form-label">Empreendimento</label>
                        <select v-model="form.cod_emp" class="form-select" id="cod_emp" required>
                            <option value="">Selecione um empreendimento</option>
                            <option v-for="emp in props.empreendimentos" :key="emp" :value="emp">{{ emp }}</option>
                        </select>
                        <InputError :message="form.errors.cod_emp" />
                    </div>
                    <div class="mb-3">
                      <label class="form-label">Família</label>
                      <input
                        v-model="form.subproduto"
                        type="text"
                        class="form-control"
                        :disabled="props.campanha.status !== 'Rejeitada'"
                        required
                      />
                      <InputError :message="form.errors.subproduto" />
                    </div>
                    <div class="d-flex justify-content-end">
                      <NavButton
                        type="button"
                        type-button="primary"
                        title="Avançar"
                        @click="subStep = 2"
                      />
                    </div>
                    <h4 class="text-center mt-4" style="font-weight: bold; color: #6c757d;">{{ subStep }}/5</h4>
                  </div>
                  <DadosGeraisEditar
                    v-if="subStep === 2"
                    :form="form"
                    :abios="abios"
                    :profissionais="campanha.profissionais"
                    :abio-records="campanha.abios || []"
                    :profissional-records="campanha.profissionais || []"
                    :sub-step="subStep"
                    @vincular-abio="vincularAbio"
                    @excluir-abio="excluirAbio"
                    @salvar-novo-profissional="salvarNovoProfissional"
                    @vincular-profissional="vincularProfissional"
                    @excluir-profissional="excluirProfissional"
                    @next="subStep = 3"
                    @prev="prevSubStep"
                  />
                  <ModulosAmostraisEditar
                    v-if="subStep === 3"
                    :form="form"
                    :modulo-records="form.modulos_amostrais"
                    :sub-step="subStep"
                    @next="subStep = 4"
                    @prev="prevSubStep"
                  />
                  <QueloniosCrocodilianosEditar
                    v-if="subStep === 4"
                    :form="form"
                    :ponto-records="form.pontos_quelo_crocod"
                    :sub-step="subStep"
                    @next="subStep = 5"
                    @prev="prevSubStep"
                  >
                    <template #footer>
                      <h4 class="text-center mt-4" style="font-weight: bold; color: #6c757d;">{{ subStep }}/5</h4>
                    </template>
                  </QueloniosCrocodilianosEditar>
                  <FaunaCavernicolaEditar
                    v-if="subStep === 5"
                    :form="form"
                    :ponto-records="form.pontos_cavernicola"
                    :sub-step="subStep"
                    @next="setActiveTab('metodologia')"
                    @prev="prevSubStep"
                  >
                    <template #footer>
                      <h4 class="text-center mt-4" style="font-weight: bold; color: #6c757d;">{{ subStep }}/5</h4>
                    </template>
                  </FaunaCavernicolaEditar>
                </div>
                <div v-if="activeTab === 'metodologia'" class="tab-pane fade" :class="{ 'show active': activeTab === 'metodologia' }">
                  <MetodologiaEditar
                    :form="form"
                    :metodologia-records="form.metodologias"
                    @prev="setActiveTab('apresentacao')"
                    @next="setActiveTab('resultados')"
                  />
                </div>
                <div v-if="activeTab === 'resultados'" class="tab-pane fade" :class="{ 'show active': activeTab === 'resultados' }">
                  <ResultadosEditar
                    :form="form"
                    :resultados-records="form.resultados"
                    @prev="setActiveTab('metodologia')"
                    @next="setActiveTab('anexos')"
                  />
                </div>
                <div v-if="activeTab === 'anexos'" class="tab-pane fade" :class="{ 'show active': activeTab === 'anexos' }">
                  <h4 class="mb-3" style="text-align: center;">ANEXOS</h4>
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div v-for="tipo in anexoTipos" :key="tipo">
                      <label class="form-label">{{ formatAnexoLabel(tipo) }}</label>
                      <input
                        type="file"
                        @change="form.anexos[tipo] = $event.target.files[0] || null"
                        accept=".pdf,.jpg,.jpeg,.png"
                        class="form-control"
                      />
                      <InputError :message="form.errors[`anexos.${tipo}`]" />
                    </div>
                  </div>
                  <div v-if="props.campanha.anexos?.length" class="overflow-x-auto mb-6">
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
                  <div class="d-flex justify-content-between mt-4">
                    <NavButton
                      type="button"
                      type-button="secondary"
                      title="Voltar"
                      @click="setActiveTab('resultados')"
                    />
                    <NavButton
                      type="button"
                      type-button="secondary"
                      title="Cancelar"
                      @click="$router.get(route('sgc.contratada.produtos.index', [contrato, produto.toLowerCase()]))"
                    />
                    <NavButton
                      type="submit"
                      type-button="primary"
                      title="Salvar Alterações"
                      :disabled="form.processing"
                    />
                  </div>
                </div>
              </div>
            </form>

            <!-- Mensagens de Sucesso/Erro -->
            <div v-if="$page.props.flash.success" class="alert alert-success mt-4">
              {{ $page.props.flash.success }}
            </div>
            <div v-if="$page.props.flash.error" class="alert alert-danger mt-4">
              {{ $page.props.flash.error }}
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
th,
td {
  padding: 0.5rem 1rem;
  border: 1px solid #dee2e6;
}
thead {
  background-color: #f8f9fa;
}
tr:hover {
  background-color: #f1f5f9;
}
.alert {
  font-size: 1rem;
  padding: 1rem;
  border-radius: 6px;
}
.alert-info {
  background-color: #e7f1ff;
  color: #084298;
}
.alert-success {
  background-color: #d4edda;
  color: #155724;
}
.alert-danger {
  background-color: #f8d7da;
  color: #721c24;
}
.form-control {
  border: 1px solid #ced4da;
  border-radius: 4px;
  padding: 0.375rem 0.75rem;
}
.form-label {
  font-weight: 500;
  margin-bottom: 0.5rem;
}
</style>