<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from "vue";
import { useLoading } from "@/Composables/useLoading";
import L from "leaflet";
import "leaflet/dist/leaflet.css";
import "leaflet.vectorgrid";
import "leaflet-draw/dist/leaflet.draw.css";
import "leaflet-measure/dist/leaflet-measure.css";
import "leaflet-draw";
import "leaflet-measure";
import axios from "axios";

// receber dados do empreendimento
const props = defineProps({
  campanha: Object,
  campanhaId: [Number, String],
  produto: String,
  emp_coordenadas: [Object, String],
});
console.log("MapViewer Props:", props.emp_coordenadas); // Log para verificar os dados recebidos


const map = ref(null);
const mapContainerRef = ref(null);
const empCoordinatesLayer = ref(null);
const empStartMarker = ref(null);
const empEndMarker = ref(null);
const drawnItems = ref(null);
const layers = ref([]);
const showLayersPanel = ref(true);
const activeLayers = ref({});
const layerColors = ref({});
const isFullscreen = ref(false);

const isLoadingMap = ref(false);
const loadingMapMessage = ref('');
const loadingTilesCount = ref(0);
const loadingLayerName = ref('');
const { start: startLoading, stop: stopLoading } = useLoading();

const isLoadingTiles = computed(() => loadingTilesCount.value > 0);

function handleDrawCreated(event) {
  if (!drawnItems.value) return;
  drawnItems.value.addLayer(event.layer);
}

function initTemporaryDrawTools() {
  if (!map.value) return;

  drawnItems.value = new L.FeatureGroup();
  map.value.addLayer(drawnItems.value);

  const drawControl = new L.Control.Draw({
    position: "topleft",
    draw: {
      polyline: false,
      polygon: false,
      rectangle: false,
      circle: true,
      marker: false,
      circlemarker: false,
    },
    edit: {
      featureGroup: drawnItems.value,
      remove: true,
    },
  });

  map.value.addControl(drawControl);
  map.value.on(L.Draw.Event.CREATED, handleDrawCreated);
}

function clearTemporaryDrawings() {
  if (!drawnItems.value) return;
  drawnItems.value.clearLayers();
}

// Paleta de cores para as camadas
const colorPalette = [
  '#FF6B6B', '#4ECDC4', '#45B7D1', '#FFA07A', '#98D8C8',
  '#F7DC6F', '#BB8FCE', '#85C1E2', '#F8B88B', '#52D273',
  '#FF6F9F', '#00D2FC', '#FFD700', '#FF1493', '#00CED1'
];

// Mapa de nomes amigáveis por tipo
const nomesTipos = {
  geologico: 'Mapa Geológico',
  geomorfologico: 'Mapa Geomorfológico',
  hipsometrico: 'Mapa Hipsométrico',
  declividades: 'Mapa de Declividades',
  hidrografico: 'Mapa Hidrográfico',
  cavidades: 'Cavidades CECAV/SBE',
  limites_areas: 'Limites de Áreas',
  potencial_inicial: 'Pot. Espeleológico - Inicial',
  potencial_reclassificado: 'Pot. Espeleológico - Reclassificado',
  projeto_engenharia: 'Projeto de Engenharia',
  estudos_posteriores: 'Estudos Posteriores',
};

// Retorna label numerado quando há múltiplas layers do mesmo tipo
function layerLabel(layer) {
  const base = nomesTipos[layer.tipo] || layer.title || layer.layer_name.replace(/_/g, ' ');
  if (!layer.tipo) return base;
  const sameType = layers.value.filter(l => l.tipo === layer.tipo);
  if (sameType.length <= 1) return base;
  const idx = sameType.findIndex(l => l.id === layer.id);
  return `${base} ${idx + 1}`;
}

// Função para gerar cor baseada no índice da camada
function getLayerColor(index) {
  return colorPalette[index % colorPalette.length];
}

function normalizeEmpCoordinates(rawCoordinates) {
  if (!rawCoordinates) return null;

  let parsedCoordinates = rawCoordinates;
  if (typeof parsedCoordinates === "string") {
    try {
      parsedCoordinates = JSON.parse(parsedCoordinates);
    } catch (error) {
      console.error("emp_coordenadas invalido:", error);
      return null;
    }
  }

  if (!parsedCoordinates || typeof parsedCoordinates !== "object") return null;

  if (parsedCoordinates.type === "Feature") {
    return parsedCoordinates;
  }

  if (parsedCoordinates.type === "FeatureCollection") {
    return parsedCoordinates;
  }

  if (parsedCoordinates.type && parsedCoordinates.coordinates) {
    return {
      type: "Feature",
      properties: {},
      geometry: parsedCoordinates,
    };
  }

  return null;
}

