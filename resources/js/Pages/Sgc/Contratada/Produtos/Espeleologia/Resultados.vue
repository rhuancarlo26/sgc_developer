<template>
  <div>
    <h3>Resultados - Visualização de Shapefiles</h3>
    <p class="text-muted mb-4">Carregue shapefiles (ZIP) para cada tipo de mapa, vincule-os e adicione observações.</p>

    <!-- Sub-menu de abas -->
    <ul class="nav nav-tabs mb-4">
      <li class="nav-item">
        <a
          class="nav-link"
          :class="{ active: activeSubTab === 'geologico' }"
          @click.prevent="activeSubTab = 'geologico'"
        >Geológico</a>
      </li>
      <li class="nav-item">
        <a
          class="nav-link"
          :class="{ active: activeSubTab === 'geomorfologico' }"
          @click.prevent="activeSubTab = 'geomorfologico'"
        >Geomorfológico</a>
      </li>
      <li class="nav-item">
        <a
          class="nav-link"
          :class="{ active: activeSubTab === 'cavidades' }"
          @click.prevent="activeSubTab = 'cavidades'"
        >Cavidades CECAV/SBE</a>
      </li>
    </ul>

    <!-- Conteúdo da aba ativa -->
    <div class="tab-content">
      <div v-for="tipo in ['geologico', 'geomorfologico', 'cavidades']" :key="tipo" class="tab-pane fade" :class="{ 'show active': activeSubTab === tipo }">
        <div v-if="activeSubTab === tipo" class="mb-5">
          <h4>{{ tipo === 'geologico' ? 'Mapa Geológico' : tipo === 'geomorfologico' ? 'Mapa Geomorfológico' : 'Cavidades do Banco CECAV e SBE' }}</h4>
          
          <!-- Dropzone -->
          <div
            @drop="onDrop($event, tipo)"
            @dragover.prevent
            @dragenter.prevent
            class="border p-4 rounded bg-light mb-3 text-center"
            :class="{ 'border-danger': errors[tipo] }"
            style="min-height: 100px;"
          >
            <p>Arraste .zip do shapefile aqui ou <button @click="triggerFileInput(tipo)" class="btn btn-link p-0">clique para selecionar</button>.</p>
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
          <div v-if="anexos.find(a => a.tipo === tipo)" class="alert alert-info mb-3 d-flex align-items-center">
            <span>Arquivo vinculado: {{ anexos.find(a => a.tipo === tipo).nome_arquivo }}</span>
            <button @click="toggleRender(tipo)" class="btn btn-sm btn-outline-secondary ms-2">
              <i :class="rendered[tipo] ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
              {{ rendered[tipo] ? 'Esconder Mapa' : 'Ver Mapa' }}
            </button>
          </div>

          <!-- Mapa -->
          <div v-if="rendered[tipo]" :id="'map-' + tipo" style="height: 600px; border: 1px solid #ddd; border-radius: 4px;" class="mb-3"></div>
          <p v-else-if="anexos.find(a => a.tipo === tipo)" class="text-muted">Mapa vinculado, clique no olho para visualizar.</p>
          <p v-else-if="!features[tipo].length" class="text-muted">Carregue um shapefile para ver o mapa.</p>

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
    <div v-if="anexos.length > 0" class="mt-4">
      <h5>Anexos Carregados</h5>
      <ul class="list-group">
        <li v-for="anexo in anexos" :key="anexo.id" class="list-group-item d-flex justify-content-between align-items-center">
          {{ anexo.nome_arquivo }} ({{ anexo.tipo }})
          <div>
            <a :href="anexo.url_publica" target="_blank" class="btn btn-sm btn-outline-primary me-2">Download</a>
            <button @click="removerAnexo(anexo.id)" class="btn btn-sm btn-outline-danger">Remover</button>
          </div>
        </li>
      </ul>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, nextTick } from 'vue';
import { router } from '@inertiajs/vue3';
import L from 'leaflet';
import * as shapefile from 'shapefile';
import JSZip from 'jszip';

const props = defineProps({
  empreendimentos: {
    type: Array,
    default: () => [],
  },
  errors: {
    type: Object,
    default: () => ({}),
  },
  campanhaId: {
    type: Number,
    required: true,
  },
  contrato: {
    type: Number,
    required: true,
  },
  resultadosAnexos: {
    type: Array,
    default: () => [],
  },
});

