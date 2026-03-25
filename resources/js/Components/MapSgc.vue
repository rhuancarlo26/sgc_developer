<script setup>
import { onMounted, ref, defineExpose, nextTick } from 'vue';
import { useToast } from 'vue-toastification';

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

let map = null;
let geojson_layer = null;
let marker_layer = null;
let marker_group = null;

onMounted(async () => {
  await nextTick(); // Garantir que o DOM esteja pronto antes de renderizar o mapa

  if (!props.manualRender) {
    setTimeout(() => {
    renderMapa();
  }, 500);
  }
});

// Função para obter a cor com base no valor de 'br'
const getColor = (value) => colorMapping[value] || '#000000';

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
    
    // Força o mapa a recalcular o tamanho do container
    map.invalidateSize();
  }
};

// Função para adicionar linhas e eventos ao GeoJSON
const setLinestrings = (linestring_array, popupAndEvent = false, cleanPrevious = true) => {
  // Remove Layer anterior caso exista e cleanPrevious seja true
  if (geojson_layer && cleanPrevious) {
    map.removeLayer(geojson_layer);
    geojson_layer = null;
  }

  if (linestring_array.length) {
    try {
      // Adiciona nova Layer Geojson caso não exista
      if (geojson_layer === null) {
        geojson_layer = L.geoJSON().addTo(map).on('click', function (e) {
          if (popupAndEvent) {
            const properties = e.layer.feature.properties;

            // Exibe informações da camada clicada
            const info = `
              <strong>UF:</strong> ${properties.uf || 'N/A'}<br>
              <strong>BR:</strong> ${properties.br || 'N/A'}<br>
              <strong>KM Inicial:</strong> ${properties.kmi || 'N/A'}<br>
              <strong>KM Final:</strong> ${properties.kmf || 'N/A'}
            `;
            
            L.popup()
              .setLatLng(e.latlng)
              .setContent(info || 'Sem informações')
              .openOn(map);

            // Emitir evento quando um trecho é clicado
            this.$emit('popupopen', e);
          }
        });
      }
      
      // Itera sobre coordenadas e adiciona na layer
      linestring_array.forEach(geojson_linestring => {
        let geojson = JSON.parse(geojson_linestring[0]);

        if (popupAndEvent) {
          geojson.properties = geojson_linestring[2] || {};
          geojson.properties.popup = geojson_linestring[1] || 'Sem informações';

          geojson_layer.addData(geojson).setStyle(function (feature) {
            let style = {
              weight: 5,
              opacity: 1,
              fillColor: 'white',
              color: getColor(feature.properties.br),
              lineJoin: 'round'
            };

            return style;
          });
        } else {
          geojson_layer.addData(geojson).setStyle({ weight: 6 });
        }
      });

      // Zoom automático
      zoomFitBounds();

    } catch (e) {
      if (linestring_array && linestring_array.length) {
        console.log(e);
        toast.error('GeoJSON inválido.');
      }
    }
  } else {
    // Retorna a visualização inicial caso pontos não sejam informados
    map.setView(["-10.6007767", "-63.6037797"], 4.5);
  }
};

const zoomFitBounds = () => {
  if (marker_group) {
    map.fitBounds([marker_group.getBounds(), geojson_layer.getBounds()]);
  } else if (geojson_layer) {
    map.fitBounds(geojson_layer.getBounds());
  }
};

const setGeoJson = (geojson_linestring) => {
  // Remove Layer anterior caso exista
  if (geojson_layer) {
    map.removeLayer(geojson_layer);
    geojson_layer = null;
  }

  try {
    const geojson = JSON.parse(geojson_linestring);
    
    // Adiciona nova Layer com dados do GeoJSON Parseado
    geojson_layer = L.geoJSON().addTo(map);
    geojson_layer.addData(geojson);

    zoomFitBounds();
  } catch (e) {
    if (geojson_linestring) {
      console.log(e);
      toast.error('GeoJSON inválido.');
    }
  }
};

defineExpose({ renderMapa, setLinestrings, zoomFitBounds, setGeoJson });

</script>

<template>
  <div ref="mapContainer" :style="`height: ${height}; width: ${width}`"></div>
</template>