function addEmpCoordinatesLayer() {
  if (!map.value) return;

  if (empCoordinatesLayer.value) {
    map.value.removeLayer(empCoordinatesLayer.value);
    empCoordinatesLayer.value = null;
  }

  if (empStartMarker.value) {
    map.value.removeLayer(empStartMarker.value);
    empStartMarker.value = null;
  }

  if (empEndMarker.value) {
    map.value.removeLayer(empEndMarker.value);
    empEndMarker.value = null;
  }

  const geojson = normalizeEmpCoordinates(props.emp_coordenadas);
  if (!geojson) return;

  empCoordinatesLayer.value = L.geoJSON(geojson, {
    style: {
      color: "#E65100",
      weight: 4,
      opacity: 0.9,
    },
    pointToLayer: (_, latlng) =>
      L.circleMarker(latlng, {
        radius: 6,
        color: "#E65100",
        weight: 2,
        fillColor: "#FF9800",
        fillOpacity: 0.85,
      }),
  }).addTo(map.value);

  const bounds = empCoordinatesLayer.value.getBounds();

  // Marca visualmente os pontos inicial e final para orientar o usuario.
  const rawMapLayers = empCoordinatesLayer.value.getLayers();
  const collectedLatLngs = [];
  rawMapLayers.forEach((layer) => {
    if (typeof layer.getLatLngs === "function") {
      const latLngs = layer.getLatLngs();
      if (Array.isArray(latLngs)) {
        const flattened = latLngs.flat(Infinity);
        flattened.forEach((item) => {
          if (item && typeof item.lat === "number" && typeof item.lng === "number") {
            collectedLatLngs.push(item);
          }
        });
      }
      return;
    }

    if (typeof layer.getLatLng === "function") {
      const point = layer.getLatLng();
      if (point && typeof point.lat === "number" && typeof point.lng === "number") {
        collectedLatLngs.push(point);
      }
    }
  });

  const firstLatLng = collectedLatLngs[0] || null;
  const lastLatLng = collectedLatLngs[collectedLatLngs.length - 1] || null;

  if (firstLatLng) {
    empStartMarker.value = L.marker(firstLatLng)
      .addTo(map.value)
      .bindPopup("Ponto inicial do trecho")
      .openPopup();
  }

  if (lastLatLng) {
    empEndMarker.value = L.marker(lastLatLng)
      .addTo(map.value)
      .bindPopup("Ponto final do trecho");
  }

  if (bounds.isValid()) {
    map.value.fitBounds(bounds, {
      padding: [10, 10],
      maxZoom: 18,
    });

    // Dá um passo extra de zoom para focar melhor no trecho ao abrir.
    const nextZoom = Math.min(map.value.getZoom() + 1, 18);
    map.value.setZoom(nextZoom);
  }
}

async function toggleFullscreen() {
  if (!mapContainerRef.value) return;

  try {
    if (!document.fullscreenElement) {
      await mapContainerRef.value.requestFullscreen();
    } else {
      await document.exitFullscreen();
    }
  } catch (error) {
    console.error("Nao foi possivel alternar tela cheia:", error);
  }
}

function handleFullscreenChange() {
  isFullscreen.value = document.fullscreenElement === mapContainerRef.value;

  // Leaflet precisa recalcular dimensoes apos troca de layout.
  setTimeout(() => {
    if (map.value) map.value.invalidateSize();
  }, 200);
}

onMounted(async () => {
  map.value = L.map("map").setView([-14.235, -51.925], 4);

  L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
    attribution: "&copy; OpenStreetMap contributors",
  }).addTo(map.value);

  // Os eventos de tile são rastreados por camada individualmente em toggleLayer

  addEmpCoordinatesLayer();
  initTemporaryDrawTools();

  const params = {};
  if (props.campanhaId) {
    params.campanha_id = props.campanhaId;
  }

  isLoadingMap.value = true;
  loadingMapMessage.value = 'Carregando camadas...';
  startLoading();
  try {
    const { data } = await axios.get("/sgc/contratada/espeleologia/layers", { params });
    layers.value = data;

    // Armazenar cores para cada camada
    data.forEach((layer, index) => {
      const key = `${layer.workspace}:${layer.layer_name}`;
      layerColors.value[key] = getLayerColor(index);
    });
  } finally {
    isLoadingMap.value = false;
    loadingMapMessage.value = '';
    stopLoading();
  }

  map.value.on("click", getFeatureInfo);
  document.addEventListener("fullscreenchange", handleFullscreenChange);
});

onUnmounted(() => {
  document.removeEventListener("fullscreenchange", handleFullscreenChange);
  if (map.value) {
    map.value.off(L.Draw.Event.CREATED, handleDrawCreated);
  }
});

watch(
  () => props.emp_coordenadas,
  () => {
    addEmpCoordinatesLayer();
  },
  { deep: true }
);

