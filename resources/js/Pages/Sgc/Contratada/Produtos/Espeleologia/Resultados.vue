<template>
  <div>
    <h3>Resultados - Visualização de Shapefiles</h3>
    <p class="text-muted mb-4">
      Carregue shapefiles (ZIP) para cada tipo de mapa, vincule-os e adicione observações.
    </p>

    <!-- Sub-menu de abas -->
    <ul class="nav nav-tabs mb-4">
      <li class="nav-item" v-for="tipo in ['geologico','geomorfologico','cavidades']" :key="tipo">
        <a
          class="nav-link"
          :class="{ active: activeSubTab === tipo }"
          @click.prevent="activeSubTab = tipo"
        >
          {{ tipo === 'geologico' ? 'Geológico' : tipo === 'geomorfologico' ? 'Geomorfológico' : 'Cavidades CECAV/SBE' }}
        </a>
      </li>
    </ul>

    <!-- Conteúdo das abas -->
    <div class="tab-content">
      <div
        v-for="tipo in ['geologico', 'geomorfologico', 'cavidades']"
        :key="tipo"
        class="tab-pane fade"
        :class="{ 'show active': activeSubTab === tipo }"
        v-show="activeSubTab === tipo"
      >
        <div class="mb-5">
          <h4>
            {{ tipo === 'geologico' ? 'Mapa Geológico' :
               tipo === 'geomorfologico' ? 'Mapa Geomorfológico' :
               'Cavidades do Banco CECAV e SBE' }}
          </h4>

          <!-- Dropzone só aparece se não houver anexo vinculado -->
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
              <button @click="triggerFileInput(tipo)" class="btn btn-link p-0">clique para selecionar</button>.
            </p>
            <input
              type="file"
              @change="onFileChange($event, tipo)"
              multiple
              accept=".zip,.shp,.shx,.dbf"
              :ref="'fileInput-' + tipo"
              style="display: none;"
            />
            <div v-if="uploadedFiles[tipo].length > 0" class="mt-2 text-success">
              <small>Carregado: {{ uploadedFiles[tipo].map(f => f.name).join(', ') }}</small>
            </div>
            <small v-if="errors[tipo]" class="text-danger d-block">{{ errors[tipo] }}</small>
          </div>

          <!-- Botão Vincular Mapa -->
          <button
            v-if="features[tipo].length > 0 && !anexos.find(a => a.tipo === tipo)"
            @click="vincularMapa(tipo)"
            class="btn btn-primary mb-3"
          >
            Vincular Mapa
          </button>

          <!-- Indicador de arquivo vinculado -->
          <div
            v-if="anexos.find(a => a.tipo === tipo)"
            class="alert alert-info mb-3 d-flex align-items-center justify-content-between"
          >
            <span>Arquivo vinculado: {{ anexos.find(a => a.tipo === tipo).nome_arquivo }}</span>
            <div>
              <button @click="toggleRender(tipo)" class="btn btn-sm btn-outline-secondary me-1">
                <i :class="rendered[tipo] ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                {{ rendered[tipo] ? 'Esconder Mapa' : 'Ver Mapa' }}
              </button>
              <button
                @click="removerAnexo(anexos.find(a => a.tipo === tipo).id)"
                class="btn btn-sm btn-outline-danger"
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
          <p v-else-if="!features[tipo].length" class="text-muted">
            Carregue um shapefile para ver o mapa.
          </p>

          <!-- Observações -->
          <div class="mt-3">
            <label class="form-label">Observações</label>
            <textarea
              v-model="observacoes[tipo]"
              class="form-control"
              rows="4"
              :placeholder="'Descreva observações sobre o ' + (tipo === 'geologico' ? 'mapa geológico' : tipo === 'geomorfologico' ? 'mapa geomorfológico' : 'mapa de cavidades')"
              @input="updateObservacao(tipo)"
            ></textarea>
          </div>
        </div>
      </div>
    </div>

    <!-- Lista de Anexos -->
    <div v-if="anexos.length > 0" class="mt-3">
      <div
        v-for="anexo in anexos.filter(a => a.tipo === activeSubTab)"
        :key="anexo.id"
        class="flex items-center justify-between border rounded-lg p-2 mb-2"
      >
        <div class="text-sm font-medium">{{ anexo.nome_arquivo }}</div>
        <div class="flex items-center gap-2">
          <a :href="anexo.url_publica" target="_blank" class="text-blue-600 hover:underline">
            Download
          </a>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, nextTick, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import L from 'leaflet';
import * as shapefile from 'shapefile';
import JSZip from 'jszip';

const props = defineProps({
  empreendimentos: Array,
  errors: Object,
  campanhaId: Number,
  contrato: Number,
  resultadosAnexos: Array,
});

const emit = defineEmits(['update-resultados-anexos']);

