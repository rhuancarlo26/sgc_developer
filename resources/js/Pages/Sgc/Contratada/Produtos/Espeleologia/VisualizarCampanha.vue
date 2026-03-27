<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import NavbarContrato from '@/Pages/Sgc/Contratada/NavbarContrato.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import InputError from '@/Components/InputError.vue';
import { computed, ref, watch } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import * as XLSX from 'xlsx';
import MapLayer from './MapViewer.vue';
import ResultadosGeoserver from './ResultadosGeoserver.vue';
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

const activeTab = ref('apresentacao');
const anexosLocais = ref(Array.isArray(props.campanha?.anexos) ? [...props.campanha.anexos] : []);

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

const etapasPreenchimento = computed(() => {
  const campanha = props.campanha || {};
  const temDadosApresentacao = Boolean(campanha.cod_emp || campanha.subproduto || campanha.subtrecho || campanha.segmento);

  return [
    { nome: 'Dados de apresentação', completo: temDadosApresentacao },
    { nome: 'Mapa/coordenadas', completo: hasCoordenadas.value },
    { nome: 'Metodologia', completo: Boolean((campanha.metodologia || '').trim()) },
    { nome: 'Resultados/mapas', completo: Array.isArray(campanha.resultados_anexos) && campanha.resultados_anexos.length > 0 },
    { nome: 'Anexos de imagens', completo: anexosLocais.value.length > 0 },
    { nome: 'Equipe vinculada', completo: Array.isArray(campanha.profissionais) && campanha.profissionais.length > 0 },
    { nome: 'Justificativas', completo: Array.isArray(campanha.justificativas) && campanha.justificativas.length > 0 },
  ];
});

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

const planilhaPreviewHeaders = ref([])
const planilhaPreviewRows = ref([])
const showPlanilhaPreview = ref(false)
const loadingPlanilhaPreview = ref(false)

const planilhaFeicoes = computed(() =>
  Array.isArray(props.campanha?.resultados_anexos)
    ? props.campanha.resultados_anexos.find(a => a.tipo === 'feicoes') || null
    : null
)

const togglePlanilhaPreview = async () => {
  if (showPlanilhaPreview.value) {
    showPlanilhaPreview.value = false
    return
  }
  if (planilhaPreviewRows.value.length > 0) {
    showPlanilhaPreview.value = true
    return
  }
  if (!planilhaFeicoes.value) return
  const url = planilhaFeicoes.value.caminho
  if (!url) return
  loadingPlanilhaPreview.value = true
  try {
    const res = await fetch(url)
    const buffer = await res.arrayBuffer()
    const wb = XLSX.read(new Uint8Array(buffer), { type: 'array' })
    const ws = wb.Sheets[wb.SheetNames[0]]
    const rows = XLSX.utils.sheet_to_json(ws, { defval: '' })
    planilhaPreviewHeaders.value = rows.length > 0 ? Object.keys(rows[0]) : []
    planilhaPreviewRows.value = rows.slice(0, 10)
    showPlanilhaPreview.value = true
  } catch {
    showPlanilhaPreview.value = true
  } finally {
    loadingPlanilhaPreview.value = false
  }
}

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
  formAprovacao.status = 'Rejeitada';
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
</script>