function toggleLayer(layer) {
  const key = `${layer.workspace}:${layer.layer_name}`;
  if (activeLayers.value[key]) {
    // Remover camada: decrementar contador se ainda estava carregando
    const oldLayer = activeLayers.value[key];
    if (oldLayer._sgcLoading) {
      loadingTilesCount.value = Math.max(0, loadingTilesCount.value - 1);
      if (loadingTilesCount.value === 0) {
        loadingLayerName.value = '';
        stopLoading();
      }
    }
    map.value.removeLayer(oldLayer);
    delete activeLayers.value[key];
    return;
  }

  const wmsLayer = L.tileLayer.wms("/sgc/contratada/mapa/wms", {
    layers: `${layer.workspace}:${layer.layer_name}`,
    format: "image/png",
    transparent: true,
    version: "1.1.1",
    srs: "EPSG:3857",
    tiled: true,
    opacity: 0.7,
  });

  // Rastrear carregamento de tiles desta camada WMS
  wmsLayer._sgcLoading = true;
  loadingTilesCount.value++;
  loadingLayerName.value = layer.title || layer.layer_name || 'camada';
  startLoading();

  wmsLayer.on('load', () => {
    if (wmsLayer._sgcLoading) {
      wmsLayer._sgcLoading = false;
      loadingTilesCount.value = Math.max(0, loadingTilesCount.value - 1);
      if (loadingTilesCount.value === 0) {
        loadingLayerName.value = '';
        stopLoading();
      }
    }
  });

  wmsLayer.on('tileerror', () => {
    if (wmsLayer._sgcLoading) {
      wmsLayer._sgcLoading = false;
      loadingTilesCount.value = Math.max(0, loadingTilesCount.value - 1);
      if (loadingTilesCount.value === 0) {
        loadingLayerName.value = '';
        stopLoading();
      }
    }
  });

  wmsLayer.addTo(map.value);
  activeLayers.value[key] = wmsLayer;
}

function toggleLayersPanel() {
  showLayersPanel.value = !showLayersPanel.value;
  setTimeout(() => {
    if (map.value) map.value.invalidateSize();
  }, 300);
}