const activeSubTab = ref('geologico');
const uploadedFiles = ref({ geologico: [], geomorfologico: [], cavidades: [] });
const features = ref({ geologico: [], geomorfologico: [], cavidades: [] });
const observacoes = ref({ geologico: '', geomorfologico: '', cavidades: '' });
const anexos = ref(props.resultadosAnexos || []);
const rendered = ref({ geologico: false, geomorfologico: false, cavidades: false });
const zipFiles = ref({ geologico: null, geomorfologico: null, cavidades: null });
const maps = ref({});

// Mantém anexos sincronizados com o backend
watch(
  () => props.resultadosAnexos,
  (newVal) => { anexos.value = [...newVal]; },
  { deep: true, immediate: true }
);

const triggerFileInput = (tipo) => {
  document.querySelector(`input[ref='fileInput-${tipo}']`)?.click();
};

const onFileChange = (event, tipo) => {
  const files = Array.from(event.target.files);
  processFiles(files, tipo);
};

const onDrop = (event, tipo) => {
  event.preventDefault();
  const files = Array.from(event.dataTransfer.files);
  processFiles(files, tipo);
};

const processFiles = async (files, tipo) => {
  uploadedFiles.value[tipo] = files;
  features.value[tipo] = [];
  const zipFile = files.find(f => f.name.endsWith('.zip'));
  if (!zipFile) return;

  zipFiles.value[tipo] = zipFile;
  try {
    const zip = await JSZip.loadAsync(zipFile);
    const shpName = Object.keys(zip.files).find(n => n.endsWith('.shp'));
    if (!shpName) return;
    const shpBuffer = await zip.file(shpName).async('arraybuffer');
    const geojson = await shapefile.read(shpBuffer, null, { name: 'features' });
    features.value[tipo] = geojson.features || [];
    rendered.value[tipo] = true;
    await nextTick();
    renderMap(tipo);
  } catch (e) {
    console.error(`Erro ao processar ZIP (${tipo})`, e);
  }
};

// Vincular mapa: mapa fica oculto automaticamente
const vincularMapa = async (tipo) => {
  const zipFile = zipFiles.value[tipo];
  if (!zipFile) return;

  const formData = new FormData();
  formData.append('zip_file', zipFile);
  formData.append('campanha_id', props.campanhaId);
  formData.append('tipo', tipo);
  formData.append('comentario', observacoes.value[tipo] || '');

  try {
    const response = await axios.post(
      route('sgc.contratada.produtos.espeleo.resultados.upload', {
        contrato: props.contrato,
        produto: 'espeleologia',
      }),
      formData,
      { headers: { 'Content-Type': 'multipart/form-data' } }
    );

    if (response.data.success) {
      anexos.value = response.data.resultadosAnexos || [];
      emit('update-resultados-anexos', anexos.value);
      uploadedFiles.value[tipo] = [];
      rendered.value[tipo] = false; // mapa oculto
    }
  } catch (error) {
    console.error('Erro ao vincular mapa:', error);
  }
};

// Alternar visualização do mapa
const toggleRender = (tipo) => {
  rendered.value[tipo] = !rendered.value[tipo];
  if (rendered.value[tipo]) {
    nextTick(() => {
      if (maps.value[tipo]) {
        try { maps.value[tipo].remove(); } catch(e){ console.warn(e); }
        maps.value[tipo] = null;
      }
      setTimeout(() => { renderMap(tipo); }, 100);
    });
  }
};

const renderMap = async (tipo) => {
  await nextTick();
  const container = document.getElementById(`map-${tipo}`);
  if (!container) return;

  const map = L.map(container).setView([-14.235, -51.925], 6);
  maps.value[tipo] = map;

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors',
  }).addTo(map);

  if (features.value[tipo] && features.value[tipo].length > 0) {
    const layer = L.geoJSON(features.value[tipo]).addTo(map);
    map.fitBounds(layer.getBounds().pad(0.1));
  }

  setTimeout(() => map.invalidateSize(), 300);
};

const updateObservacao = (tipo) => {
  const anexo = anexos.value.find(a => a.tipo === tipo);
  if (!anexo) return;
  router.post(route('sgc.contratada.produtos.espeleo.resultados.update', {
    contrato: props.contrato,
    produto: 'espeleologia',
    id: anexo.id,
  }), { comentario: observacoes.value[tipo] });
};

// Remover anexo + limpar observações, features e liberar dropzone
const removerAnexo = (id) => {
  router.delete(route('sgc.contratada.produtos.espeleo.resultados.delete', {
    contrato: props.contrato,
    produto: 'espeleologia',
    id,
  }), {
    preserveState: true,
    preserveScroll: true,
    onSuccess: () => {
      anexos.value = anexos.value.filter(a => a.id !== id);
      emit('update-resultados-anexos', anexos.value);

      Object.keys(observacoes.value).forEach(t => {
        if (!anexos.value.find(a => a.tipo === t)) {
          observacoes.value[t] = '';
          features.value[t] = [];
          rendered.value[t] = false;
          uploadedFiles.value[t] = [];
        }
      });
    },
  });
};
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
