<template>
  <div style="position: relative;">
    <!-- Overlay de carregamento geral (loadGeoLayers / vincularMapa) -->
    <transition name="geo-fade">
      <div v-if="isLoading" class="geo-loading-overlay">
        <div class="geo-loading-box">
          <div class="spinner-border text-primary" role="status" style="width:2.2rem;height:2.2rem;"></div>
          <p class="mt-2 mb-0 fw-semibold">{{ loadingMessage || 'Carregando...' }}</p>
        </div>
      </div>
    </transition>

    <h3>Resultados - Visualização de Shapefiles</h3>
    <p class="text-muted mb-4">
      Carregue shapefiles (ZIP) para cada tipo de mapa, vincule-os e adicione observações.
    </p>

    <ul class="nav nav-tabs mb-4 flex-wrap">
      <li class="nav-item" v-for="tipo in tipos" :key="tipo">
        <a
          class="nav-link"
          :class="{ active: activeSubTab === tipo }"
          @click.prevent="activeSubTab = tipo"
        >
          {{ nomesTipos[tipo] }}
          <i
            v-if="layersForTipo(tipo).length > 0"
            class="fas fa-check-circle text-success ms-1"
          ></i>
          <span v-if="layersForTipo(tipo).length > 1" class="badge bg-success ms-1">{{ layersForTipo(tipo).length }}</span>
        </a>
      </li>
    </ul>

    <div class="tab-content">
      <div
        v-for="tipo in tipos"
        :key="tipo"
        class="tab-pane fade"
        :class="{ 'show active': activeSubTab === tipo }"
        v-show="activeSubTab === tipo"
      >
        <div class="mb-5">
          <h4>{{ nomesTipos[tipo] }}</h4>

          <!-- === ESTUDOS POSTERIORES (interface especial) === -->
          <div v-if="tipo === 'estudos_posteriores'">
            <label class="form-label fw-bold">Será necessário executar demais estudos espeleológicos?</label>
            <div class="mb-3">
              <label class="me-3">
                <input type="radio" v-model="necessarioEstudos" :value="true" /> Sim
              </label>
              <label>
                <input type="radio" v-model="necessarioEstudos" :value="false" /> Não
              </label>
            </div>

            <div v-if="necessarioEstudos">
              <div
                v-for="(item, index) in estudos"
                :key="item.__key"
                class="border rounded p-3 mb-3"
              >
                <label class="form-label">Subproduto</label>
                <select class="form-control mb-2" v-model="item.subproduto_id">
                  <option value="">-- selecione --</option>
                  <option
                    v-for="s in subprodutosEspeleologia"
                    :value="s.id"
                    :key="s.id"
                  >
                    {{ s.descricao_revisada }}
                  </option>
                </select>

                <label class="form-label">Quantidade estimada</label>
                <input class="form-control mb-2" v-model="item.quantidade" placeholder="Digite a quantidade" />

                <label class="form-label">KM's a serem prospectados</label>
                <div
                  v-for="(coord, cIdx) in item.coordenadas"
                  :key="coord.__key"
                  class="d-flex gap-2 mb-2"
                >
                  <input class="form-control" v-model="coord.lat" placeholder="Lat" />
                  <input class="form-control" v-model="coord.lng" placeholder="Lng" />
                  <button class="btn btn-sm btn-outline-danger" @click="removePar(index, cIdx)">X</button>
                </div>

                <button class="btn btn-sm btn-outline-secondary mb-2" @click="addPar(index)">
                  + Adicionar KM
                </button>

                <button class="btn btn-sm btn-outline-danger" @click="removeEstudo(index)">
                  Remover Subproduto
                </button>
              </div>

              <button class="btn btn-sm btn-primary" @click="addEstudo">
                + Adicionar Subproduto
              </button>

              <button class="btn btn-success mt-3" @click="salvarEstudos">
                Salvar Estudos Posteriores
              </button>
            </div>

            <!-- Bloqueia a parte padrão nesta aba -->
            <template v-if="true"></template>
            <hr />
          </div>

          <!-- === Demais abas (padrão shapefile) === -->
          <template v-else>
            <!-- Camadas já vinculadas para este tipo -->
            <div v-if="layersForTipo(tipo).length > 0" class="mb-4">
              <h6 class="text-muted mb-2">
                <i class="fas fa-layer-group me-1"></i>
                Camadas vinculadas ({{ layersForTipo(tipo).length }}):
              </h6>
              <div
                v-for="(layer, index) in layersForTipo(tipo)"
                :key="layer.id"
              >
                <div class="alert alert-info mb-2 d-flex align-items-center justify-content-between">
                  <span>
                    <i class="fas fa-map me-2"></i>
                    <strong>{{ tipoLabel(tipo, index, layersForTipo(tipo).length) }}</strong>
                    <small class="text-muted ms-2">— {{ layer.layer_name }}</small>
                  </span>
                  <div>
                    <button
                      @click="toggleRenderLayer(layer)"
                      class="btn btn-sm btn-outline-secondary me-1"
                      type="button"
                      :disabled="loadingLayerIds.includes(layer.id)"
                    >
                      <span v-if="loadingLayerIds.includes(layer.id)" class="spinner-border spinner-border-sm me-1" role="status"></span>
                      <i v-else :class="renderedLayers[layer.id] ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                      {{ loadingLayerIds.includes(layer.id) ? 'Carregando...' : (renderedLayers[layer.id] ? 'Esconder' : 'Ver Mapa') }}
                    </button>
                    <button
                      @click="desvincularLayer(layer.id, tipo)"
                      class="btn btn-sm btn-outline-danger"
                      type="button"
                      :disabled="loadingLayerIds.includes(layer.id)"
                    >
                      <span v-if="loadingLayerIds.includes(layer.id)" class="spinner-border spinner-border-sm" role="status"></span>
                      <i v-else class="fas fa-unlink"></i>
                    </button>
                  </div>
                </div>
                <div
                  v-show="renderedLayers[layer.id]"
                  :id="'wms-map-' + layer.id"
                  style="height: 400px; border: 1px solid #ddd; border-radius: 4px; margin-bottom: 12px;"
                ></div>
              </div>
            </div>

            <!-- Upload zone (sempre visível para adicionar mais) -->
            <div
              @drop="onDrop($event, tipo)"
              @dragover.prevent
              @dragenter.prevent
              class="border p-4 rounded bg-light mb-3 text-center"
              :class="{ 'border-danger': errors[tipo] }"
              style="min-height: 100px;"
            >
              <p>
                Arraste .zip do shapefile aqui ou
                <button type="button" @click.prevent="triggerFileInput(tipo)" class="btn btn-link p-0">clique para selecionar</button>
                para {{ layersForTipo(tipo).length > 0 ? 'adicionar mais um mapa' : 'vincular um mapa' }}.
              </p>
              <input
                :id="'fileInput-' + tipo"
                type="file"
                @change="onFileChange($event, tipo)"
                multiple
                accept=".zip,.shp,.shx,.dbf"
                style="display: none;"
              />
              <div v-if="uploadedFiles[tipo].length > 0" class="mt-2 text-success">
                <small>Carregado: {{ uploadedFiles[tipo].map(f => f.name).join(', ') }}</small>
              </div>
              <small v-if="errors[tipo]" class="text-danger d-block">{{ errors[tipo] }}</small>
            </div>

            <!-- Preview do shapefile recém-carregado -->
            <div
              v-if="rendered[tipo]"
              :id="'map-' + tipo"
              style="height: 400px; border: 1px solid #ddd; border-radius: 4px;"
              class="mb-3"
            ></div>

            <button
              v-if="features[tipo].length > 0"
              @click="vincularMapa(tipo)"
              class="btn btn-primary mb-3"
              type="button"
              :disabled="loadingTipo === tipo"
            >
              <span v-if="loadingTipo === tipo" class="spinner-border spinner-border-sm me-1" role="status"></span>
              <i v-else class="fas fa-link me-1"></i>
              {{ loadingTipo === tipo ? 'Publicando no GeoServer...' : ('Vincular Mapa' + (layersForTipo(tipo).length > 0 ? ' Adicional' : '')) }}
            </button>
          </template>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, ref, nextTick, watch, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'
