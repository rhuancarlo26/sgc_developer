<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import NavbarContrato from '@/Pages/Sgc/Contratada/NavbarContrato.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import InputError from '@/Components/InputError.vue';
import { computed, ref, watch } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import axios from 'axios';
import MapLayer from './MapViewer.vue';
import ResultadosGeoserver from './ResultadosGeoserver.vue';
import Resultados from './Resultados.vue';
import ResultadosProspeccao from './ResultadosProspeccao.vue';
import VisualizarAnexos from './VisualizarAnexos.vue';
import Anexos from './Anexos.vue';

const props = defineProps({
  campanha: {
    type: Object,
    default: () => ({}),
  },
  contrato: [Number, String],
  produto: String,
  contratos: Object,
  canApprove: Boolean,
  coordenadas: [Object, String, Array],
});

const isResumoView = computed(() => {
  if (typeof window === 'undefined') return false;
  return new URLSearchParams(window.location.search).get('modo') === 'resumo';
});

const showAnalysisModal = ref(false);

const motivoReprovacao = computed(() => {
  const status = props.campanha?.status;
  const observacoes = (props.campanha?.observacoes_analise || '').trim();
  const statusReprovado = status === 'Reprovada' || status === 'Rejeitada';

  return statusReprovado && observacoes ? observacoes : '';
});

const statusClass = computed(() => ({
  'bg-warning text-white': props.campanha.status === 'Em análise',
  'bg-success text-white': props.campanha.status === 'Aprovada',
  'bg-danger text-white': props.campanha.status === 'Reprovada' || props.campanha.status === 'Rejeitada',
  'bg-secondary text-white': props.campanha.status === 'Em elaboração',
}));

const statusDisplay = computed(() => {
  if (props.campanha.status === 'Reprovada' || props.campanha.status === 'Rejeitada') {
    return 'Em revisão';
  }
  return props.campanha.status;
});

const activeTab = ref('apresentacao');
const anexosLocais = ref(Array.isArray(props.campanha?.anexos) ? [...props.campanha.anexos] : []);
const resultadosAnexosLocais = ref(Array.isArray(props.campanha?.resultados_anexos) ? [...props.campanha.resultados_anexos] : []);

const formAprovacao = useForm({
  status: '',
  observacoes: '',
});

watch(
  () => props.campanha?.anexos,
  (newVal) => {
    anexosLocais.value = Array.isArray(newVal) ? [...newVal] : [];
  },
  { deep: true }
);

watch(
  () => props.campanha?.resultados_anexos,
  (newVal) => {
    resultadosAnexosLocais.value = Array.isArray(newVal) ? [...newVal] : [];
  },
  { deep: true }
);

const hasCoordenadas = computed(() => {
  const coords = props.coordenadas;

  if (Array.isArray(coords)) {
    return coords.length > 0;
  }

  if (coords && typeof coords === 'object') {
    return Object.keys(coords).length > 0;
  }

  if (typeof coords === 'string') {
    const texto = coords.trim();
    return texto.length > 0 && texto !== '[]' && texto !== '{}';
  }

  return false;
});

const normalizeText = (value) =>
  String(value || '')
    .normalize('NFD')
    .replace(/[\u0300-\u036f]/g, '')
    .toLowerCase();

const tiposGeoserverPadrao = [
  'geologico',
  'geomorfologico',
  'hipsometrico',
  'declividades',
  'hidrografico',
  'cavidades',
  'limites_areas',
  'potencial_inicial',
  'potencial_reclassificado',
  'projeto_engenharia',
  'estudos_posteriores',
];

const tiposGeoserverProspeccao = [
//   'feicoes',
  'feicoes_carsticas_identificadas',
  'cavidades_nao_encontradas',
  'cavidades_cecav_canie',
  'caminhamento',
  'raio_de_250m_de_cavidades',
  'curvas_de_nivel',
  'extensao_rodovia_prospectada',
];

const isProspeccao = computed(() => normalizeText(props.campanha?.subproduto).includes('prospeccao'));

const tiposGeoserverEsperados = computed(() =>
  isProspeccao.value ? tiposGeoserverProspeccao : tiposGeoserverPadrao
);

