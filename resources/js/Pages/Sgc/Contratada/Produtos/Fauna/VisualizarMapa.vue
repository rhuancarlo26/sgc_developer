<template>
  <div>
    <div class="row my-3">
        <div class="col-md-4">
            <select v-model="selectedFile" @change="loadSelectedFile" class="form-select">
                <option v-for="(path, index) in props.filePaths" :key="index" :value="path">
                    {{ getFileName(path) }}
                </option>
            </select>
        </div>
    </div>
    <div v-if="loading" class="loading-indicator">loading... ...</div>
    <div v-if="error">{{ error }}</div>
    <div id="map" style="height: 400px;"></div> <!-- Div para o mapa -->
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import shp from 'shpjs'; // Importa a lib
import L from 'leaflet'; // Importa Leaflet
import 'leaflet/dist/leaflet.css'; // CSS do Leaflet

const props = defineProps({
  filePaths: {
    type: Array,
    required: true,
    default: () => []
  }
});

const map = ref(null);
const overlayGroup = ref(null); // Grupo para as layers (para limpar facilmente)
const selectedFile = ref(null); // Path selecionado
const loading = ref(false);
const error = ref(null);

onMounted(() => {
  // Inicializa o mapa Leaflet
  map.value = L.map('map').setView([0, 0], 2); // Centro inicial
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap'
  }).addTo(map.value);

  // Cria o grupo de overlays como FeatureGroup para suportar getBounds
  overlayGroup.value = L.featureGroup().addTo(map.value);

  // Carrega o primeiro arquivo inicialmente, se houver
  if (props.filePaths.length > 0) {
    selectedFile.value = props.filePaths[0];
  }
});

// Watcher para carregar quando selectedFile mudar
watch(selectedFile, () => {
  loadSelectedFile();
});

const getFileName = (path) => {
  return path.split('/').pop().replace('.zip', ''); // Extrai nome: ex: 'xxx'
};

const loadSelectedFile = async () => {
  if (!selectedFile.value) return;

  loading.value = true;
  error.value = null;

  try {
    // Limpa todas as layers do grupo anterior
    overlayGroup.value.clearLayers();

    const path = selectedFile.value;
    const name = getFileName(path);

    // Fetch do arquivo ZIP como ArrayBuffer
    const response = await fetch(path);
    if (!response.ok) throw new Error(`Erro ao buscar ${name}: ${response.statusText}`);

    const buffer = await response.arrayBuffer();
    let geojson = await shp(buffer); // Parseia (detecta ZIP)

    // Se for array (múltiplos layers no ZIP), converte para FeatureCollection única
    if (Array.isArray(geojson)) {
      geojson = {
        type: 'FeatureCollection',
        features: geojson.flatMap(g => g.features || [])
      };
    }

    // Cria a nova layer e adiciona ao grupo
    const newLayer = L.geoJSON(geojson, {
      style: { color: '#ff7800', weight: 2 }, // Estilo
      onEachFeature: (feature, layer) => {
        console.log(`${name} properties:`, feature.properties);
        layer.bindPopup(`Detalhes de ${name}: ${JSON.stringify(feature.properties)}`);
      }
    });

    overlayGroup.value.addLayer(newLayer);

    // Zoom para os dados, com handling para points isolados
    const bounds = overlayGroup.value.getBounds();
    if (bounds.isValid()) {
      const ne = bounds.getNorthEast();
      const sw = bounds.getSouthWest();
      if (ne.equals(sw)) {
        // Caso de ponto único: centra e define zoom fixo
        const center = bounds.getCenter();
        map.value.setView(center, 15); // Zoom padrão para pontos (ajuste se quiser)
      } else {
        // Para áreas/polígonos/lines: fitBounds com padding
        map.value.fitBounds(bounds, { padding: [50, 50], maxZoom: 18 });
      }
    }

    // Se precisar enviar para backend via Inertia
    // Inertia.post('/save-map', { geojson, name });

  } catch (err) {
    console.log('Erro ao processar o arquivo: ' + err.message);
    error.value = 'Erro ao carregar o arquivo: ' + err.message;
  } finally {
    loading.value = false;
  }
};
</script>

<style scoped>
.loading-indicator {
  font-size: 1.2em;
  color: #666;
  text-align: center;
  margin: 20px 0;
  animation: ellipsis 1.5s infinite;
}

@keyframes ellipsis {
  0% { content: "loading"; }
  25% { content: "loading."; }
  50% { content: "loading.."; }
  75% { content: "loading..."; }
  100% { content: "loading...."; } /* Adicionei extra para o '... ...' feel */
}
</style>