import { useLoading } from '@/Composables/useLoading'
import axios from 'axios'
import L from 'leaflet'
import * as shapefile from 'shapefile'
import JSZip from 'jszip'

const props = defineProps({
  empreendimentos: Array,
  errors: Object,
  campanhaId: Number,
  contrato: Number,
  subproduto: { type: String, default: '' },
  resultadosAnexos: Array,
  subprodutosEspeleologia: { type: Array, default: () => [] },
  estudosPosteriores: { type: Array, default: () => [] },
})

const emit = defineEmits(['update-resultados-anexos'])

const tiposPadrao = [
  'geologico', 'geomorfologico', 'hipsometrico',
  'declividades', 'hidrografico', 'cavidades',
  'limites_areas', 'potencial_inicial', 'potencial_reclassificado',
  'projeto_engenharia', 'estudos_posteriores'
]

const nomesTiposPadrao = {
  geologico: 'Mapa Geológico',
  geomorfologico: 'Mapa Geomorfológico',
  hipsometrico: 'Mapa Hipsométrico',
  declividades: 'Mapa de Declividades',
  hidrografico: 'Mapa Hidrográfico',
  cavidades: 'Cavidades CECAV/SBE',
  limites_areas: 'Limites de Áreas',
  potencial_inicial: 'Mapa de Potencial Espeleológico - Inicial',
  potencial_reclassificado: 'Mapa de Potencial Espeleológico - Reclassificado',
  projeto_engenharia: 'Projeto de Engenharia',
  estudos_posteriores: 'Estudos Posteriores'
}

