<template>
  <div ref="mapContainer" style="height: 750px; width: 100%" id="mapa"></div>
</template>

<script setup>
import { onMounted, ref } from "vue";
import L from "leaflet";
import "leaflet/dist/leaflet.css";
import "leaflet.vectorgrid";
import "leaflet-draw/dist/leaflet.draw.css"; // Novo: CSS para desenho
import "leaflet-measure/dist/leaflet-measure.css"; // Novo: CSS para medição
import "leaflet-draw"; // Novo: Plugin de desenho
import "leaflet-measure"; // Novo: Plugin de medição

const props = defineProps({
  layers: Array,
});
const layers_de_teste = ref([
  {
    layerName: "baciasdenivel2",
    tileUrl: "http://localhost:3000/bacias-de-nivel-2/{z}/{x}/{y}",
    geometryType: "Polygon",
    label: "Bacias de Nível 2",
    fillColor: "#ffff00", // Amarelo
  },
  {
    layerName: "baciasdenivel5",
    tileUrl: "http://localhost:3000/bacias-de-nivel-5/{z}/{x}/{y}",
    geometryType: "Polygon",
    label: "Bacias de Nível 5",
    fillColor: "#00ff00", // Verde
  },
  {
    layerName: "relevov2",
    tileUrl: "http://localhost:3000/relevo-v2/{z}/{x}/{y}",
    geometryType: "Point",
    label: "Relevo V2",
    fillColor: "#ff0000", // Vermelho para pontos
  },
  {
    layerName: "abastecimentosaneamento",
    tileUrl: "http://localhost:3000/abastecimento-saneamento/{z}/{x}/{y}",
    geometryType: "Point",
    label: "Abastecimento e Saneamento",
    fillColor: "#0000ff", // Azul para pontos
  },
  {
    layerName: "cavid",
    tileUrl: "http://localhost:3000/cavid/{z}/{x}/{y}",
    geometryType: "Point",
    label: "Cavidades",
    fillColor: "#0000ff", // Azul para pontos
  },
]);

const mapContainer = ref(null);

