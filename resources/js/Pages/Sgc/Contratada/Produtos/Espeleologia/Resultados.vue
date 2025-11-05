<template>
  <div>
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
            v-if="anexos.find(a => a.tipo === tipo)"
            class="fas fa-check-circle text-success ms-1"
          ></i>
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
                  + Adicionar Par
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
            <div
              v-if="!anexos.find(a => a.tipo === tipo)"
              @drop="onDrop($event, tipo)"
              @dragover.prevent
              @dragenter.prevent
              class="border p-4 rounded bg-light mb-3 text-center"
              :class="{ 'border-danger': errors[tipo] }"
              style="min-height: 100px;"
            >
              <p>
                Arraste .zip do shapefile aqui ou
                <button type="button" @click.prevent="triggerFileInput(tipo)" class="btn btn-link p-0">clique para selecionar</button>.
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

            <button
              v-if="features[tipo].length > 0 && !anexos.find(a => a.tipo === tipo)"
              @click="vincularMapa(tipo)"
              class="btn btn-primary mb-3"
              type="button"
            >
              Vincular Mapa
            </button>

            <div
              v-if="anexos.find(a => a.tipo === tipo)"
              class="alert alert-info mb-3 d-flex align-items-center justify-content-between"
            >
              <span>Arquivo vinculado: {{ anexos.find(a => a.tipo === tipo).nome_arquivo }}</span>
              <div>
                <button @click="toggleRender(tipo)" class="btn btn-sm btn-outline-secondary me-1" type="button">
                  <i :class="rendered[tipo] ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                  {{ rendered[tipo] ? 'Esconder Mapa' : 'Ver Mapa' }}
                </button>
                <button
                  @click="removerAnexo(anexos.find(a => a.tipo === tipo).id)"
                  class="btn btn-sm btn-outline-danger"
                  type="button"
                >
                  <i class="fas fa-trash-alt"></i> Excluir
                </button>
              </div>
            </div>

            <!-- Mapa -->
            <div
              v-if="rendered[tipo]"
              :id="'map-' + tipo"
              style="height: 600px; border: 1px solid #ddd; border-radius: 4px;"
              class="mb-3"
            ></div>
            <p v-else-if="anexos.find(a => a.tipo === tipo)" class="text-muted">
              Mapa vinculado, clique no olho para visualizar.
            </p>

            <!-- Observações -->
            <div v-if="anexos.find(a => a.tipo === tipo)" class="mt-3">
              <div v-if="!editando[tipo] && savedObservacoes[tipo]">
                <div class="p-3 border rounded bg-light">
                  <strong>Observação:</strong>
                  <p class="mb-2">{{ observacoes[tipo] }}</p>

                  <button class="btn btn-sm btn-outline-primary me-2" @click="editando[tipo] = true" type="button">
                    <i class="fas fa-edit"></i> Editar
                  </button>

                  <button class="btn btn-sm btn-outline-danger" @click="removerObservacao(tipo)" type="button">
                    <i class="fas fa-trash"></i> Excluir
                  </button>
                </div>
              </div>

              <div v-else>
                <label class="form-label">Observações</label>
                <textarea
                  v-model="observacoes[tipo]"
                  class="form-control"
                  rows="4"
                ></textarea>

                <button class="btn btn-success btn-sm mt-2" @click="salvarObservacao(tipo)" type="button">
                  <i class="fas fa-save"></i> Salvar Observação
                </button>

                <button class="btn btn-secondary btn-sm mt-2 ms-2" @click="cancelarEdicao(tipo)" type="button">
                  Cancelar
                </button>
              </div>
            </div>
          </template>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, nextTick, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import L from 'leaflet'
import * as shapefile from 'shapefile'
import JSZip from 'jszip'

const props = defineProps({
  empreendimentos: Array,
  errors: Object,
  campanhaId: Number,
  contrato: Number,
  resultadosAnexos: Array,
  subprodutosEspeleologia: { type: Array, default: () => [] },
  estudosPosteriores: { type: Array, default: () => [] },
})

const emit = defineEmits(['update-resultados-anexos'])

const tipos = [
  'geologico', 'geomorfologico', 'hipsometrico', 
  'declividades', 'hidrografico', 'cavidades', 
  'limites_areas', 'potencial_inicial', 'potencial_reclassificado',
  'projeto_engenharia', 'estudos_posteriores'
]

const nomesTipos = {
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

const activeSubTab = ref('geologico')
const uploadedFiles = ref({})
const features = ref({})
const observacoes = ref({})
const savedObservacoes = ref({})
const anexos = ref(Array.isArray(props.resultadosAnexos) ? [...props.resultadosAnexos] : [])
const rendered = ref({})
const zipFiles = ref({})
const maps = ref({})

// === ESTUDOS POSTERIORES (estado local reativo)
const necessarioEstudos = ref(false)
const estudos = ref([]) // [{ __key, subproduto_id, quantidade, coordenadas: [{__key, lat, lng}] }]

// inicialização reativa geral
tipos.forEach(t => {
  uploadedFiles.value[t] = []
  features.value[t] = []
  observacoes.value[t] = ''
  savedObservacoes.value[t] = ''
  rendered.value[t] = false
  zipFiles.value[t] = null
})

// === SYNC anexos/observações vindos do backend
watch(
  () => props.resultadosAnexos,
  (newVal) => {
    anexos.value = Array.isArray(newVal) ? [...newVal] : []
    tipos.forEach((t) => {
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
const editando = ref({})
tipos.forEach(t => { editando.value[t] = false })

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
  }
}

const vincularMapa = async (tipo) => {
  const zipFile = zipFiles.value[tipo]
  if (!zipFile) return

  const formData = new FormData()
  formData.append('zip_file', zipFile)
  formData.append('campanha_id', props.campanhaId)
  formData.append('tipo', tipo)
  formData.append('comentario', observacoes.value[tipo] || '')

  try {
    const response = await axios.post(
      route('sgc.contratada.produtos.espeleo.resultados.upload', {
        contrato: props.contrato,
        produto: 'espeleologia'
      }),
      formData,
      { headers: { 'Content-Type': 'multipart/form-data' } }
    )

    if (response.data.success) {
      anexos.value = response.data.resultadosAnexos || []
      emit('update-resultados-anexos', anexos.value)
      uploadedFiles.value[tipo] = []
      rendered.value[tipo] = false
    }
  } catch (error) {
    console.error('Erro ao vincular mapa:', error)
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

      tipos.forEach(t => {
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
