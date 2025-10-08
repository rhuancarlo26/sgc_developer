<template>
  <div>
    <h3>Resultados - Visualização de Shapefiles</h3>
    <p class="text-muted mb-3">Arraste um shapefile (ZIP) aqui para renderizar no mapa (ex: pontos de cavidades).</p>

    <!-- Dropzone para Upload -->
    <div
      @drop="onDrop"
      @dragover.prevent
      @dragenter.prevent
      class="border p-4 rounded bg-light mb-4 text-center"
      :class="{ 'border-danger': errors.resultados }"
      style="min-height: 100px;"
    >
      <p>Arraste .zip do shapefile aqui ou <button @click="triggerFileInput" class="btn btn-link p-0">clique para selecionar</button>.</p>
      <input
        type="file"
        @change="onFileChange"
        multiple
        accept=".zip,.shp,.shx,.dbf"
        ref="fileInput"
        style="display: none;"
      />
      <div v-if="uploadedFiles.length > 0" class="mt-2 text-success">
        <small>Carregado: {{ uploadedFiles.map(f => f.name).join(', ') }}</small>
      </div>
      <small v-if="errors.resultados" class="text-danger d-block">{{ errors.resultados }}</small>
    </div>

    <!-- Mapa -->
    <div id="map" style="height: 600px; border: 1px solid #ddd; border-radius: 4px;" v-if="features.length > 0"></div>
    <p v-else class="text-muted">Carregue um shapefile para ver o mapa.</p>

    <!-- Lista de Anexos -->
    <div v-if="anexos.length > 0" class="mt-3">
      <h5>Anexos Carregados</h5>
      <ul class="list-group">
        <li v-for="anexo in anexos" :key="anexo.id" class="list-group-item d-flex justify-content-between align-items-center">
          {{ anexo.nome_arquivo }}
          <a :href="anexo.url_publica" target="_blank" class="btn btn-sm btn-outline-primary">Download</a>
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

const uploadedFiles = ref([]);
const features = ref([]);
const fileInput = ref(null);
const anexos = ref(props.resultadosAnexos || []);
let map = null;

const triggerFileInput = () => {
  fileInput.value?.click();
};

const onFileChange = (event) => {
  const files = Array.from(event.target.files);
  processFiles(files);
  fileInput.value.value = '';
};

const onDrop = (event) => {
  event.preventDefault();
  const files = Array.from(event.dataTransfer.files);
  processFiles(files);
};

const processFiles = async (files) => {
  uploadedFiles.value = files;
  features.value = [];

  const zipFile = files.find(f => f.name.endsWith('.zip'));
  if (!zipFile) {
    console.warn('Nenhum .zip encontrado');
    return;
  }

  try {
    // Parse ZIP pra render
    const zip = await JSZip.loadAsync(zipFile);
    const shpFileInZip = Object.keys(zip.files).find(name => name.endsWith('.shp'));
    if (!shpFileInZip) {
      console.warn('Nenhum .shp encontrado dentro do ZIP');
      return;
    }
    const shpBuffer = await zip.file(shpFileInZip).async('arraybuffer');
    const geojson = await shapefile.read(shpBuffer, null, { name: 'features' });
    features.value = geojson.features || [];
    console.log('Features parseadas:', features.value);
    await nextTick();
    renderMap();

    // Upload pro backend
    const formData = new FormData();
    formData.append('zip_file', zipFile);
    formData.append('campanha_id', props.campanhaId);

    router.post(route('sgc.contratada.produtos.espeleo.resultados.upload', {
      contrato: props.contrato,
      produto: 'espeleologia',
    }), formData, {
      onSuccess: (page) => {
        const newAnexo = page.props.flash?.anexo || null;
        if (newAnexo) {
          anexos.value.push(newAnexo);
          emit('update-resultados-anexos', anexos.value);
          console.log('Anexo salvo:', newAnexo);
        }
      },
      onError: (err) => {
        console.error('Erro no upload:', err);
      },
    });
  } catch (err) {
    console.error('Erro ao processar ZIP:', err);
  }
};

const initMap = () => {
  const emp = props.empreendimentos[0];
  const center = emp && emp.segmento ? 
    [parseFloat(emp.segmento.split(' - ')[0]) || -14.235, -51.925] : 
    [-14.235, -51.925];
  
  map = L.map('map').setView(center, 6);
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors'
  }).addTo(map);
};

const renderMap = () => {
  if (!map) {
    initMap();
  } else {
    map.eachLayer(layer => {
      if (layer instanceof L.GeoJSON) {
        map.removeLayer(layer);
      }
    });
  }

  if (features.value.length === 0) return;

  const geoLayer = L.geoJSON(features.value, {
    pointToLayer: (feature, latlng) => L.circleMarker(latlng, { radius: 5, fillColor: 'red' }),
    style: (feature) => ({
      color: feature.geometry.type === 'Point' ? 'red' : feature.geometry.type === 'LineString' ? 'blue' : 'green',
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
  }).addTo(map);

  map.fitBounds(geoLayer.getBounds().pad(0.1));
};

onMounted(async () => {
  await nextTick();
  if (features.value.length > 0) {
    renderMap();
  }
});
</script>

<style scoped>
@import 'leaflet/dist/leaflet.css';

#map :deep(img.leaflet-tile) {
  /* Ajustes se necessário */
}
.list-group-item {
  font-size: 0.9rem;
}
</style>