const tiposResultadosRegistrados = computed(() => {
  const resultados = Array.isArray(resultadosAnexosLocais.value) ? resultadosAnexosLocais.value : [];
  return new Set(resultados.map((item) => item.tipo).filter(Boolean));
});

const geoLayersCampanha = ref([]);

const carregarGeoLayersCampanha = async () => {
  if (!props.campanha?.id) {
    geoLayersCampanha.value = [];
    return;
  }

  try {
    const { data } = await axios.get(route('sgc.contratada.espeleologia.layers.index'), {
      params: { campanha_id: props.campanha.id },
    });
    geoLayersCampanha.value = Array.isArray(data) ? data : [];
  } catch {
    geoLayersCampanha.value = [];
  }
};

watch(
  () => props.campanha?.id,
  () => {
    carregarGeoLayersCampanha();
  },
  { immediate: true }
);

const tiposGeoserverComMapa = computed(() => {
  const layers = Array.isArray(geoLayersCampanha.value) ? geoLayersCampanha.value : [];
  return new Set(layers.map((layer) => layer?.tipo).filter(Boolean));
});

const subAbasGeoserver = computed(() =>
  tiposGeoserverEsperados.value.map((tipo) => ({
    nome: formatAnexoLabel(tipo),
    completo: tiposGeoserverComMapa.value.has(tipo),
  }))
);

const etapasGeoserver = computed(() => subAbasGeoserver.value);

const etapasGeoserverCompletas = computed(() =>
  etapasGeoserver.value.filter((etapa) => etapa.completo).length
);

const percentualGeoserver = computed(() => {
  const total = etapasGeoserver.value.length;
  if (!total) return 0;
  return Math.round((etapasGeoserverCompletas.value / total) * 100);
});

const abasPrimarias = computed(() => {
  const campanha = props.campanha || {};
  const temDadosApresentacao = Boolean(
    campanha.cod_emp || campanha.subproduto || campanha.subtrecho || campanha.segmento || hasCoordenadas.value
  );
  const temResultados = Array.isArray(resultadosAnexosLocais.value) && resultadosAnexosLocais.value.length > 0;

  return [
    { nome: 'Apresentação', completo: temDadosApresentacao },
    { nome: 'Metodologias', completo: Boolean((campanha.metodologia || '').trim()) },
    { nome: 'Resultados', completo: temResultados },
    {
      nome: `Resultados Geoserver (${percentualGeoserver.value}%)`,
      completo: etapasGeoserverCompletas.value > 0,
    },
    { nome: 'Anexos', completo: anexosLocais.value.length > 0 },
  ];
});

const etapasPreenchimento = computed(() => [...abasPrimarias.value, ...etapasGeoserver.value]);

const etapasCompletas = computed(() => etapasPreenchimento.value.filter((etapa) => etapa.completo).length);

const percentualPreenchimento = computed(() => {
  const total = etapasPreenchimento.value.length;
  if (!total) return 0;
  return Math.round((etapasCompletas.value / total) * 100);
});

const setActiveTab = (tab) => {
  activeTab.value = tab;
};

const onUpdateAnexos = (novosAnexos) => {
  anexosLocais.value = Array.isArray(novosAnexos) ? [...novosAnexos] : [];
};

const onUpdateResultadosAnexos = (novosResultados) => {
  resultadosAnexosLocais.value = Array.isArray(novosResultados) ? [...novosResultados] : [];
};