const tiposProspeccao = [
//   'feicoes',
  'feicoes_carsticas_identificadas',
  'cavidades_nao_encontradas',
  'cavidades_cecav_canie',
  'caminhamento',
  'raio_de_250m_de_cavidades',
  'curvas_de_nivel',
  'extensao_rodovia_prospectada'
]

const nomesTiposProspeccao = {
//   feicoes: 'Planilha de Feições Cársticas',
  feicoes_carsticas_identificadas: 'Feições Cársticas Identificadas',
  cavidades_nao_encontradas: 'Cavidades Não Encontradas',
  cavidades_cecav_canie: 'Cavidades CECAV/CANIE',
  caminhamento: 'Mapa de Caminhamento',
  raio_de_250m_de_cavidades: 'Raio de 250m de Cavidades',
  curvas_de_nivel: 'Curvas de Nível',
  extensao_rodovia_prospectada: 'Extensão de Rodovia Prospectada'
}

const normalizeText = (value) =>
  String(value || '')
    .normalize('NFD')
    .replace(/[\u0300-\u036f]/g, '')
    .toLowerCase()

const isProspeccao = computed(() => normalizeText(props.subproduto).includes('prospeccao'))
const tipos = computed(() => (isProspeccao.value ? tiposProspeccao : tiposPadrao))
const nomesTipos = computed(() => (isProspeccao.value ? nomesTiposProspeccao : nomesTiposPadrao))

const activeSubTab = ref('')
const uploadedFiles = ref({})
const features = ref({})
const observacoes = ref({})
const savedObservacoes = ref({})
const anexos = ref(Array.isArray(props.resultadosAnexos) ? [...props.resultadosAnexos] : [])
const rendered = ref({})
const zipFiles = ref({})
const maps = ref({})

