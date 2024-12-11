<script setup>
import { onMounted, ref, defineExpose, nextTick } from 'vue';
import { useToast } from 'vue-toastification';
import L from 'leaflet';

const toast = useToast();

const props = defineProps({
  geojson: String,
  height: { type: String, default: "100%" },
  width: { type: String, default: "100%" },
  coordinates: Array,
  manualRender: { type: Boolean, default: false },
  id: String
});

const mapContainer = ref();
let featureGroup = null;

let map = null;
let geojson_layers = []; 
let geojson_layer = null;
let marker_layer = null;
let marker_group = null;

onMounted(() => {
  if (!props.manualRender) {
    renderMapa();
  }
});

const renderMapa = () => {
  if (!map) {
    // Renderiza Mapa vazio
    map = L.map(mapContainer.value, { renderer: L.canvas() }).setView(
      ["-10.6007767", "-63.6037797"],
      4.5
    );

    // Adiciona Tiles do OpenStreetMap
    L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
      maxZoom: 18,
      attribution: '&copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a>',
      id: "mapbox.streets",
      fadeAnimation: true,
    }).addTo(map);
  }
}

const zoomFitBounds = () => {
  if (marker_group) {
    map.fitBounds([marker_group.getBounds(), geojson_layer.getBounds()]);
  } else if (geojson_layer) {
    map.fitBounds(geojson_layer.getBounds());
  }
}

const zoomToLinestring = (geojson_linestring) => {
  try {
    const geojson = JSON.parse(geojson_linestring);
    let latLngArray = [];

    if (geojson.type === 'LineString') {
      // Mapeamento é necessário pois latitude/longitude vem na ordem errada do linestring
      latLngArray = geojson.coordinates.map(arr => [arr[1], arr[0]]);
    } else {
      geojson.coordinates.forEach((linha) => {
        latLngArray.push(linha.map(coord => [coord[1], coord[0]]));
      });
    }

    map.fitBounds(latLngArray);

  } catch (e) {
    if (geojson_linestring) {
      toast.error('GeoJSON inválido.');
    }
  }
}

const getColorItem = (item) =>  {
  let color
  if (item) {
    color = '#008000';
  } else {
    color = '#dc3545';
  }
  
  return color
}

const setGeoJson = async (geojson_linestring, weight, filterOSE = null) => {
  if (!featureGroup) {
    featureGroup = L.featureGroup().addTo(map);
  }
  featureGroup.clearLayers();

  geojson_linestring.forEach(coordenada => {
    try {
      const geojson = JSON.parse(coordenada.coordenadas);

      let dnitButton = '';
      let ibamaButton = '';
      let incraButton = '';
      if (coordenada.situacao_processo_licenciamento_dnit !== null) {
        dnitButton = `<a tabindex="0" role="button" class="text-white btn btn-success p-2 mt-2 btn-custon" 
          data-bs-toggle="popover" 
          data-bs-trigger="focus"
          data-bs-title="Situação Dnit" 
          data-bs-content="${coordenada.situacao_processo_licenciamento_dnit}">
          Dnit
        </a>`;
      }

      if (coordenada.situacao_ibama_oema !== null) {
        ibamaButton = `<a tabindex="0" role="button" class="text-white btn btn-warning p-2 mt-2 btn-custon" 
          data-bs-toggle="popover" 
          data-bs-trigger="focus" 
          data-bs-title="Situação Ibama" 
          data-bs-content="${coordenada.situacao_ibama_oema}">
          Ibama
        </a>`;
      }

      if (coordenada.situacao_incra !== null) {
        incraButton = `<a tabindex="0" role="button" class="text-white btn btn-danger p-2 mt-2 btn-custon" 
          data-bs-toggle="popover" 
          data-bs-trigger="focus" 
          data-bs-title="Situação Incra" 
          data-bs-content="${coordenada.situacao_incra}">
          Incra
        </a>`;
      }

      const info = `
              <strong>BR:</strong> ${coordenada.br || 'N/A'}<br>
              <strong>UF:</strong> ${coordenada.uf || 'N/A'}<br>
              <strong>KM Inicial:</strong> ${coordenada.km_ini || 'N/A'}<br>
              <strong>KM Final:</strong> ${coordenada.km_fin || 'N/A'}<br>
              <strong>Contrato:</strong> ${coordenada.contrato_est_ambiental || 'N/A'}<br>
              <strong>FCA:</strong> ${coordenada.fca_sei || 'N/A'}<br>
              <strong>TRE:</strong> ${coordenada.tre_sei_dnit || 'N/A'}<br>
              <strong>Plano de Trabalho:</strong> ${'N/A'}<br>
              <strong>OSE:</strong> ${coordenada.ose_sei || 'N/A'}<br>
              ${dnitButton}
              ${ibamaButton}
              ${incraButton}
            `;

      const layerName = coordenada.ose_sei ? 'ose' : 's/ose';

      if (filterOSE === 'ose' && layerName !== 'ose') return;
      if (filterOSE === 's/ose' && layerName !== 's/ose') return;

      const geojson_layer = L.geoJSON(geojson, {
        style: {
          color: getColorItem(coordenada.ose_sei),
          weight: weight || 2,
          layerName: coordenada.ose_sei ? 'ose' : 's/osei',
        }
      }).bindPopup(info);

      geojson_layer.addTo(featureGroup)
      geojson_layers.push(geojson_layer);

      // Adiciona o evento de clique para a linha (GeoJSON layer)
      geojson_layer.on('click', async function () {
        // Atraso para garantir que o Vue tenha atualizado a DOM
        await nextTick();

        // Seleciona todos os botões com popover dentro do popup
        const popoverTriggerList = geojson_layer.getPopup().getElement().querySelectorAll('[data-bs-toggle="popover"]');

        // Inicializa o popover
        popoverTriggerList.forEach(popoverTriggerEl => {
          new bootstrap.Popover(popoverTriggerEl);
        });
      });

      if (featureGroup.getLayers().length > 0) {
        map.fitBounds(featureGroup.getBounds());
      }
    } catch (e) {
      console.error('GeoJSON inválido:', e);
    }
  });

  zoomFitBounds();
};


const mapHasLayer = (layerName) => {

  const layers = featureGroup.getLayers();
  const foundLayer = layers.find(l => console.log(l))

  return layerName;
}

defineExpose({ renderMapa, zoomToLinestring, zoomFitBounds, setGeoJson, mapHasLayer });

</script>

<template>
  <div ref="mapContainer" :style="`height: ${height}; width: ${width}`"></div>
</template>

<style scoped>

.btn-custon{
  font-size: 1rem !important;
}

</style>