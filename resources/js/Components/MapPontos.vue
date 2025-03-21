<script setup>
import { onMounted } from 'vue';
import { ref, defineExpose } from 'vue';
import { useToast } from 'vue-toastification';
import 'leaflet.heat';

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
let density_layer = null;

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

const setDensity = (density_array, radius = 25, blur = 15, cleanPrevious = true) => {
  if (density_layer && cleanPrevious) {
    map.removeLayer(density_layer);
    density_layer = null;
  }

  var heat = L.heatLayer(density_array, {
    radius: radius,
    max: 1,
    blur: blur,
    gradient: {
      0.2: 'blue',
      0.4: 'green',
      0.6: 'yellow',
      1.0: 'red'
    }
  }).addTo(map);

  density_layer = heat;

  if (density_array.length > 0) {
    const bounds = L.latLngBounds(density_array.map(coord => [coord[0], coord[1]]));
    map.fitBounds(bounds);
  }
};

// Caso poupupAndEvent seja true. Linestring array vai ter [0] como coordenadas e [1] como texto do popup,
// e [2] como retorno do evento 'trechoClicado'
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

            L.popup()
              .setLatLng(e.latlng)
              .setContent(e.layer?.feature?.properties?.popup)
              .openOn(map);
          }
        });
      }

      // Itera sobre coordenadas e adiciona na layer
      linestring_array.forEach(geojson_linestring => {
        if (popupAndEvent) { // Define coordenadas e dados do evento
          let geojson = JSON.parse(geojson_linestring[0]);
          geojson.properties = geojson_linestring[2];
          geojson.properties.popup = geojson_linestring[1];
          
          console.log("geojson final:",geojson);
          geojson_layer.addData(geojson).setStyle(function (feature) {

            let style = {
              color: '#0000CD',
              weight: 5,
              opacity: 1,
              fillColor: '#9999eb',
              lineJoin: 'round'
            };

            return style
          });

        } else { // Define apenas coordenadas
          const geojson = JSON.parse(geojson_linestring);
          geojson_layer.addData(geojson).setStyle({ weight: 6 });
        }
      });

      // Zoom automático
      return zoomFitBounds();

    } catch (e) {
      if (linestring_array && linestring_array.length) {
        console.log(e);
        toast.error('GeoJSON inválido.');
      }
    }
  }

  // Retorna a visualização inicial caso pontos não sejam informados
  map.setView(
    ["-10.6007767", "-63.6037797"],
    4.5
  );
}

const zoomFitBounds = () => {
  map.fitBounds([marker_group?.getBounds(), geojson_layer?.getBounds()]);
}

const zoomToLinestring = (geojson_linestring) => {
  try {
    const geojson = JSON.parse(geojson_linestring);

    let latLngArrat = []

    if (geojson.type === 'LineString') {
      // Mapeamento é necessário pois latitude/longitude vem na ordem errada do linestring
      latLngArrat = geojson.coordinates.map(arr => {
        return [arr[1], arr[0]]
      });
    } else {
      geojson.coordinates.forEach((linha) => {
        latLngArrat.push([linha[1], linha[0]])
      })
    }

    map.fitBounds([latLngArrat]);

  } catch (e) {
    if (geojson_linestring) {
      console.log(e);
      toast.error('GeoJSON inválido.');
    }
  }

}



defineExpose({ renderMapa, setLinestrings, zoomToLinestring, zoomFitBounds, setDensity });

</script>

<template>
  <div ref="mapContainer" :style="`height: ${height};width: ${width}`"></div>
</template>