const emit = defineEmits(['update-resultados-anexos']);

const activeSubTab = ref('geologico');
const uploadedFiles = ref({
  geologico: [],
  geomorfologico: [],
  cavidades: [],
});
const features = ref({
  geologico: [],
  geomorfologico: [],
  cavidades: [],
});
const observacoes = ref({
  geologico: '',
  geomorfologico: '',
  cavidades: '',
});
const anexos = ref(props.resultadosAnexos || []);
const rendered = ref({
  geologico: false,
  geomorfologico: false,
  cavidades: false,
});
const zipFiles = ref({
  geologico: null,
  geomorfologico: null,
  cavidades: null,
});
const fileInputs = ref({});
const maps = ref({});

// Trigger do input de arquivo
const triggerFileInput = (tipo) => {
  fileInputs.value[tipo]?.click();
};

// Handler para upload via input
const onFileChange = (event, tipo) => {
  const files = Array.from(event.target.files);
  processFiles(files, tipo);
};

// Handler para drop de arquivo
const onDrop = (event, tipo) => {
  event.preventDefault();
  const files = Array.from(event.dataTransfer.files);
  processFiles(files, tipo);
};

// Processa o arquivo ZIP e renderiza imediatamente
const processFiles = async (files, tipo) => {
  uploadedFiles.value[tipo] = files;
  features.value[tipo] = [];
  zipFiles.value[tipo] = null

  const zipFile = files.find(f => f.name.endsWith('.zip'));
  if (!zipFile) {
    console.warn(`Nenhum .zip encontrado para ${tipo}`);
    return;
  }

  zipFiles.value[tipo] = zipFile; // Armazena ZIP pra upload posterior

  try {
    const zip = await JSZip.loadAsync(zipFile);
    const shpFileInZip = Object.keys(zip.files).find(name => name.endsWith('.shp'));
    if (!shpFileInZip) {
      console.warn(`Nenhum .shp encontrado no ZIP para ${tipo}`);
      return;
    }
    const shpBuffer = await zip.file(shpFileInZip).async('arraybuffer');
    const geojson = await shapefile.read(shpBuffer, null, { name: 'features' });
    features.value[tipo] = geojson.features || [];
    console.log(`Features parseadas (${tipo}):`, features.value[tipo]);
    rendered.value[tipo] = true; // Renderiza mapa imediatamente
    await nextTick();
    renderMap(tipo);
  } catch (err) {
    console.error(`Erro ao processar ZIP (${tipo}):`, err);
  }
};

// Vincula o mapa (upload pro backend)
const vincularMapa = (tipo) => {
  const zipFile = zipFiles.value[tipo];
  if (!zipFile) {
    console.warn(`Nenhum ZIP para vincular em ${tipo}`);
    return;
  }

  const formData = new FormData();
  formData.append('zip_file', zipFile);
  formData.append('campanha_id', props.campanhaId);
  formData.append('tipo', tipo);
  formData.append('comentario', observacoes.value[tipo] || '');

  router.post(route('sgc.contratada.produtos.espeleo.resultados.upload', {
    contrato: props.contrato,
    produto: 'espeleologia',
  }), formData, {
    preserveState: true,
    preserveScroll: true,
    onSuccess: (page) => {
      const newAnexo = page.props.flash?.anexo || null;
      if (newAnexo) {
        anexos.value.push(newAnexo);
        emit('update-resultados-anexos', anexos.value);
        console.log(`Anexo salvo (${tipo}):`, newAnexo);
        uploadedFiles.value[tipo] = [];
        zipFiles.value[tipo] = null;
        rendered.value[tipo] = true; // Mantém mapa visível após vincular
        nextTick(() => renderMap(tipo));
      }
    },
    onError: (err) => {
      console.error(`Erro no upload (${tipo}):`, err);
    },
  });
};

// Toggle para renderizar/esconder mapa
const toggleRender = (tipo) => {
  rendered.value[tipo] = !rendered.value[tipo];
  if (rendered.value[tipo] && features.value[tipo].length > 0) {
    nextTick(() => renderMap(tipo));
  }
};