// === Estados de carregamento ===
const isLoading = ref(false)
const loadingMessage = ref('')
const loadingLayerIds = ref([])
const loadingTipo = ref(null)
const { start: startLoading, stop: stopLoading } = useLoading()

// === GeoServer layers da campanha
const geoLayers = ref([])
const renderedLayers = ref({})
const layerMaps = ref({})

// === ESTUDOS POSTERIORES (estado local reativo)
const necessarioEstudos = ref(false)
const estudos = ref([]) // [{ __key, subproduto_id, quantidade, coordenadas: [{__key, lat, lng}] }]

// inicialização reativa geral
const ensureTiposState = (tiposLista) => {
  tiposLista.forEach((t) => {
    if (!(t in uploadedFiles.value)) uploadedFiles.value[t] = []
    if (!(t in features.value)) features.value[t] = []
    if (!(t in observacoes.value)) observacoes.value[t] = ''
    if (!(t in savedObservacoes.value)) savedObservacoes.value[t] = ''
    if (!(t in rendered.value)) rendered.value[t] = false
    if (!(t in zipFiles.value)) zipFiles.value[t] = null
  })
}

watch(
  tipos,
  (tiposLista) => {
    ensureTiposState(tiposLista)

    if (!tiposLista.includes(activeSubTab.value)) {
      activeSubTab.value = tiposLista[0] || ''
    }
  },
  { immediate: true }
)

const editando = ref({})

watch(
  tipos,
  (tiposLista) => {
    tiposLista.forEach((t) => {
      if (!(t in editando.value)) editando.value[t] = false
    })
  },
  { immediate: true }
)

// === Carrega layers do GeoServer vinculadas a esta campanha
const loadGeoLayers = async () => {
  if (!props.campanhaId) return
  isLoading.value = true
  loadingMessage.value = 'Carregando camadas vinculadas...'
  startLoading()
  try {
    const { data } = await axios.get(route('sgc.contratada.espeleologia.layers.index'), {
      params: { campanha_id: props.campanhaId }
    })
    geoLayers.value = Array.isArray(data) ? data : []
  } catch (e) {
    console.error('Erro ao carregar layers da campanha:', e)
  } finally {
    isLoading.value = false
    loadingMessage.value = ''
    stopLoading()
  }
}

onMounted(loadGeoLayers)
watch(() => props.campanhaId, loadGeoLayers)

const layersForTipo = (tipo) => geoLayers.value.filter(l => l.tipo === tipo)

const tipoLabel = (tipo, index, total) => {
  const base = nomesTipos.value[tipo] || tipo
  return total > 1 ? `${base} ${index + 1}` : base
}

const toggleRenderLayer = async (layer) => {
  renderedLayers.value = { ...renderedLayers.value, [layer.id]: !renderedLayers.value[layer.id] }
  if (renderedLayers.value[layer.id]) {
    loadingLayerIds.value = [...loadingLayerIds.value, layer.id]
    startLoading()
    await nextTick()
    setTimeout(() => {
      renderLayerWms(layer)
      // O indicador por camada será removido pelo evento 'load' do mapa Leaflet
    }, 50)
  } else {
    if (layerMaps.value[layer.id]) {
      try { layerMaps.value[layer.id].remove() } catch (e) { /* noop */ }
      layerMaps.value[layer.id] = null
    }
    loadingLayerIds.value = loadingLayerIds.value.filter(id => id !== layer.id)
  }
}

const renderLayerWms = (layer) => {
  const container = document.getElementById(`wms-map-${layer.id}`)
  if (!container) return
  if (layerMaps.value[layer.id]) {
    try { layerMaps.value[layer.id].remove() } catch (e) { /* noop */ }
  }
  const map = L.map(container).setView([-14.235, -51.925], 6)
  layerMaps.value[layer.id] = map

  // Remove indicador assim que os tiles terminarem de carregar
  map.on('load', () => {
    loadingLayerIds.value = loadingLayerIds.value.filter(id => id !== layer.id)
    stopLoading()
  })

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors'
  }).addTo(map)
  L.tileLayer.wms('/sgc/contratada/mapa/wms', {
    layers: `${layer.workspace}:${layer.layer_name}`,
    format: 'image/png',
    transparent: true,
    version: '1.1.0',
  }).addTo(map)
  setTimeout(() => map.invalidateSize(), 300)
}

