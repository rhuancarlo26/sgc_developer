<template>
  <div ref="mapContainer" style="height: 750px; width: 100%;"></div>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import 'leaflet.vectorgrid';

const props = defineProps({
  layers: Array,
});
const layers_de_teste = ref([
  {
    layerName: 'baciasdenivel2',
    tileUrl: 'http://localhost:3000/bacias-de-nivel-2/{z}/{x}/{y}',
    geometryType: 'Polygon',
    label: 'Bacias de Nível 2',
    fillColor: '#ffff00' // Amarelo
  },
  {
    layerName: 'baciasdenivel5',
    tileUrl: 'http://localhost:3000/bacias-de-nivel-5/{z}/{x}/{y}',
    geometryType: 'Polygon',
    label: 'Bacias de Nível 5',
    fillColor: '#00ff00' // Verde
  },
  {
    layerName: 'relevov2',
    tileUrl: 'http://localhost:3000/relevo-v2/{z}/{x}/{y}',
    geometryType: 'Point',
    label: 'Relevo V2',
    fillColor: '#ff0000' // Vermelho para pontos
  },
  {
    layerName: 'abastecimentosaneamento',
    tileUrl: 'http://localhost:3000/abastecimento-saneamento/{z}/{x}/{y}',
    geometryType: 'Point',
    label: 'Abastecimento e Saneamento',
    fillColor: '#0000ff' // Azul para pontos
  },
  {
    layerName: 'cavid',
    tileUrl: 'http://localhost:3000/cavid/{z}/{x}/{y}',
    geometryType: 'Point',
    label: 'Cavidades',
    fillColor: '#0000ff' // Azul para pontos
  }
]);

const mapContainer = ref(null);

onMounted(() => {
  const map = L.map(mapContainer.value).setView([-15.793889, -47.882778], 4);

  const basemap = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap contributors'
  }).addTo(map);

  const baseLayers = { "OpenStreetMap": basemap };
  const overlays = {};

//   props.layers.forEach((layerConfig) => {
  layers_de_teste.value.forEach((layerConfig) => {
    // Função de estilo personalizada por layer, usando configs como fillColor
    const getStyleForLayer = (properties, zoom) => {
      const baseStyle = { weight: 1, opacity: 1 }; // Comum a todos

      if (layerConfig.geometryType === 'Point') {
        return {
          ...baseStyle,
          radius: 5, // Tamanho dinâmico
          fillColor: layerConfig.fillColor || '#ff0000', // Usa o personalizado ou default (vermelho)
          color: layerConfig.color || '#0f0', // Borda
          fillOpacity: layerConfig.fillOpacity || 0.8,
          fill: true
        };
      } else if (layerConfig.geometryType === 'MultiPoint') {
        return {
          ...baseStyle,
          radius: 10 + zoom / 5, // Tamanho dinâmico
          fillColor: layerConfig.fillColor || '#ff7800', // Usa o personalizado ou default
          color: layerConfig.color || '#0f0', // Borda
          fillOpacity: layerConfig.fillOpacity || 0.8
        };
      } else if (layerConfig.geometryType === 'LineString' || layerConfig.geometryType === 'MultiLineString') {
        return {
          ...baseStyle,
          color: layerConfig.color || '#0f0', // Usa o personalizado ou default (azul)
          weight: 2 + zoom / 10, // Espessura dinâmica
          fill: false // Sem preenchimento
        };
      } else if (layerConfig.geometryType === 'Polygon' || layerConfig.geometryType === 'MultiPolygon') {
        return {
          ...baseStyle,
          fill: true,
          fillColor: layerConfig.fillColor || '#00ff00', // Usa o personalizado ou default (verde)
          fillOpacity: layerConfig.fillOpacity || 0.5, // Menos opaco em zooms altos
          color: layerConfig.color || '#0f0' // Borda
        };
      } else {
        // Default (fallback)
        return {
          ...baseStyle,
          fillColor: layerConfig.fillColor || '#ff0000',
          color: layerConfig.color || '#ff0000',
          fillOpacity: layerConfig.fillOpacity || 0.2
        };
      }
    };

    const vectorLayer = L.vectorGrid.protobuf(layerConfig.tileUrl, {
      vectorTileLayerStyles: {
        [layerConfig.layerName]: getStyleForLayer
      },
      interactive: true,
      getFeatureId: (f) => f.properties.id
    }).addTo(map);

    // Tooltips e popups genéricos (igual ao original)
    let tooltip;
    vectorLayer.on('mouseover', (e) => {
      if (e.layer && e.layer.properties) {
        const content = `<b>${layerConfig.label}</b><br>ID: ${e.layer.properties.id || 'N/A'}`;
        tooltip = L.tooltip({ sticky: true, opacity: 0.9 })
          .setContent(content)
          .setLatLng(e.latlng)
          .addTo(map);
      }
    });
    vectorLayer.on('mouseout', () => {
      if (tooltip) {
        map.removeLayer(tooltip);
        tooltip = null;
      }
    });

    vectorLayer.on('click', (e) => {
      if (e.layer && e.layer.properties) {
        const content = `<b>${layerConfig.label}</b><br>${generatePropertiesTable(e.layer.properties)}`;
        L.popup({ maxWidth: 400, maxHeight: 300 })
          .setLatLng(e.latlng)
          .setContent(content)
          .openOn(map);
      }
    });

    overlays[layerConfig.label] = vectorLayer;
  });

  L.control.layers(baseLayers, overlays).addTo(map);
});

// Função auxiliar para gerar tabela HTML (igual ao original)
const generatePropertiesTable = (properties) => {
  let tableContent = '<table style="border-collapse: collapse; width: 100%;">';
  tableContent += '<tr><th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Chave</th><th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Valor</th></tr>';

  Object.entries(properties).forEach(([key, value]) => {
    let displayValue = '';
    if (typeof value === 'object' && value !== null) {
      displayValue = JSON.stringify(value, null, 2).replace(/\n/g, '<br>');
    } else {
      displayValue = value;
    }
    tableContent += `<tr><td style="border: 1px solid #ddd; padding: 8px;">${key}</td><td style="border: 1px solid #ddd; padding: 8px;">${displayValue}</td></tr>`;
  });

  tableContent += '</table>';
  return tableContent;
};
</script>