// Inicializa o mapa
const initMap = (tipo) => {
  const emp = props.empreendimentos[0];
  const center = emp && emp.segmento ? 
    [parseFloat(emp.segmento.split(' - ')[0]) || -14.235, -51.925] : 
    [-14.235, -51.925];
  
  maps.value[tipo] = L.map(`map-${tipo}`).setView(center, 6);
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors'
  }).addTo(maps.value[tipo]);
};

// Renderiza o mapa
const renderMap = (tipo) => {
  if (!maps.value[tipo]) {
    initMap(tipo);
  } else {
    maps.value[tipo].eachLayer(layer => {
      if (layer instanceof L.GeoJSON) {
        maps.value[tipo].removeLayer(layer);
      }
    });
  }

  if (features.value[tipo].length === 0) return;

  const geoLayer = L.geoJSON(features.value[tipo], {
    pointToLayer: (feature, latlng) => L.circleMarker(latlng, { radius: 5, fillColor: tipo === 'cavidades' ? 'red' : tipo === 'geologico' ? 'blue' : 'green' }),
    style: (feature) => ({
      color: feature.geometry.type === 'Point' ? (tipo === 'cavidades' ? 'red' : tipo === 'geologico' ? 'blue' : 'green') : 
             feature.geometry.type === 'LineString' ? 'purple' : 'orange',
      weight: 2,
      opacity: 0.8,
    }),
    onEachFeature: (feature, layer) => {
      layer.bindPopup(`
        <div>
          <strong>Tipo:</strong> ${feature.geometry.type}<br>
          <strong>Props:</strong> <pre>${JSON.stringify(feature.properties, null, 2)}</pre>
        </div>
      `);
    },
  }).addTo(maps.value[tipo]);

  maps.value[tipo].fitBounds(geoLayer.getBounds().pad(0.1));
};

// Atualiza observação no backend
const updateObservacao = (tipo) => {
  const anexo = anexos.value.find(a => a.tipo === tipo);
  if (anexo) {
    router.post(route('sgc.contratada.produtos.espeleo.resultados.update', {
      contrato: props.contrato,
      produto: 'espeleologia',
      id: anexo.id,
    }), { comentario: observacoes.value[tipo] }, {
      preserveState: true,
      preserveScroll: true,
      onSuccess: () => {
        console.log(`Comentário atualizado (${tipo}):`, observacoes.value[tipo]);
      },
      onError: (err) => {
        console.error(`Erro ao atualizar comentário (${tipo}):`, err);
      },
    });
  }
};

// Remove anexo
const removerAnexo = (id) => {
  router.delete(route('sgc.contratada.produtos.espeleo.resultados.delete', {
    contrato: props.contrato,
    produto: 'espeleologia',
    id,
  }), {
    preserveState: true,
    preserveScroll: true,
    onSuccess: () => {
      const anexo = anexos.value.find(a => a.id === id);
      anexos.value = anexos.value.filter(a => a.id !== id);
      emit('update-resultados-anexos', anexos.value);
      if (anexo) {
        const tipo = anexo.tipo;
        uploadedFiles.value[tipo] = [];
        features.value[tipo] = [];
        rendered.value[tipo] = false;
        zipFiles.value[tipo] = null;
        if (maps.value[tipo]) {
          maps.value[tipo].eachLayer(layer => {
            if (layer instanceof L.GeoJSON) {
              maps.value[tipo].removeLayer(layer);
            }
          });
        }
        console.log('Anexo removido:', id);
      }
    },
    onError: (err) => {
      console.error('Erro ao remover anexo:', err);
    },
  });
};

// Carrega observações iniciais e configura mapas vinculados
onMounted(async () => {
  await nextTick();
  ['geologico', 'geomorfologico', 'cavidades'].forEach(tipo => {
    const anexo = props.resultadosAnexos.find(a => a.tipo === tipo);
    if (anexo && anexo.comentario) {
      observacoes.value[tipo] = anexo.comentario;
    }
    // Mapas vinculados não renderizam automaticamente
    rendered.value[tipo] = false;
  });
});
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
.list-group-item {
  font-size: 0.9rem;
}
.alert-info {
  font-size: 0.9rem;
}
</style>