const desvincularLayer = async (layerId, tipo) => {
  if (!confirm('Desvincular este mapa da campanha?')) return
  loadingLayerIds.value = [...loadingLayerIds.value, layerId]
  startLoading()
  try {
    await axios.delete(route('sgc.contratada.espeleologia.layers.desvincular', layerId), {
      params: { campanha_id: props.campanhaId }
    })
    if (layerMaps.value[layerId]) {
      try { layerMaps.value[layerId].remove() } catch (e) { /* noop */ }
      layerMaps.value[layerId] = null
    }
    geoLayers.value = geoLayers.value.filter(l => l.id !== layerId)
  } catch (e) {
    console.error('Erro ao desvincular layer:', e)
  } finally {
    loadingLayerIds.value = loadingLayerIds.value.filter(id => id !== layerId)
    stopLoading()
  }
}

// === SYNC anexos/observações vindos do backend
watch(
  () => props.resultadosAnexos,
  (newVal) => {
    anexos.value = Array.isArray(newVal) ? [...newVal] : []
    tipos.value.forEach((t) => {
      const a = anexos.value.find(x => x.tipo === t)
      savedObservacoes.value[t] = a?.comentario || ''
      observacoes.value[t] = savedObservacoes.value[t]
      // não liga modo edição sozinho
    })
  },
  { deep: true, immediate: true }
)

// === SYNC estudos posteriores vindos do backend
const normalizeEstudos = (arr = []) => {
  return arr.map((e, i) => ({
    __key: `${e.id || 'tmp'}-${i}-${Date.now()}`,
    subproduto_id: e.subproduto_id ?? '',
    quantidade: e.quantidade ?? '',
    coordenadas: (Array.isArray(e.coordenadas) ? e.coordenadas : (e.coordenadas ? JSON.parse(e.coordenadas) : []))
      .map((c, j) => ({
        __key: `c-${i}-${j}-${Date.now()}`,
        lat: c.lat ?? '',
        lng: c.lng ?? ''
      }))
  }))
}

watch(
  () => props.estudosPosteriores,
  (arr) => {
    const list = Array.isArray(arr) ? arr : []
    necessarioEstudos.value = !!(list[0]?.necessario)
    estudos.value = normalizeEstudos(list)
  },
  { immediate: true }
)

// === helpers estudos
const addEstudo = () => {
  estudos.value.push({
    __key: `n-${Date.now()}`,
    subproduto_id: '',
    quantidade: '',
    coordenadas: []
  })
}
const removeEstudo = (idx) => {
  estudos.value.splice(idx, 1)
}
const addPar = (idx) => {
  estudos.value[idx].coordenadas.push({
    __key: `p-${idx}-${Date.now()}`,
    lat: '',
    lng: ''
  })
}
const removePar = (idx, cIdx) => {
  estudos.value[idx].coordenadas.splice(cIdx, 1)
}

// === salvar estudos posteriores
const salvarEstudos = () => {
  const payload = {
    campanha_id: props.campanhaId,
    necessario: necessarioEstudos.value ? 1 : 0,
    estudos: necessarioEstudos.value
      ? estudos.value.map(e => ({
          subproduto_id: e.subproduto_id || null,
          quantidade: e.quantidade || null,
          coordenadas: e.coordenadas.map(c => ({ lat: c.lat || '', lng: c.lng || '' }))
        }))
      : []
  }

  router.post(
    // ajuste o nome da rota se o seu for diferente:
    route('sgc.contratada.produtos.espeleo.estudos.store', {
      contrato: props.contrato,
      produto: 'espeleologia'
    }),
    payload,
    {
      preserveState: true,
      preserveScroll: true,
      replace: true
    }
  )
}