async function getFeatureInfo(e) {
  if (Object.keys(activeLayers.value).length === 0) return;

  isLoadingMap.value = true;
  loadingMapMessage.value = 'Consultando feições...';
  startLoading();

  const size = map.value.getSize();
  const point = map.value.latLngToContainerPoint(e.latlng);

  // Projetar os bounds para EPSG:3857 (metros)
  const geoBounds = map.value.getBounds();
  const crs = map.value.options.crs; // CRS padrão é EPSG3857
  const projectedSW = crs.project(geoBounds.getSouthWest());
  const projectedNE = crs.project(geoBounds.getNorthEast());
  const bbox = `${projectedSW.x},${projectedSW.y},${projectedNE.x},${projectedNE.y}`;

  let popupContent = "";
  let featureCounter = 0;

  const baseParams = {
    REQUEST: "GetFeatureInfo",
    VERSION: "1.1.1",
    FORMAT: "image/png",
    TRANSPARENT: true,
    INFO_FORMAT: "text/html", // HTML para matching com o viewer do GeoServer
    FEATURE_COUNT: 50,
    WIDTH: size.x,
    HEIGHT: size.y,
    SRS: "EPSG:3857",
    BBOX: bbox,
    X: Math.round(point.x),
    Y: Math.round(point.y),
    BUFFER: 20, // Tolerância em pixels
  };

  for (const key in activeLayers.value) {
    const params = { ...baseParams, LAYERS: key, QUERY_LAYERS: key };
    const url =
      "/sgc/contratada/mapa/wms?" + new URLSearchParams(params).toString();
    console.log("GetFeatureInfo URL:", url);

    try {
      const response = await axios.get(url, { responseType: "text" }); // Garantir texto puro para HTML
      let data = response.data;
      console.log("Raw Response:", data); // Log completo para debug

      // Corrige encoding: substitui caracteres estranhos comuns (ex: â -> ã, etc.), mas ideal é configurar GeoServer para UTF-8
      data = data
        .replace(/Ã/g, "Ã") // Ajuste para caracteres comuns em PT-BR; teste e adicione mais se necessário
        .replace(/â/g, "â")
        .replace(/õ/g, "õ")
        .replace(/ç/g, "ç")
        .replace(/á/g, "á")
        .replace(/é/g, "é")
        .replace(/í/g, "í")
        .replace(/ó/g, "ó")
        .replace(/ú/g, "ú")
        .replace(/Â/g, "Â")
        .replace(/Ê/g, "Ê")
        .replace(/Ô/g, "Ô"); // Isso é paliativo; verifique o encoding no GeoServer/DB

      if (typeof data === "string" && data.includes("<table")) {
        // HTML do GeoServer: injeta diretamente (já é uma tabela bonita)
        popupContent += `<h3>${key.replace(":", " - ")}</h3>`;
        popupContent += `<div class="feature-container" style=" overflow-x: scroll !important;
            overflow-y: scroll !important;
            max-width: 400px !important;
            max-height: 300px !important;">`;
        // popupContent += `<input type="text" class="filter-input" placeholder="Filtrar atributos..." data-table-class="featureInfo">`;  // Usa a classe original do GeoServer
        popupContent += data.replace(
          /<table class="featureInfo"/g,
          '<table class="info-table featureInfo"'
        ); // Mantém classe para filtro
        popupContent += `</div>`;
      } else if (data.features && data.features.length > 0) {
        // Fallback se JSON (caso mude de volta)
        popupContent += `<h3>${key.replace(":", " - ")}</h3>`;
        data.features.forEach((feature, index) => {
          featureCounter++;
          const tableId = `info-table-${Date.now()}-${featureCounter}`;
          popupContent += `<div class="feature-container" style=" overflow-x: scroll !important;
            overflow-y: scroll !important;
            max-width: 400px !important;
            max-height: 300px !important;">`;
          popupContent += `<h4>Feature ${index + 1}</h4>`;
          popupContent += `<input type="text" class="filter-input" placeholder="Filtrar atributos..." data-table-id="${tableId}">`;
          popupContent += `<table id="${tableId}" class="info-table">`;
          popupContent +=
            "<thead><tr><th>Atributo</th><th>Valor</th></tr></thead><tbody>";
          for (const prop in feature.properties) {
            const value = feature.properties[prop] || "N/A";
            popupContent += `<tr><td>${prop}</td><td>${value}</td></tr>`;
          }
          popupContent += "</tbody></table></div>";
        });
      } else {
        popupContent += `<p>Nenhuma feature na camada ${key} (verifique zoom/clique).</p>`;
      }
    } catch (error) {
      console.error(`Erro GetFeatureInfo ${key}:`, error);
      popupContent += `<p style="color:red">Erro na camada ${key}: ${error.message}</p>`;
    }
  }

  // Adiciona meta charset ao popup para forçar UTF-8
  popupContent = `<meta charset="UTF-8">${popupContent}`;

  isLoadingMap.value = false;
  loadingMapMessage.value = '';
  stopLoading();

  if (popupContent) {
    const popup = L.popup({ maxWidth: 450, className: "custom-popup" })
      .setLatLng(e.latlng)
      .setContent(popupContent)
      .openOn(map.value);

    setTimeout(() => {
      const popupEl = document.querySelector(".leaflet-popup-content");
      if (popupEl) {
        const inputs = popupEl.querySelectorAll(".filter-input");
        inputs.forEach((input) => {
          input.addEventListener("input", (ev) => {
            const filter = ev.target.value.toLowerCase();
            const tableId = ev.target.dataset.tableId;
            const tableClass = ev.target.dataset.tableClass;
            let rows = [];
            if (tableId) {
              const table = document.getElementById(tableId);
              if (table) rows = Array.from(table.querySelectorAll("tbody tr"));
            } else if (tableClass) {
              const tables = popupEl.querySelectorAll(`.${tableClass}`);
              tables.forEach((t) => {
                // Para GeoServer HTML: rows são tr após o header (th)
                const tableRows = t.querySelectorAll("tr");
                for (let i = 1; i < tableRows.length; i++) {
                  // Pula a primeira tr (header)
                  rows.push(tableRows[i]);
                }
              });
            }
            rows.forEach((row) => {
              const text = row.textContent.toLowerCase();
              row.style.display = text.includes(filter) ? "" : "none";
            });
          });
        });
      }
    }, 100);
  } else {
    isLoadingMap.value = false;
    loadingMapMessage.value = '';
    stopLoading();
    L.popup()
      .setLatLng(e.latlng)
      .setContent(
        '<p style="color:#666">Nenhuma feature encontrada.<br>Tente zoom maior ou BUFFER mais alto.</p>'
      )
      .openOn(map.value);
  }
}
</script>
<!-- O restante do <template> e <style> permanece o mesmo do código anterior. -->