onMounted(() => {
  const map = L.map(mapContainer.value, {
    autoPan: false,
    popupAutoPan: false,
    popupOptions: {
      autoPan: false,
    },
  }).setView([-15.793889, -47.882778], 4);

  // Novo: Adicionar régua de medição (distâncias em km)
  const measureControl = new L.Control.Measure({
    position: "topleft",
    primaryLengthUnit: "kilometers",
    secondaryLengthUnit: "meters",
    activeColor: "#FF0000",
    completedColor: "#00FF00",
    // Tente desativar o autoPan temporariamente para diagnóstico
    popupOptions: {
      className: "leaflet-measure-popup",
      autoPan: false,
    },
  });
  map.addControl(measureControl);

  const basemap = L.tileLayer(
    "https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png",
    {
      attribution: "&copy; OpenStreetMap contributors",
    }
  ).addTo(map);

  const baseLayers = { OpenStreetMap: basemap };
  const overlays = {};

  //   props.layers.forEach((layerConfig) => {
  layers_de_teste.value.forEach((layerConfig) => {
    // Função de estilo personalizada por layer, usando configs como fillColor
    const getStyleForLayer = (properties, zoom) => {
      const baseStyle = { weight: 1, opacity: 1 }; // Comum a todos

      if (layerConfig.geometryType === "Point") {
        return {
          ...baseStyle,
          radius: 5, // Tamanho dinâmico
          fillColor: layerConfig.fillColor || "#ff0000", // Usa o personalizado ou default (vermelho)
          color: layerConfig.color || "#0f0", // Borda
          fillOpacity: layerConfig.fillOpacity || 0.8,
          fill: true,
        };
      } else if (layerConfig.geometryType === "MultiPoint") {
        return {
          ...baseStyle,
          radius: 10 + zoom / 5, // Tamanho dinâmico
          fillColor: layerConfig.fillColor || "#ff7800", // Usa o personalizado ou default
          color: layerConfig.color || "#0f0", // Borda
          fillOpacity: layerConfig.fillOpacity || 0.8,
        };
      } else if (
        layerConfig.geometryType === "LineString" ||
        layerConfig.geometryType === "MultiLineString"
      ) {
        return {
          ...baseStyle,
          color: layerConfig.color || "#0f0", // Usa o personalizado ou default (azul)
          weight: 2 + zoom / 10, // Espessura dinâmica
          fill: false, // Sem preenchimento
        };
      } else if (
        layerConfig.geometryType === "Polygon" ||
        layerConfig.geometryType === "MultiPolygon"
      ) {
        return {
          ...baseStyle,
          fill: true,
          fillColor: layerConfig.fillColor || "#00ff00", // Usa o personalizado ou default (verde)
          fillOpacity: layerConfig.fillOpacity || 0.5, // Menos opaco em zooms altos
          color: layerConfig.color || "#0f0", // Borda
        };
      } else {
        // Default (fallback)
        return {
          ...baseStyle,
          fillColor: layerConfig.fillColor || "#ff0000",
          color: layerConfig.color || "#ff0000",
          fillOpacity: layerConfig.fillOpacity || 0.2,
        };
      }
    };

    const vectorLayer = L.vectorGrid
      .protobuf(layerConfig.tileUrl, {
        vectorTileLayerStyles: {
          [layerConfig.layerName]: getStyleForLayer,
        },
        interactive: true,
        getFeatureId: (f) => f.properties.id,
      })
      .addTo(map);

    // Tooltips e popups genéricos (igual ao original)
    let tooltip;
    vectorLayer.on("mouseover", (e) => {
      if (e.layer && e.layer.properties) {
        const content = `<b>${layerConfig.label}</b><br>ID: ${
          e.layer.properties.id || "N/A"
        }`;
        tooltip = L.tooltip({ sticky: true, opacity: 0.9, autoPan: false, popupOptions: { autoPan: false } })
          .setContent(content)
          .setLatLng(e.latlng)
          .addTo(map);
      }
    });
    vectorLayer.on("mouseout", () => {
      if (tooltip) {
        map.removeLayer(tooltip);
        tooltip = null;
      }
    });

    vectorLayer.on("click", (e) => {
      if (e.layer && e.layer.properties) {
        const content = `<b>${
          layerConfig.label
        }</b><br>${generatePropertiesTable(e.layer.properties)}`;
        L.popup({ maxWidth: 400, maxHeight: 300, autoPan: false })
          .setLatLng(e.latlng)
          .setContent(content)
          .openOn(map);
      }
    });

    overlays[layerConfig.label] = vectorLayer;
  });

  L.control.layers(baseLayers, overlays).addTo(map);

  // Novo: Adicionar controle de desenho (polígonos, círculos, etc.)
  const drawnItems = new L.FeatureGroup(); // Grupo para armazenar os desenhos
  map.addLayer(drawnItems);

  const drawControl = new L.Control.Draw({
    position: "topleft", // Posição do toolbar
    popupOptions: {
      autoPan: false, // Desativa autoPan para evitar conflitos
    },
    draw: {
      polygon: true, // Ativa polígonos
      circle: true, // Ativa círculos de seleção
      rectangle: true, // Opcional: retângulos
      polyline: true, // Opcional: linhas
      marker: true, // Opcional: marcadores
      circlemarker: false, // Desativa se não precisar
    },
    edit: {
      featureGroup: drawnItems, // Permite editar/remover desenhos
      remove: true,
    },
  });
  map.addControl(drawControl);

  // Evento para quando um desenho é criado (exemplo: adicionar ao mapa)
  map.on(L.Draw.Event.CREATED, (e) => {
    const layer = e.layer;
    drawnItems.addLayer(layer); // Adiciona o desenho ao grupo
    // Aqui você pode adicionar lógica extra, como calcular área ou exportar GeoJSON: console.log(layer.toGeoJSON());
  });


});

// Função auxiliar para gerar tabela HTML (igual ao original)
const generatePropertiesTable = (properties) => {
  let tableContent = '<table style="border-collapse: collapse; width: 100%;">';
  tableContent +=
    '<tr><th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Chave</th><th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Valor</th></tr>';

  Object.entries(properties).forEach(([key, value]) => {
    let displayValue = "";
    if (typeof value === "object" && value !== null) {
      displayValue = JSON.stringify(value, null, 2).replace(/\n/g, "<br>");
    } else {
      displayValue = value;
    }
    tableContent += `<tr><td style="border: 1px solid #ddd; padding: 8px;">${key}</td><td style="border: 1px solid #ddd; padding: 8px;">${displayValue}</td></tr>`;
  });

  tableContent += "</table>";
  return tableContent;
};
</script>
<style lang="css" scoped>
/* Estilos adicionais, se necessário */
</style>