// === Observações (mantém não reativo até clicar em salvar)
const salvarObservacao = (tipo) => {
  const anexo = anexos.value.find(a => a.tipo === tipo)
  if (!anexo) return

  router.post(
    route('sgc.contratada.produtos.espeleo.resultados.update', {
      contrato: props.contrato,
      produto: 'espeleologia',
      id: anexo.id
    }),
    { comentario: observacoes.value[tipo] },
    {
      preserveState: true,
      preserveScroll: true,
      replace: true,
      onSuccess: () => {
        savedObservacoes.value[tipo] = observacoes.value[tipo]
        editando.value[tipo] = false
      }
    }
  )
}

const removerObservacao = (tipo) => {
  const anexo = anexos.value.find(a => a.tipo === tipo)
  if (!anexo) return

  router.post(
    route('sgc.contratada.produtos.espeleo.resultados.update', {
      contrato: props.contrato,
      produto: 'espeleologia',
      id: anexo.id
    }),
    { comentario: '' },
    {
      preserveState: true,
      preserveScroll: true,
      replace: true,
      onSuccess: () => {
        savedObservacoes.value[tipo] = ''
        observacoes.value[tipo] = ''
        editando.value[tipo] = false
      }
    }
  )
}

const cancelarEdicao = (tipo) => {
  const a = anexos.value.find(x => x.tipo === tipo)
  observacoes.value[tipo] = a?.comentario || ''
  editando.value[tipo] = false
}

// === Upload / mapa (padrão)
const triggerFileInput = (tipo) => {
  const el = document.getElementById(`fileInput-${tipo}`)
  if (el) el.click()
}

const onFileChange = (event, tipo) => {
  const files = Array.from(event.target.files)
  processFiles(files, tipo)
}

const onDrop = (event, tipo) => {
  event.preventDefault()
  const files = Array.from(event.dataTransfer.files)
  processFiles(files, tipo)
}

const processFiles = async (files, tipo) => {
  uploadedFiles.value[tipo] = files
  features.value[tipo] = []
  const zipFile = files.find(f => f.name.endsWith('.zip'))
  if (!zipFile) return

  zipFiles.value[tipo] = zipFile
  loadingTipo.value = tipo
  startLoading()
  try {
    const zip = await JSZip.loadAsync(zipFile)
    const shpName = Object.keys(zip.files).find(n => n.endsWith('.shp'))
    if (!shpName) return
    const shpBuffer = await zip.file(shpName).async('arraybuffer')
    const geojson = await shapefile.read(shpBuffer, null, { name: 'features' })
    features.value[tipo] = geojson.features || []
    rendered.value[tipo] = true
    await nextTick()
    renderMap(tipo)
  } catch (e) {
    console.error(`Erro ao processar ZIP (${tipo})`, e)
  } finally {
    loadingTipo.value = null
    stopLoading()
  }
}

// envia shapefile para o novo controlador que publica no GeoServer
const vincularMapa = async (tipo) => {
  const zipFile = zipFiles.value[tipo]
  if (!zipFile) return
  if (!props.campanhaId) {
    console.error('Campanha não definida para vincular camada GeoServer.')
    return
  }

  const formData = new FormData()
  formData.append('file', zipFile)
  formData.append('campanha_id', props.campanhaId)
  formData.append('tipo', tipo)

  loadingTipo.value = tipo
  isLoading.value = true
  loadingMessage.value = 'Publicando no GeoServer...'
  startLoading()
  try {
    const response = await axios.post(
      route('sgc.contratada.espeleologia.layers.upload_shapefile'),
      formData,
      { headers: { 'Content-Type': 'multipart/form-data' } }
    )

    if (response.data.layer) {
      geoLayers.value.push({
        id: response.data.layer.id,
        layer_name: response.data.layer.layer_name,
        workspace: response.data.layer.workspace,
        title: response.data.layer.title,
        tipo,
      })
      uploadedFiles.value[tipo] = []
      zipFiles.value[tipo] = null
      features.value[tipo] = []
      if (maps.value[tipo]) {
        try { maps.value[tipo].remove() } catch (e) { /* noop */ }
        maps.value[tipo] = null
      }
      rendered.value[tipo] = false
    }
  } catch (error) {
    console.error('Erro ao vincular mapa:', error)
  } finally {
    loadingTipo.value = null
    isLoading.value = false
    loadingMessage.value = ''
    stopLoading()
  }
}