<!-- Template e Style permanecem iguais ao anterior -->
<template>
  <div class="overflow-hidden">
    <!-- Sidebar de camadas -->
    <div class="w-72 p-4 border-r bg-gray-50 overflow-y-auto card">
      <div
        ref="mapContainerRef"
        class="map-container"
        :class="{ 'is-fullscreen': isFullscreen }"
      >
        <!-- Mapa -->
        <div id="map"></div>

        <!-- Overlay de carregamento (fetch inicial e GetFeatureInfo) -->
        <transition name="map-fade">
          <div v-if="isLoadingMap" class="map-loading-overlay">
            <div class="map-loading-box">
              <div class="spinner-border text-primary" role="status" style="width:2rem;height:2rem;"></div>
              <span class="mt-2 fw-semibold">{{ loadingMapMessage }}</span>
            </div>
          </div>
        </transition>

        <!-- Overlay de carregamento de tiles de camada WMS (não bloqueia interação) -->
        <transition name="map-fade">
          <div v-if="isLoadingTiles && !isLoadingMap" class="map-tiles-overlay">
            <div class="map-tiles-box">
              <div class="spinner-border text-primary" role="status" style="width:1.8rem;height:1.8rem;"></div>
              <div class="mt-2">
                <div class="fw-semibold" style="font-size:0.88rem;">Carregando camada...</div>
                <div v-if="loadingLayerName" class="text-muted" style="font-size:0.8rem;">{{ loadingLayerName }}</div>
              </div>
            </div>
          </div>
        </transition>

        <button
          class="fullscreen-btn"
          @click="toggleFullscreen"
          :title="isFullscreen ? 'Sair da tela cheia' : 'Tela cheia'"
        >
          {{ isFullscreen ? "Sair da Tela Cheia" : "Tela Cheia" }}
        </button>

        <button
          class="clear-drawings-btn"
          @click="clearTemporaryDrawings"
          title="Limpar desenhos temporarios"
        >
          Limpar Desenhos
        </button>

        <!-- Painel de controle de camadas -->
        <div class="layers-panel" :class="{ hidden: !showLayersPanel }">
          <div class="panel-header">
            <h2>Camadas do Mapa</h2>
            <button
              class="close-btn"
              @click="toggleLayersPanel"
              title="Fechar painel"
            >
              <span>&times;</span>
            </button>
          </div>

          <div class="panel-body">
            <div v-if="layers.length === 0" class="no-layers">
              <p>Nenhuma camada disponível no momento</p>
            </div>

            <div v-else class="layers-list">
              <div
                v-for="layer in layers"
                :key="layer.id"
                class="layer-item"
                :class="{
                  active:
                    !!activeLayers[`${layer.workspace}:${layer.layer_name}`],
                }"
              >
                <input
                  type="checkbox"
                  :id="'layer-' + layer.id"
                  :checked="
                    !!activeLayers[`${layer.workspace}:${layer.layer_name}`]
                  "
                  @change="toggleLayer(layer)"
                />
                <label :for="'layer-' + layer.id">
                  <span
                    class="layer-icon"
                    :style="{ backgroundColor: layerColors[`${layer.workspace}:${layer.layer_name}`] }"
                  ></span>
                  <span class="layer-name">{{ layerLabel(layer) }}</span>
                </label>
              </div>
            </div>
          </div>

          <div class="panel-footer">
            <!-- <button class="btn-toggle-all" @click="toggleAllLayers">
          {{ allLayersActive ? 'Desativar todas' : 'Ativar todas' }}
        </button> -->
          </div>
        </div>

        <!-- Botão para mostrar/esconder o painel -->
        <button
          class="toggle-panel-btn"
          :class="{ expanded: showLayersPanel }"
          @click="toggleLayersPanel"
        >
          <div class="panel-icon">
            <div class="bar"></div>
            <div class="bar"></div>
            <div class="bar"></div>
          </div>
          <span class="btn-text">{{
            showLayersPanel ? "Ocultar Camadas" : "Mostrar Camadas"
          }}</span>
        </button>

        <!-- Legenda informativa -->
      </div>
    </div>

    <!-- Mapa -->
  </div>
</template>

<style scoped>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

.map-container {
  position: relative;
  width: 100%;
  height: 60vh;
  min-height: 420px;
  font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
}

#map {
  width: 100%;
  height: 100%;
  z-index: 1;
}

.map-container:fullscreen,
.map-container.is-fullscreen {
  width: 100vw;
  height: 100vh;
  max-width: 100vw;
  max-height: 100vh;
  background: #fff;
}

.fullscreen-btn {
  position: absolute;
  top: 16px;
  left: 56px;
  z-index: 1002;
  background: rgba(38, 0, 255, 0.95);
  border: 1px solid #d1d5db;
  color: #ffffff;
  padding: 8px 12px;
  border-radius: 8px;
  font-size: 0.85rem;
  font-weight: 600;
  cursor: pointer;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
  transition: all 0.2s ease;
}

.fullscreen-btn:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.16);
}

.clear-drawings-btn {
  position: absolute;
  top: 58px;
  left: 56px;
  z-index: 1002;
  background: rgba(255, 255, 255, 0.95);
  border: 1px solid #d1d5db;
  color: #1f2937;
  padding: 7px 10px;
  border-radius: 8px;
  font-size: 0.8rem;
  font-weight: 600;
  cursor: pointer;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
  transition: all 0.2s ease;
}

.clear-drawings-btn:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.16);
}

/* Painel de camadas */
.layers-panel {
  position: absolute;
  top: 20px;
  right: 20px;
  width: 320px;
  background: rgba(255, 255, 255, 0.3);
  border-radius: 12px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.18);
  z-index: 1000;
  display: flex;
  flex-direction: column;
  transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
  overflow: hidden;
  border: 1px solid rgba(0, 0, 0, 0.08);
}