<template>
  <AuthenticatedLayout>
    <template #header>
      <Breadcrumb
        :links="[
          { route: route('sgc.gestao.listagem', contratos.tipo_contrato), label: 'Gestão de Contratos' },
          { route: route('sgc.contratada.produtos.index', [contrato, 'espeleologia']), label: contratos.contratada },
          { route: '#', label: `Visualizar Campanha ${campanha.id_campanha || campanha.id}` },
        ]"
      />
    </template>

    <NavbarContrato :tipo="{ id: contrato }">
      <template #body>
        <div class="card">
          <div class="card-body">
            <h2 class="text-center mb-4">VISUALIZAR CAMPANHA: ESPELEOLOGIA</h2>
            <h4 class="mb-3">Status: {{ campanha.status }}</h4>

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
                      <span class="badge bg-primary">{{ percentualPreenchimento }}%</span>
                    </div>
                    <div class="progress mb-2" style="height: 10px;">
                      <div
                        class="progress-bar"
                        role="progressbar"
                        :style="{ width: `${percentualPreenchimento}%` }"
                        :aria-valuenow="percentualPreenchimento"
                        aria-valuemin="0"
                        aria-valuemax="100"
                      ></div>
                    </div>
                    <small class="text-muted d-block mb-2">{{ etapasCompletas }} de {{ etapasPreenchimento.length }} etapas preenchidas</small>
                    <div class="d-flex flex-wrap gap-2">
                      <span
                        v-for="etapa in etapasPreenchimento"
                        :key="etapa.nome"
                        class="badge"
                        :class="etapa.completo ? 'bg-success' : 'bg-secondary'"
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

                <!-- Card especial: Planilha de Feições Cársticas -->
                <div v-if="planilhaFeicoes" class="card mb-4 border-success">
                  <div class="card-header bg-success bg-opacity-10 d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <span class="fw-semibold">
                      <i class="fas fa-file-excel text-success me-1"></i>
                      Planilha de Feições Cársticas
                    </span>
                    <div class="d-flex gap-2">
                      <button
                        class="btn btn-sm btn-outline-secondary"
                        type="button"
                        @click="togglePlanilhaPreview"
                      >
                        <span v-if="loadingPlanilhaPreview" class="spinner-border spinner-border-sm me-1" role="status"></span>
                        <i v-else :class="showPlanilhaPreview ? 'fas fa-eye-slash' : 'fas fa-eye'" class="me-1"></i>
                        {{ showPlanilhaPreview ? 'Ocultar' : 'Pré-visualizar' }}
                      </button>
                      <a
                        :href="planilhaFeicoes.caminho"
                        class="btn btn-sm btn-success"
                        download
                      >
                        <i class="fas fa-download me-1"></i> Baixar
                      </a>
                    </div>
                  </div>
                  <div class="card-body py-2">
                    <small class="text-muted">
                      {{ planilhaFeicoes.nome_arquivo }}
                      <span v-if="planilhaFeicoes.created_at"> — {{ planilhaFeicoes.created_at }}</span>
                    </small>
                    <p v-if="planilhaFeicoes.comentario" class="mb-0 mt-1">{{ planilhaFeicoes.comentario }}</p>
                  </div>
                  <!-- Preview das 10 primeiras linhas -->
                  <div v-if="showPlanilhaPreview" class="card-footer p-0">
                    <div v-if="planilhaPreviewRows.length > 0" class="table-responsive">
                      <table class="table table-sm table-bordered mb-0" style="font-size:0.8rem;">
                        <thead class="table-light">
                          <tr>
                            <th v-for="col in planilhaPreviewHeaders" :key="col">{{ col }}</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr v-for="(row, i) in planilhaPreviewRows" :key="i">
                            <td v-for="col in planilhaPreviewHeaders" :key="col">{{ row[col] ?? '' }}</td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                    <p v-else class="p-3 text-muted mb-0 small">Não foi possível carregar a pré-visualização.</p>
                  </div>
                </div>

                <!-- Tabela dos demais resultados (excluindo feicoes que já tem card próprio) -->
                <table
                  class="table table-bordered"
                  v-if="campanha.resultados_anexos && campanha.resultados_anexos.filter(r => r.tipo !== 'feicoes').length"
                >
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Tipo</th>
                      <th>Arquivo</th>
                      <th>Observação</th>
                      <th>Data</th>
                      <th>Ação</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="resultado in campanha.resultados_anexos.filter(r => r.tipo !== 'feicoes')" :key="resultado.id">
                      <td>{{ resultado.id }}</td>
                      <td>{{ formatAnexoLabel(resultado.tipo) }}</td>
                      <td>{{ resultado.nome_arquivo || 'Nao informado' }}</td>
                      <td>{{ resultado.comentario || 'Sem observacao' }}</td>
                      <td>{{ resultado.created_at || 'Nao informado' }}</td>
                      <td>
                        <a v-if="resultado.caminho" :href="resultado.caminho" target="_blank" class="btn btn-link p-0">Abrir</a>
                        <span v-else>Nenhum arquivo</span>
                      </td>
                    </tr>
                  </tbody>
                </table>
                <div v-else-if="!planilhaFeicoes" class="alert alert-info">Nenhum resultado anexado.</div>
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
          </div>
        </div>
      </template>
    </NavbarContrato>
  </AuthenticatedLayout>
</template>