const toggleRender = (tipo) => {
  rendered.value[tipo] = !rendered.value[tipo]
  if (rendered.value[tipo]) {
    nextTick(() => {
      if (maps.value[tipo]) {
        try { maps.value[tipo].remove() } catch (e) { /* noop */ }
        maps.value[tipo] = null
      }
      setTimeout(() => { renderMap(tipo) }, 100)
    })
  }
}

const renderMap = async (tipo) => {
  await nextTick()
  const container = document.getElementById(`map-${tipo}`)
  if (!container) return

  const map = L.map(container).setView([-14.235, -51.925], 6)
  maps.value[tipo] = map

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors'
  }).addTo(map)

  if (features.value[tipo] && features.value[tipo].length > 0) {
    const layer = L.geoJSON(features.value[tipo]).addTo(map)
    map.fitBounds(layer.getBounds().pad(0.1))
  }

  setTimeout(() => map.invalidateSize(), 300)
}

const removerAnexo = (id) => {
  // se o anexo veio do GeoServer (upload recente), basta tirar da lista localmente
  const found = anexos.value.find(a => a.id === id)
  if (found && found.fromGeoServer) {
    anexos.value = anexos.value.filter(a => a.id !== id)
    emit('update-resultados-anexos', anexos.value)

    tipos.value.forEach(t => {
      if (!anexos.value.find(a => a.tipo === t)) {
        observacoes.value[t] = ''
        features.value[t] = []
        rendered.value[t] = false
        uploadedFiles.value[t] = []
      }
    })
    return
  }

  router.delete(route('sgc.contratada.produtos.espeleo.resultados.delete', {
    contrato: props.contrato,
    produto: 'espeleologia',
    id
  }), {
    preserveState: true,
    preserveScroll: true,
    onSuccess: () => {
      anexos.value = anexos.value.filter(a => a.id !== id)
      emit('update-resultados-anexos', anexos.value)

      tipos.value.forEach(t => {
        if (!anexos.value.find(a => a.tipo === t)) {
          observacoes.value[t] = ''
          features.value[t] = []
          rendered.value[t] = false
          uploadedFiles.value[t] = []
        }
      })
    }
  })
}
</script>

<style scoped>
@import 'leaflet/dist/leaflet.css';
@import '@fortawesome/fontawesome-free/css/all.min.css';

/* === Loading overlay do componente === */
.geo-loading-overlay {
  position: absolute;
  inset: 0;
  background: rgba(255, 255, 255, 0.75);
  z-index: 500;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 6px;
}

.geo-loading-box {
  background: white;
  border-radius: 10px;
  padding: 22px 32px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 4px;
  font-size: 0.95rem;
  color: #333;
}

.geo-fade-enter-active,
.geo-fade-leave-active {
  transition: opacity 0.2s ease;
}
.geo-fade-enter-from,
.geo-fade-leave-to {
  opacity: 0;
}

.nav-tabs .nav-link {
  color: #6c757d;
  font-weight: 500;
}
.nav-tabs .nav-link.active {
  color: #007bff;
  border-bottom: 2px solid #007bff;
}
.alert-info {
  font-size: 0.9rem;
}
</style>