.layers-panel.hidden {
  transform: translateX(-100%);
  opacity: 0;
  pointer-events: none;
}

.panel-header {
  padding: 16px 20px;
  background: linear-gradient(135deg, #1e5799 0%, #207cca 51%, #2989d8 100%);
  color: white;
  display: flex;
  justify-content: space-between;
  align-items: center;
  border-bottom: 1px solid rgba(255, 255, 255, 0.15);
}

.panel-header h2 {
  font-size: 1.4rem;
  font-weight: 600;
  letter-spacing: -0.5px;
}

.close-btn {
  background: rgba(255, 255, 255, 0.2);
  border: none;
  width: 32px;
  height: 32px;
  border-radius: 50%;
  color: white;
  font-size: 1.5rem;
  line-height: 1;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s;
}

.close-btn:hover {
  background: rgba(255, 255, 255, 0.35);
  transform: rotate(90deg);
}

.panel-body {
  padding: 15px;
  max-height: calc(100vh - 200px);
  overflow-y: auto;
}

.no-layers {
  padding: 20px;
  text-align: center;
  color: #777;
  font-style: italic;
}

.layers-list {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.layer-item {
  background: rgba(255, 255, 255, 0.636);
  border-radius: 8px;
  padding: 10px 15px;
  display: flex;
  align-items: center;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
  transition: all 0.2s;
  border-left: 4px solid #4a90e2;
}

.layer-item:hover {
  transform: translateX(3px);
  box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1);
}

.layer-item.active {
  background: #eef5ff;
  border-left: 4px solid #2196f3;
  box-shadow: 0 2px 10px rgba(33, 150, 243, 0.15);
}

.layer-item input[type="checkbox"] {
  width: 18px;
  height: 18px;
  margin-right: 12px;
  cursor: pointer;
  accent-color: #2196f3;
  border-radius: 4px;
  transition: all 0.2s;
}

.layer-item input[type="checkbox"]:hover {
  transform: scale(1.15);
}

.layer-item label {
  display: flex;
  align-items: center;
  cursor: pointer;
  width: 100%;
}

.layer-icon {
  display: inline-block;
  width: 16px;
  height: 16px;
  background: linear-gradient(135deg, #4a90e2, #2196f3);
  border-radius: 4px;
  margin-right: 10px;
  flex-shrink: 0;
  border: 1px solid rgba(0, 0, 0, 0.2);
  transition: all 0.2s;
}

.layer-item.active .layer-icon {
  box-shadow: 0 0 0 2px rgba(255, 255, 255, 0.8), 0 0 0 3px currentColor;
  transform: scale(1.1);
}

.layer-name {
  font-weight: 500;
  color: #333;
  font-size: 0.95rem;
  word-break: break-word;
}

.panel-footer {
  padding: 12px 20px;
  border-top: 1px solid #eee;
  background: #f9fbfd;
}

.btn-toggle-all {
  width: 100%;
  padding: 8px 15px;
  background: linear-gradient(135deg, #4a90e2, #2196f3);
  color: white;
  border: none;
  border-radius: 8px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s;
  box-shadow: 0 2px 6px rgba(33, 150, 243, 0.3);
}

.btn-toggle-all:hover {
  background: linear-gradient(135deg, #3a80d2, #1976d2);
  transform: translateY(-1px);
  box-shadow: 0 4px 8px rgba(33, 150, 243, 0.4);
}

.btn-toggle-all:active {
  transform: translateY(0);
}

/* Botão de toggle do painel */
.toggle-panel-btn {
  position: absolute;
  bottom: 10px;
  left: 30px;
  z-index: 1001;
  background: white;
  border: 1px solid #ddd;
  border-radius: 30px;
  padding: 8px 18px;
  display: flex;
  align-items: center;
  gap: 10px;
  cursor: pointer;
  box-shadow: 0 3px 12px rgba(0, 0, 0, 0.15);
  transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
  font-weight: 500;
  color: #2c3e50;
  height: 42px;
  overflow: hidden;
}

.toggle-panel-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
  background: #f8f9ff;
}

.toggle-panel-btn.expanded {
  width: 170px;
  border-radius: 30px 8px 8px 30px;
  padding-left: 15px;
}

.toggle-panel-btn:not(.expanded) {
  width: 42px;
  padding: 0;
  border-radius: 50%;
}

.panel-icon {
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  width: 18px;
  height: 14px;
}

.bar {
  width: 100%;
  height: 2px;
  background: #2196f3;
  border-radius: 2px;
  transition: all 0.3s ease;
}

.toggle-panel-btn.expanded .bar:nth-child(1) {
  transform: rotate(45deg) translate(5px, 5px);
}

.toggle-panel-btn.expanded .bar:nth-child(2) {
  opacity: 0;
}

.toggle-panel-btn.expanded .bar:nth-child(3) {
  transform: rotate(-45deg) translate(5px, -5px);
}

.btn-text {
  white-space: nowrap;
  transition: opacity 0.3s;
  opacity: 1;
}

.toggle-panel-btn:not(.expanded) .btn-text {
  opacity: 0;
  width: 0;
  padding: 0;
  margin: 0;
}

/* Legenda do mapa */
.map-legend {
  position: absolute;
  bottom: 30px;
  right: 30px;
  background: rgba(255, 255, 255, 0.92);
  border-radius: 10px;
  padding: 12px 18px;
  box-shadow: 0 3px 10px rgba(0, 0, 0, 0.12);
  z-index: 900;
  display: flex;
  gap: 15px;
  border: 1px solid #eee;
}

.legend-item {
  display: flex;
  align-items: center;
  font-size: 0.85rem;
  color: #444;
}

.legend-color {
  display: inline-block;
  width: 20px;
  height: 20px;
  border-radius: 4px;
  margin-right: 6px;
}

.legend-color.caves {
  background: linear-gradient(135deg, #8d6e63, #5d4037);
}

.legend-color.rivers {
  background: linear-gradient(135deg, #4fc3f7, #0288d1);
}

.legend-color.preservation {
  background: linear-gradient(135deg, #81c784, #388e3c);
}

/* === Loading overlay sobre o mapa === */
.map-loading-overlay {
  position: absolute;
  inset: 0;
  background: rgba(255, 255, 255, 0.7);
  z-index: 2000;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 4px;
}

.map-loading-box {
  background: white;
  border-radius: 10px;
  padding: 20px 28px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.18);
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
  font-size: 0.95rem;
  color: #333;
}

/* Overlay de carregamento de tiles (visível mas não bloqueia interação) */
.map-tiles-overlay {
  position: absolute;
  inset: 0;
  z-index: 1500;
  pointer-events: none;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(255, 255, 255, 0.45);
}

.map-tiles-box {
  background: white;
  border-radius: 10px;
  padding: 16px 24px;
  box-shadow: 0 4px 18px rgba(0, 0, 0, 0.16);
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 4px;
  text-align: center;
}

/* Transição suave do overlay */
.map-fade-enter-active,
.map-fade-leave-active {
  transition: opacity 0.25s ease;
}
.map-fade-enter-from,
.map-fade-leave-to {
  opacity: 0;
}

/* Scroll suave para o painel */
.panel-body::-webkit-scrollbar {
  width: 6px;
}

.panel-body::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 10px;
}

.panel-body::-webkit-scrollbar-thumb {
  background: #c1c1c1;
  border-radius: 10px;
}

.panel-body::-webkit-scrollbar-thumb:hover {
  background: #a8a8a8;
}

/* Animações de entrada */
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.layer-item {
  animation: fadeIn 0.3s ease-out forwards;
}

.layer-item:nth-child(1) {
  animation-delay: 0.05s;
}
.layer-item:nth-child(2) {
  animation-delay: 0.1s;
}
.layer-item:nth-child(3) {
  animation-delay: 0.15s;
}
.layer-item:nth-child(4) {
  animation-delay: 0.2s;
}
.layer-item:nth-child(5) {
  animation-delay: 0.25s;
}
.layer-item:nth-child(6) {
  animation-delay: 0.3s;
}
.layer-item:nth-child(7) {
  animation-delay: 0.35s;
}
.layer-item:nth-child(8) {
  animation-delay: 0.4s;
}

/* Responsividade */
@media (max-width: 768px) {
  .map-container {
    min-height: 360px;
  }

  .layers-panel {
    width: 280px;
    left: 15px;
    top: 15px;
  }

  .fullscreen-btn {
    top: 12px;
    left: 12px;
    padding: 7px 10px;
    font-size: 0.8rem;
  }

  .clear-drawings-btn {
    top: 52px;
    left: 50px;
    padding: 6px 9px;
    font-size: 0.75rem;
  }

  .toggle-panel-btn {
    bottom: 20px;
    left: 20px;
  }

  .map-legend {
    flex-direction: column;
    align-items: flex-start;
    gap: 8px;
    padding: 10px;
  }

  .toggle-panel-btn.expanded {
    width: 150px;
  }
}

@media (max-width: 480px) {
  .map-container {
    min-height: 300px;
  }

  .layers-panel {
    width: 92%;
    left: 4%;
    top: 15px;
  }

  .fullscreen-btn {
    top: 10px;
    left: 10px;
    padding: 6px 9px;
    font-size: 0.75rem;
  }

  .clear-drawings-btn {
    top: 47px;
    left: 45px;
    padding: 6px 8px;
    font-size: 0.72rem;
  }

  .toggle-panel-btn {
    width: 40px;
    height: 40px;
    padding: 0;
    border-radius: 50%;
  }

  .toggle-panel-btn.expanded {
    width: 40px;
    border-radius: 50%;
    padding: 0;
  }

  .btn-text {
    display: none;
  }

  .map-legend {
    bottom: 20px;
    right: 20px;
    padding: 8px 12px;
  }
}

/* Estilos para o popup customizado (infowindow) */
.custom-popup .leaflet-popup-content-wrapper {
  background: #ffffff;
  border-radius: 8px;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
  padding: 15px;
  font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
}

.custom-popup .leaflet-popup-content {
  max-height: 300px;
  overflow-y: auto;
}

.custom-popup h3 {
  font-size: 1.2rem;
  color: #2196f3;
  margin-bottom: 10px;
  border-bottom: 1px solid #eee;
  padding-bottom: 5px;
}

.custom-popup h4 {
  font-size: 1rem;
  color: #333;
  margin: 15px 0 5px;
}

.custom-popup .feature-container {
  margin-bottom: 20px;
  overflow-x: scroll !important;
  overflow-y: scroll !important;
  max-width: 400px !important;
  max-height: 300px !important;
}

.custom-popup .filter-input {
  width: 100%;
  padding: 8px 12px;
  margin-bottom: 10px;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 0.9rem;
  box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.05);
  transition: border-color 0.2s;
}

.custom-popup .filter-input:focus {
  border-color: #2196f3;
  outline: none;
  box-shadow: 0 0 0 2px rgba(33, 150, 243, 0.2);
}

.custom-popup .info-table {
  width: 50%;
  border-collapse: collapse;
  margin-bottom: 15px;
}

.custom-popup .info-table th,
.custom-popup .info-table td {
  padding: 8px 12px;
  border: 1px solid #ddd;
  text-align: left;
  font-size: 0.9rem;
}

.custom-popup .info-table th {
  background: #f5f5f5;
  font-weight: 600;
  color: #555;
}

.custom-popup .info-table td {
  background: #ffffff;
  color: #333;
}

.custom-popup .info-table tr:nth-child(even) td {
  background: #fafafa;
}

.custom-popup .leaflet-popup-tip {
  border-top-color: #ffffff;
}

.custom-popup::-webkit-scrollbar {
  width: 6px;
}

.custom-popup::-webkit-scrollbar-track {
  background: #f1f1f1;
}

.custom-popup::-webkit-scrollbar-thumb {
  background: #c1c1c1;
  border-radius: 10px;
}
/* Estilos para o popup customizado (infowindow) */
.custom-popup .leaflet-popup-content-wrapper {
  background: #ffffff;
  border-radius: 8px;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
  padding: 15px;
  font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
}

.custom-popup .leaflet-popup-content {
  max-height: 400px; /* Aumentado para mais espaço, com scroll */
  overflow-y: auto;
  width: 100%;
  max-width: 400px; /* Limite de largura para evitar estourar horizontalmente */
  word-wrap: break-word; /* Evita texto longo sem quebra */
}

.custom-popup h3 {
  font-size: 1.2rem;
  color: #2196f3;
  margin-bottom: 10px;
  border-bottom: 1px solid #eee;
  padding-bottom: 5px;
}

.custom-popup h4 {
  font-size: 1rem;
  color: #333;
  margin: 15px 0 5px;
}

.custom-popup .feature-container {
  margin-bottom: 20px;
  overflow-x: scroll !important;
  overflow-y: scroll !important;
  max-width: 400px !important;
  max-height: 300px !important;
}

.custom-popup .filter-input {
  width: 100%;
  padding: 8px 12px;
  margin-bottom: 10px;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 0.9rem;
  box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.05);
  transition: border-color 0.2s;
}

.custom-popup .filter-input:focus {
  border-color: #2196f3;
  outline: none;
  box-shadow: 0 0 0 2px rgba(33, 150, 243, 0.2);
}

.custom-popup .info-table,
.custom-popup .featureInfo {
  /* Compatível com classe do GeoServer */
  width: 100%;
  border-collapse: collapse;
  margin-bottom: 15px;
}

.custom-popup .info-table th,
.custom-popup .info-table td,
.custom-popup .featureInfo th,
.custom-popup .featureInfo td {
  padding: 8px 12px;
  border: 1px solid #ddd;
  text-align: left;
  font-size: 0.9rem;
}

.custom-popup .info-table th,
.custom-popup .featureInfo th {
  background: #f5f5f5;
  font-weight: 600;
  color: #555;
}

.custom-popup .info-table td,
.custom-popup .featureInfo td {
  background: #ffffff;
  color: #333;
}

.custom-popup .info-table tr:nth-child(even) td,
.custom-popup .featureInfo tr:nth-child(even) td {
  background: #fafafa;
}

.custom-popup .leaflet-popup-tip {
  border-top-color: #ffffff;
}

.custom-popup::-webkit-scrollbar {
  width: 6px;
}

.custom-popup::-webkit-scrollbar-track {
  background: #f1f1f1;
}

.custom-popup::-webkit-scrollbar-thumb {
  background: #c1c1c1;
  border-radius: 10px;
}
</style>