const formatAnexoLabel = (tipo) => {
  if (!tipo) return 'Nao informado';

  const labels = {
    shape_areas: 'Shape de areas',
    shape_pontos: 'Shape de pontos',
    shape_linhas: 'Shape de linhas',
    geojson_areas: 'GeoJSON de areas',
    geojson_pontos: 'GeoJSON de pontos',
    geojson_linhas: 'GeoJSON de linhas',
    csv_areas: 'CSV de areas',
    csv_pontos: 'CSV de pontos',
    csv_linhas: 'CSV de linhas',
    kml_areas: 'KML de areas',
    kml_pontos: 'KML de pontos',
    kml_linhas: 'KML de linhas',
    gpkg_areas: 'GPKG de areas',
    gpkg_pontos: 'GPKG de pontos',
    gpkg_linhas: 'GPKG de linhas',
    geotiff_mde: 'GeoTIFF MDE',
    geotiff_mds: 'GeoTIFF MDS',
    feicoes: 'Planilha de Feicoes Carsticas',
    feicoes_carsticas_identificadas: 'Feicoes Carsticas Identificadas',
    cavidades_nao_encontradas: 'Cavidades Nao Encontradas',
    cavidades_cecav_canie: 'Cavidades CECAV/CANIE',
    caminhamento: 'Mapa de Caminhamento',
    raio_de_250m_de_cavidades: 'Raio de 250m de Cavidades',
    curvas_de_nivel: 'Curvas de Nivel',
    extensao_rodovia_prospectada: 'Extensao de Rodovia Prospectada',
  };

  return labels[tipo] || tipo;
};

const aprovarCampanha = () => {
  formAprovacao.status = 'Aprovada';
  formAprovacao.observacoes = '';
  salvarAprovacao();
};

const rejeitarCampanha = () => {
  if (!formAprovacao.observacoes.trim()) {
    formAprovacao.setError('observacoes', 'Observações são obrigatórias para rejeição.');
    return;
  }
  formAprovacao.status = 'Reprovada';
  salvarAprovacao();
};

const salvarAprovacao = () => {
  formAprovacao.post(route('sgc.contratada.produtos.espeleo.approve', [props.contrato, 'espeleologia', props.campanha.id]), {
    onSuccess: () => {
      formAprovacao.reset();
      router.get(route('sgc.contratada.produtos.index', [props.contrato, 'espeleologia']));
    },
  });
};

const irParaResumo = () => {
  router.get(
    route('sgc.contratada.produtos.espeleo.show', [props.contrato, 'espeleologia', props.campanha.id]),
    { modo: 'resumo' }
  );
};

const irParaEdicao = () => {
  router.get(route('sgc.contratada.produtos.espeleo.show', [props.contrato, 'espeleologia', props.campanha.id]));
};
</script>

<template>
  <AuthenticatedLayout>
    <template #header>
      <Breadcrumb
        :links="[
          { route: route('sgc.gestao.listagem', contratos.tipo_contrato), label: 'Gestão de Contratos' },
          { route: route('sgc.contratada.produtos.index', [contrato, 'espeleologia']), label: contratos.contratada },
          { route: '#', label: `${isResumoView ? 'Resumo' : 'Editar'} Campanha ${campanha.id_campanha || campanha.id}` },
        ]"
      />
    </template>

    <NavbarContrato :tipo="{ id: contrato }">
      <template #body>
        <div class="card">
          <div class="card-body">
            <div v-if="campanha.analises && campanha.analises.length > 0" class="alert alert-info mb-3 d-flex justify-content-between align-items-center" style="cursor: pointer;" @click="showAnalysisModal = true">
              <span>
                <i class="bi bi-info-circle me-2"></i>
                {{ campanha.analises.length }} análise{{ campanha.analises.length !== 1 ? 's' : '' }} registrada{{ campanha.analises.length !== 1 ? 's' : '' }}
              </span>
              <span class="badge bg-info text-white">Clique para visualizar</span>
            </div>

            <div class="text-center mb-4">
              <h2 class="mb-2">
                {{ isResumoView ? 'RESUMO DA CAMPANHA: ESPELEOLOGIA' : 'EDITAR CAMPANHA: ESPELEOLOGIA' }}
              </h2>
              <p class="text-muted mb-3 fs-5">{{ campanha.subproduto || 'Subproduto não informado' }}</p>
              <div class="d-inline-block">
                <span class="badge fs-4 px-3 py-2 fw-bold text-white" :class="statusClass">{{ statusDisplay || 'N/A' }}</span>
              </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mb-4">
              <button v-if="isResumoView" class="btn btn-outline-warning" type="button" @click="irParaEdicao">
                Ir para edição completa
              </button>
              <button v-else class="btn btn-outline-info" type="button" @click="irParaResumo">
                Abrir resumo em tela única
              </button>
            </div>

            <template v-if="isResumoView">
              <div class="card mb-4">
                <div class="card-body">
                  <h5 class="mb-3">Dados principais</h5>
                  <div class="row g-3">
                    <div class="col-md-6">
                      <label class="form-label mb-1">Empreendimento</label>
                      <div class="form-control bg-light">{{ campanha.cod_emp || 'Nao informado' }}</div>
                    </div>
                    <div class="col-md-3">
                      <label class="form-label mb-1">Subproduto</label>
                      <div class="form-control bg-light">{{ campanha.subproduto || 'Nao informado' }}</div>
                    </div>
                    <div class="col-md-3">
                      <label class="form-label mb-1">Subtrecho</label>
                      <div class="form-control bg-light">{{ campanha.subtrecho || 'Nao informado' }}</div>
                    </div>
                    <div class="col-md-3">
                      <label class="form-label mb-1">Segmento</label>
                      <div class="form-control bg-light">{{ campanha.segmento || 'Nao informado' }}</div>
                    </div>
                    <div class="col-md-3">
                      <label class="form-label mb-1">Resultados anexados</label>
                      <div class="form-control bg-light">{{ campanha.resultados_anexos?.length || 0 }}</div>
                    </div>
                    <div class="col-md-3">
                      <label class="form-label mb-1">Anexos</label>
                      <div class="form-control bg-light">{{ campanha.anexos?.length || 0 }}</div>
                    </div>
                    <div class="col-md-3">
                      <label class="form-label mb-1">Profissionais vinculados</label>
                      <div class="form-control bg-light">{{ campanha.profissionais?.length || 0 }}</div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="card mb-4">
                <div class="card-body">
                  <h5 class="mb-3">Mapa e coordenadas</h5>
                  <MapLayer :emp_coordenadas="coordenadas" :campanha-id="campanha.id" />
                </div>
              </div>

              <div class="card mb-4">
                <div class="card-body">
                  <h5 class="mb-3">Metodologia</h5>
                  <p class="mb-0" style="white-space: pre-wrap;">{{ campanha.metodologia || 'Nenhuma metodologia registrada.' }}</p>
                </div>
              </div>

              <div class="card mb-4" v-if="campanha.resultados_anexos?.length">
                <div class="card-body">
                  <h5 class="mb-3">Resultados</h5>
                  <div class="table-responsive">
                    <table class="table table-bordered mb-0">
                      <thead>
                        <tr>
                          <th>ID</th>
                          <th>Tipo</th>
                          <th>Arquivo</th>
                          <th>Observação</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr v-for="resultado in campanha.resultados_anexos" :key="resultado.id">
                          <td>{{ resultado.id }}</td>
                          <td>{{ formatAnexoLabel(resultado.tipo) }}</td>
                          <td>{{ resultado.nome_arquivo || 'Nao informado' }}</td>
                          <td>{{ resultado.comentario || 'Sem observacao' }}</td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>

              <div class="card mb-4" v-if="campanha.profissionais?.length">
                <div class="card-body">
                  <h5 class="mb-3">Equipe vinculada</h5>
                  <div class="table-responsive">
                    <table class="table table-bordered mb-0">
                      <thead>
                        <tr>
                          <th>Profissional</th>
                          <th>Formação</th>
                          <th>Função</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr v-for="profissional in campanha.profissionais" :key="profissional.id">
                          <td>{{ profissional.profissional || 'Nao informado' }}</td>
                          <td>{{ profissional.formacao || 'Nao informado' }}</td>
                          <td>{{ profissional.funcao || 'Nao informado' }}</td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>

              <div class="card mb-4" v-if="campanha.justificativas?.length">
                <div class="card-body">
                  <h5 class="mb-3">Justificativas</h5>
                  <div v-for="item in campanha.justificativas" :key="item.id" class="border rounded p-3 mb-2">
                    <p class="mb-1"><strong>Tipo:</strong> {{ item.tipo || 'Nao informado' }}</p>
                    <p class="mb-1"><strong>Título:</strong> {{ item.titulo || 'Nao informado' }}</p>
                    <p class="mb-1"><strong>Código SEI:</strong> {{ item.codigo_sei || 'Nao informado' }}</p>
                    <p class="mb-0"><strong>Texto:</strong> {{ item.justificativa || 'Nao informado' }}</p>
                  </div>
                </div>
              </div>

              <div class="card" v-if="anexosLocais.length">
                <div class="card-body">
                  <h5 class="mb-3">Anexos</h5>
                  <VisualizarAnexos :anexos="anexosLocais" />
                </div>
              </div>
            </template>

            <template v-else>

            <ul class="nav nav-tabs mb-4">
              <li class="nav-item">
                <a class="nav-link" :class="{ active: activeTab === 'apresentacao' }" @click.prevent="setActiveTab('apresentacao')">
                  Apresentação
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" :class="{ active: activeTab === 'metodologia' }" @click.prevent="setActiveTab('metodologia')">
                  Metodologias
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" :class="{ active: activeTab === 'resultados' }" @click.prevent="setActiveTab('resultados')">
                  Resultados
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" :class="{ active: activeTab === 'resultadosgeo' }" @click.prevent="setActiveTab('resultadosgeo')">
                  Resultados Geoserver
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" :class="{ active: activeTab === 'anexos' }" @click.prevent="setActiveTab('anexos')">
                  Anexos
                </a>
              </li>
            </ul>

            <div class="tab-content">
              <div v-if="activeTab === 'apresentacao'" class="tab-pane fade" :class="{ 'show active': activeTab === 'apresentacao' }">
                <h4 class="text-center mb-3" style="font-weight: bold;">APRESENTAÇÃO</h4>
                <div class="card mb-4">
                  <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                      <h5 class="mb-0">Progresso da campanha</h5>
                      <span class="badge bg-info text-white">{{ percentualPreenchimento }}%</span>
                    </div>
                    <div class="progress mb-2" style="height: 10px;">
                      <div
                        class="progress-bar progress-campaign-bar"
                        role="progressbar"
                        :style="{ width: `${percentualPreenchimento}%` }"
                        :aria-valuenow="percentualPreenchimento"
                        aria-valuemin="0"
                        aria-valuemax="100"
                      ></div>
                    </div>
                    <small class="text-muted d-block mb-2">
                      {{ etapasCompletas }} de {{ etapasPreenchimento.length }} etapas preenchidas (abas + módulos geoserver)
                    </small>
                    <small class="text-muted d-block mb-2">
                      Sub-abas Geoserver com mapa cadastrado: {{ etapasGeoserverCompletas }} de {{ etapasGeoserver.length }}
                    </small>
                    <div class="timeline-legend mb-2">
                      <span class="timeline-legend__item">
                        <span class="timeline-legend__dot timeline-legend__dot--ok"></span>
                        Concluído (com mapa cadastrado)
                      </span>
                      <span class="timeline-legend__item">
                        <span class="timeline-legend__dot timeline-legend__dot--pending"></span>
                        Pendente (sem mapa cadastrado)
                      </span>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                      <span
                        v-for="etapa in abasPrimarias"
                        :key="etapa.nome"
                        class="badge stage-badge"
                        :class="etapa.completo ? 'stage-badge--ok' : 'stage-badge--pending'"
                      >
                        {{ etapa.nome }}
                      </span>
                    </div>
                    <div class="stage-row-label mt-2">Mapas (sub-abas Geoserver)</div>
                    <div class="d-flex flex-wrap gap-2 mt-1">
                      <span
                        v-for="etapa in etapasGeoserver"
                        :key="`geo-${etapa.nome}`"
                        class="badge stage-badge"
                        :class="etapa.completo ? 'stage-badge--ok' : 'stage-badge--pending'"
                      >
                        {{ etapa.nome }}
                      </span>
                    </div>
                  </div>
                </div>

                <div class="row mb-4">
                  <div class="col-md-8">
                    <MapLayer :emp_coordenadas="coordenadas" :campanha-id="campanha.id" />
                  </div>
                  <div class="col-md-4">
                    <div class="mb-3">
                      <label class="form-label">Empreendimento</label>
                      <input type="text" class="form-control" :value="campanha.cod_emp || 'Nao informado'" disabled />
                    </div>
                    <div class="mb-3">
                      <label class="form-label">Subproduto</label>
                      <input type="text" class="form-control" :value="campanha.subproduto || 'Nao informado'" disabled />
                    </div>
                    <div class="mb-3">
                      <label class="form-label">Subtrecho</label>
                      <input type="text" class="form-control" :value="campanha.subtrecho || 'Nao informado'" disabled />
                    </div>
                    <div class="mb-3">
                      <label class="form-label">Segmento</label>
                      <input type="text" class="form-control" :value="campanha.segmento || 'Nao informado'" disabled />
                    </div>
                  </div>
                </div>

                <div class="card mb-3">
                  <div class="card-body">
                    <h5 class="mb-3">Equipe Vinculada</h5>
                    <table class="table table-bordered" v-if="campanha.profissionais && campanha.profissionais.length">
                      <thead>
                        <tr>
                          <th>Profissional</th>
                          <th>Formação</th>
                          <th>Função</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr v-for="profissional in campanha.profissionais" :key="profissional.id">
                          <td>{{ profissional.profissional || 'Nao informado' }}</td>
                          <td>{{ profissional.formacao || 'Nao informado' }}</td>
                          <td>{{ profissional.funcao || 'Nao informado' }}</td>
                        </tr>
                      </tbody>
                    </table>
                    <div v-else class="alert alert-info mb-0">Nenhum profissional vinculado.</div>
                  </div>
                </div>

                <div class="card">
                  <div class="card-body">
                    <h5 class="mb-3">Justificativas</h5>
                    <div v-if="campanha.justificativas && campanha.justificativas.length">
                      <div v-for="item in campanha.justificativas" :key="item.id" class="border rounded p-3 mb-2">
                        <p class="mb-1"><strong>Tipo:</strong> {{ item.tipo || 'Nao informado' }}</p>
                        <p class="mb-1"><strong>Titulo:</strong> {{ item.titulo || 'Nao informado' }}</p>
                        <p class="mb-1"><strong>Codigo SEI:</strong> {{ item.codigo_sei || 'Nao informado' }}</p>
                        <p class="mb-0"><strong>Texto:</strong> {{ item.justificativa || 'Nao informado' }}</p>
                      </div>
                    </div>
                    <div v-else class="alert alert-info mb-0">Nenhuma justificativa registrada.</div>
                  </div>
                </div>
              </div>

              <div v-if="activeTab === 'metodologia'" class="tab-pane fade" :class="{ 'show active': activeTab === 'metodologia' }">
                <h4 class="mb-3 text-center">METODOLOGIA</h4>
                <div class="card">
                  <div class="card-body">
                    <p class="mb-0" style="white-space: pre-wrap;">{{ campanha.metodologia || 'Nenhuma metodologia registrada.' }}</p>
                  </div>
                </div>
              </div>

              <div v-if="activeTab === 'resultados'" class="tab-pane fade" :class="{ 'show active': activeTab === 'resultados' }">
                <h4 class="mb-3 text-center">RESULTADOS</h4>
                <ResultadosProspeccao
                  v-if="isProspeccao"
                  :empreendimentos="[]"
                  :errors="{}"
                  :campanha-id="campanha.id"
                  :contrato="Number(contrato)"
                  :resultados-anexos="resultadosAnexosLocais"
                  :subprodutos-espeleologia="[]"
                  :estudos-posteriores="[]"
                  @update-resultados-anexos="onUpdateResultadosAnexos"
                />
                <Resultados
                  v-else
                  :empreendimentos="[]"
                  :errors="{}"
                  :campanha-id="campanha.id"
                  :contrato="Number(contrato)"
                  :resultados-anexos="resultadosAnexosLocais"
                  :subprodutos-espeleologia="[]"
                  :estudos-posteriores="[]"
                  @update-resultados-anexos="onUpdateResultadosAnexos"
                />
              </div>

              <div v-if="activeTab === 'resultadosgeo'" class="tab-pane fade" :class="{ 'show active': activeTab === 'resultadosgeo' }">
                <ResultadosGeoserver
                  :empreendimentos="[]"
                  :errors="{}"
                  :campanha-id="campanha.id"
                  :contrato="Number(contrato)"
                  :subproduto="campanha.subproduto || ''"
                  :resultados-anexos="campanha.resultados_anexos || []"
                  :subprodutos-espeleologia="[]"
                  :estudos-posteriores="[]"
                  @update-resultados-anexos="() => {}"
                />
              </div>

              <div v-if="activeTab === 'anexos'" class="tab-pane fade" :class="{ 'show active': activeTab === 'anexos' }">
                <VisualizarAnexos :anexos="anexosLocais" />

                <div class="card mt-4">
                  <div class="card-body">
                    <h5 class="mb-3">Adicionar mais anexos</h5>
                    <Anexos
                      :campanha-id="campanha.id"
                      :contrato="Number(contrato)"
                      :anexos="anexosLocais"
                      :errors="{}"
                      @update-anexos="onUpdateAnexos"
                    />
                  </div>
                </div>

                <div v-if="canApprove && campanha.status === 'Em análise'" class="mt-4">
                  <h4>APROVAÇÃO</h4>
                  <form @submit.prevent="rejeitarCampanha">
                    <div class="mb-3">
                      <label for="observacoes" class="form-label">Observações (obrigatório para rejeição)</label>
                      <textarea id="observacoes" v-model="formAprovacao.observacoes" class="form-control" rows="3"></textarea>
                      <InputError :message="formAprovacao.errors.observacoes" class="mt-2" />
                    </div>
                    <div class="d-flex gap-2">
                      <button type="button" class="btn btn-success" @click="aprovarCampanha">Aprovar</button>
                      <button type="submit" class="btn btn-danger">Rejeitar</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
            </template>
          </div>
        </div>

        <div v-if="showAnalysisModal && campanha.analises && campanha.analises.length > 0" class="modal-backdrop-custom" @click.self="showAnalysisModal = false">
          <div class="preview-modal" style="max-width: 600px;">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h5 class="mb-0">Histórico de Análises</h5>
              <button type="button" class="btn-close" @click="showAnalysisModal = false"></button>
            </div>
            <div class="modal-analysis-content">
              <div v-for="analise in campanha.analises" :key="analise.id" class="card mb-2">
                <div class="card-body p-3">
                  <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                      <strong>Versão {{ analise.versao }}</strong>
                    </div>
                    <small class="text-muted">{{ analise.created_at }}</small>
                  </div>
                  <p class="mb-2 small">
                    <strong>Fiscal:</strong> {{ analise.fiscal?.name || 'N/A' }}
                  </p>
                  <div v-if="analise.observacoes" class="alert alert-info small mb-0">
                    <strong>Observações:</strong>
                    <p class="mb-0 mt-1">{{ analise.observacoes }}</p>
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
.progress-campaign-bar {
  background: linear-gradient(90deg, #0b8f4b 0%, #23b26c 100%);
}

.stage-badge {
  font-size: 0.78rem;
  font-weight: 600;
  padding: 0.45rem 0.6rem;
  border: 1px solid transparent;
}

.stage-badge--ok {
  background-color: #166534;
  color: #ffffff;
  border-color: #14532d;
}

.stage-badge--pending {
  background-color: #fef3c7;
  color: #7c2d12;
  border-color: #f59e0b;
}

.timeline-legend {
  display: flex;
  flex-wrap: wrap;
  gap: 0.75rem;
}

.timeline-legend__item {
  display: inline-flex;
  align-items: center;
  gap: 0.35rem;
  font-size: 0.78rem;
  color: #374151;
}

.timeline-legend__dot {
  width: 10px;
  height: 10px;
  border-radius: 999px;
  border: 1px solid transparent;
}

.timeline-legend__dot--ok {
  background-color: #166534;
  border-color: #14532d;
}

.timeline-legend__dot--pending {
  background-color: #fef3c7;
  border-color: #f59e0b;
}

.stage-row-label {
  font-size: 0.78rem;
  font-weight: 600;
  color: #4b5563;
}

.modal-backdrop-custom {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.6);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1050;
  padding: 1rem;
}

.preview-modal {
  background: #fff;
  border-radius: 8px;
  box-shadow: 0 12px 30px rgba(0, 0, 0, 0.2);
  width: 100%;
  max-height: 85vh;
  overflow: auto;
  padding: 1rem;
}

.modal-analysis-content {
  max-height: 60vh;
  overflow-y: auto;
}
</style>
