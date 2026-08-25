<script setup>
import { ref, computed, watch, defineProps, nextTick } from 'vue';

const props = defineProps({
  paipaId: [Number, String],
  contrato: [Number, String],
});

const NivelEmpreendimento = {
  NIVEL_I: 'NIVEL_I',
  NIVEL_II: 'NIVEL_II',
  NIVEL_III: 'NIVEL_III',
  NIVEL_IV: 'NIVEL_IV',
};

const TipoVariavel = {
  BINARIO: 'BINARIO',
  TEXTO: 'TEXTO',
  DATA: 'DATA',
  GEO: 'GEO',
  GEO_TEXTO: 'GEO_TEXTO',
};

const nivelSelecionado = ref('');
const formMetodologia = ref({});
const mapasLeaflet = ref({});
const wmsLayers = ref({});

const labelsGrupo = {
  INFORMACOES_GERAIS: 'Informações Gerais',
  CARACTERIZACAO: 'Caracterização',
  METODOLOGIA: 'Metodologia',
};

const variaveisMetodologia = [
  {
    ordem: 1,
    grupo: 'INFORMACOES_GERAIS',
    nome: 'Termo de Compromisso do Empreendedor - TCE',
    tipo: TipoVariavel.BINARIO,
    campoTextoLivre: true,
    campoCodigoSei: true,
    niveisAplicaveis: [
      NivelEmpreendimento.NIVEL_I,
      NivelEmpreendimento.NIVEL_II,
      NivelEmpreendimento.NIVEL_III,
      NivelEmpreendimento.NIVEL_IV,
    ],
  },
  {
    ordem: 2,
    grupo: 'CARACTERIZACAO',
    nome: 'Caracterização do Empreendimento',
    tipo: TipoVariavel.TEXTO,
    campoTextoLivre: true,
    campoCodigoSei: false,
    niveisAplicaveis: [
      NivelEmpreendimento.NIVEL_II,
      NivelEmpreendimento.NIVEL_III,
      NivelEmpreendimento.NIVEL_IV,
    ],
  },
  {
    ordem: 3,
    grupo: 'CARACTERIZACAO',
    nome: 'Contextualização Geoambiental',
    tipo: TipoVariavel.TEXTO,
    campoTextoLivre: true,
    campoCodigoSei: false,
    niveisAplicaveis: [
      NivelEmpreendimento.NIVEL_II,
      NivelEmpreendimento.NIVEL_III,
      NivelEmpreendimento.NIVEL_IV,
    ],
  },
  {
    ordem: 4,
    grupo: 'CARACTERIZACAO',
    nome: 'Contextualização Arqueológica',
    tipo: TipoVariavel.TEXTO,
    campoTextoLivre: true,
    campoCodigoSei: false,
    niveisAplicaveis: [
      NivelEmpreendimento.NIVEL_II,
      NivelEmpreendimento.NIVEL_III,
      NivelEmpreendimento.NIVEL_IV,
    ],
  },
  {
    ordem: 5,
    grupo: 'CARACTERIZACAO',
    nome: 'Contextualização Etno-histórica',
    tipo: TipoVariavel.TEXTO,
    campoTextoLivre: true,
    campoCodigoSei: false,
    niveisAplicaveis: [
      NivelEmpreendimento.NIVEL_II,
      NivelEmpreendimento.NIVEL_III,
      NivelEmpreendimento.NIVEL_IV,
    ],
  },
  {
    ordem: 6,
    grupo: 'CARACTERIZACAO',
    nome: 'Contextualização Histórica',
    tipo: TipoVariavel.TEXTO,
    campoTextoLivre: true,
    campoCodigoSei: false,
    niveisAplicaveis: [
      NivelEmpreendimento.NIVEL_II,
      NivelEmpreendimento.NIVEL_III,
      NivelEmpreendimento.NIVEL_IV,
    ],
  },
  {
    ordem: 7,
    grupo: 'METODOLOGIA',
    nome: 'Análise de Materiais Pré-Coloniais',
    tipo: TipoVariavel.TEXTO,
    campoTextoLivre: true,
    campoCodigoSei: false,
    niveisAplicaveis: [
      NivelEmpreendimento.NIVEL_II,
      NivelEmpreendimento.NIVEL_III,
      NivelEmpreendimento.NIVEL_IV,
    ],
  },
  {
    ordem: 8,
    grupo: 'METODOLOGIA',
    nome: 'Análise de Materiais Históricos',
    tipo: TipoVariavel.TEXTO,
    campoTextoLivre: true,
    campoCodigoSei: false,
    niveisAplicaveis: [
      NivelEmpreendimento.NIVEL_II,
      NivelEmpreendimento.NIVEL_III,
      NivelEmpreendimento.NIVEL_IV,
    ],
  },
  {
    ordem: 9,
    grupo: 'METODOLOGIA',
    nome: 'Laboratório Indicado',
    tipo: TipoVariavel.TEXTO,
    campoTextoLivre: true,
    campoCodigoSei: false,
    niveisAplicaveis: [NivelEmpreendimento.NIVEL_II],
  },
  {
    ordem: 10,
    grupo: 'METODOLOGIA',
    nome: 'Extroversão do Conhecimento Arqueológico',
    tipo: TipoVariavel.TEXTO,
    campoTextoLivre: true,
    campoCodigoSei: false,
    niveisAplicaveis: [NivelEmpreendimento.NIVEL_II],
  },
  {
    ordem: 11,
    grupo: 'METODOLOGIA',
    nome: 'Acompanhamento Arqueológico',
    tipo: TipoVariavel.TEXTO,
    campoTextoLivre: true,
    campoCodigoSei: false,
    niveisAplicaveis: [NivelEmpreendimento.NIVEL_II],
  },
  {
    ordem: 12,
    grupo: 'METODOLOGIA',
    nome: 'Segmentos Rodoviários de Acompanhamento Arqueológico',
    tipo: TipoVariavel.GEO,
    campoTextoLivre: true,
    campoCodigoSei: false,
    niveisAplicaveis: [NivelEmpreendimento.NIVEL_II],
  },
  {
    ordem: 13,
    grupo: 'METODOLOGIA',
    nome: 'Delimitação de Sítio',
    tipo: TipoVariavel.TEXTO,
    campoTextoLivre: true,
    campoCodigoSei: false,
    niveisAplicaveis: [
      NivelEmpreendimento.NIVEL_II,
      NivelEmpreendimento.NIVEL_III,
      NivelEmpreendimento.NIVEL_IV,
    ],
  },
  {
    ordem: 14,
    grupo: 'METODOLOGIA',
    nome: 'Contexto Teórico',
    tipo: TipoVariavel.TEXTO,
    campoTextoLivre: true,
    campoCodigoSei: false,
    niveisAplicaveis: [
      NivelEmpreendimento.NIVEL_III,
      NivelEmpreendimento.NIVEL_IV,
    ],
  },
  {
    ordem: 15,
    grupo: 'METODOLOGIA',
    nome: 'Prospecção Arqueológica',
    tipo: TipoVariavel.GEO_TEXTO,
    campoTextoLivre: true,
    campoCodigoSei: false,
    niveisAplicaveis: [
      NivelEmpreendimento.NIVEL_III,
      NivelEmpreendimento.NIVEL_IV,
    ],
  },
  {
    ordem: 16,
    grupo: 'METODOLOGIA',
    nome: 'Potencial Arqueológico',
    tipo: TipoVariavel.GEO_TEXTO,
    campoTextoLivre: true,
    campoCodigoSei: false,
    niveisAplicaveis: [
      NivelEmpreendimento.NIVEL_III,
      NivelEmpreendimento.NIVEL_IV,
    ],
  },
  {
    ordem: 17,
    grupo: 'METODOLOGIA',
    nome: 'ADA',
    tipo: TipoVariavel.GEO,
    campoTextoLivre: true,
    campoCodigoSei: false,
    niveisAplicaveis: [
      NivelEmpreendimento.NIVEL_III,
      NivelEmpreendimento.NIVEL_IV,
    ],
  },
  {
    ordem: 18,
    grupo: 'METODOLOGIA',
    nome: 'Cronograma',
    tipo: TipoVariavel.DATA,
    campoTextoLivre: true,
    campoCodigoSei: false,
    niveisAplicaveis: [
      NivelEmpreendimento.NIVEL_II,
      NivelEmpreendimento.NIVEL_III,
      NivelEmpreendimento.NIVEL_IV,
    ],
  },
];

const gruposCampos = computed(() => {
  return camposVisiveis.value.reduce((grupos, campo) => {
    if (!grupos[campo.grupo]) {
      grupos[campo.grupo] = [];
    }

    grupos[campo.grupo].push(campo);

    return grupos;
  }, {});
});

const camposVisiveis = computed(() => {
  if (!nivelSelecionado.value) return [];

  return variaveisMetodologia
    .filter(campo => campo.niveisAplicaveis.includes(nivelSelecionado.value))
    .sort((a, b) => a.ordem - b.ordem);
});

watch(
  camposVisiveis,
  (campos) => {
    campos.forEach((campo) => {
      if (!formMetodologia.value[campo.nome]) {
        formMetodologia.value[campo.nome] = {
          tipo: campo.tipo,
          texto: '',
          valor: '',
          data: '',
          arquivo: null,
          shapefile_id: null,
          codigo_sei: '',
          enviando: false,
          workspace: null,
          layer_name: null,
        };
      }
    });
  },
  { immediate: true }
);

const selecionarShapefile = (campo, arquivo) => {
  formMetodologia.value[campo.nome].arquivo = arquivo;
};

const getMapaId = (campo) => `mapa-shapefile-${campo.ordem}`;

const getLayerBoundsFromCapabilities = async (qualifiedLayerName) => {
  const params = new URLSearchParams({
    service: 'WMS',
    request: 'GetCapabilities',
    version: '1.1.1',
  });

  const response = await fetch(`/sgc/contratada/mapa/wms?${params.toString()}`);
  const xml = await response.text();
  const doc = new DOMParser().parseFromString(xml, 'text/xml');

  const layers = Array.from(doc.getElementsByTagName('Layer'));
  const layer = layers.find((node) => {
    const name = node.getElementsByTagName('Name')[0]?.textContent?.trim();

    return name === qualifiedLayerName || name === qualifiedLayerName.split(':').pop();
  });

  if (!layer) return null;

  const latLonBox = layer.getElementsByTagName('LatLonBoundingBox')[0];

  if (latLonBox) {
    const west = Number(latLonBox.getAttribute('minx'));
    const south = Number(latLonBox.getAttribute('miny'));
    const east = Number(latLonBox.getAttribute('maxx'));
    const north = Number(latLonBox.getAttribute('maxy'));

    if ([west, south, east, north].every(Number.isFinite)) {
      return L.latLngBounds([south, west], [north, east]);
    }
  }

  const geographicBox = layer.getElementsByTagName('EX_GeographicBoundingBox')[0];

  if (geographicBox) {
    const getValue = (tagName) => Number(geographicBox.getElementsByTagName(tagName)[0]?.textContent);
    const west = getValue('westBoundLongitude');
    const south = getValue('southBoundLatitude');
    const east = getValue('eastBoundLongitude');
    const north = getValue('northBoundLatitude');

    if ([west, south, east, north].every(Number.isFinite)) {
      return L.latLngBounds([south, west], [north, east]);
    }
  }

  return null;
};

const ajustarZoomCamadaWms = async (mapa, qualifiedLayerName) => {
  try {
    const bounds = await getLayerBoundsFromCapabilities(qualifiedLayerName);

    if (!bounds?.isValid()) return;

    if (bounds.getNorthEast().equals(bounds.getSouthWest())) {
      mapa.setView(bounds.getCenter(), 16);
      return;
    }

    mapa.fitBounds(bounds.pad(0.1), {
      padding: [24, 24],
      maxZoom: 16,
    });
  } catch (error) {
    console.warn('Não foi possível ajustar o zoom da camada WMS.', error);
  }
};

const renderizarMapaWms = async (campo) => {
  const item = formMetodologia.value[campo.nome];

  if (!item.workspace || !item.layer_name) return;

  await nextTick();

  const id = getMapaId(campo);
  const container = document.getElementById(id);

  if (!container) return;

  if (!mapasLeaflet.value[id]) {
    mapasLeaflet.value[id] = L.map(container).setView([-14.235, -51.925], 6);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 18,
      attribution: '&copy; OpenStreetMap',
    }).addTo(mapasLeaflet.value[id]);
  }

  if (wmsLayers.value[id]) {
    mapasLeaflet.value[id].removeLayer(wmsLayers.value[id]);
  }

  wmsLayers.value[id] = L.tileLayer.wms('/sgc/contratada/mapa/wms', {
    layers: `${item.workspace}:${item.layer_name}`,
    format: 'image/png8',
    transparent: true,
    version: '1.1.1',
    srs: 'EPSG:3857',
    tiled: true,
    opacity: 0.7,
    updateWhenIdle: true,
    updateWhemZooming: false,
    keepBuffer: 2
  }).addTo(mapasLeaflet.value[id]);

  mapasLeaflet.value[id].off('click');
  mapasLeaflet.value[id].on('click', async (e) => {

    const point = mapasLeaflet.value[id].latLngToContainerPoint(e.latlng, mapasLeaflet.value[id].getZoom());
    const size = mapasLeaflet.value[id].getSize();
    const bounds = mapasLeaflet.value[id].getBounds();

    const params = new URLSearchParams({
        request: 'GetFeatureInfo',
        service: 'WMS',
        srs: 'EPSG:4326',
        styles: '',
        transparent: true,
        version: '1.1.1',
        bbox: `${bounds.getSouthWest().lng},${bounds.getSouthWest().lat},${bounds.getNorthEast().lng},${bounds.getNorthEast().lat}`,
        height: size.y,
        width: size.x,
        layers: `${item.workspace}:${item.layer_name}`,
        query_layers: `${item.workspace}:${item.layer_name}`,
        info_format: 'application/json',
        x: Math.round(point.x),
        y: Math.round(point.y),
    });

    try {
        const response = await fetch(`/sgc/contratada/mapa/wms?${params.toString()}`);
        const data = await response.json();

        if (data.features && data.features.length > 0) {
            const propriedades = data.features[0].properties;

            let htmlInfo = '<div style="max-height: 250px; overflow-y: auto;">';
            htmlInfo += '<h6 class="border-bottom pb-1 mb-2">Informações do Elemento</h6>';
            htmlInfo += '<ul class="list-unstyled mb-0" style="font-size: 13px;">';

            for (const [key, value] of Object.entries(propriedades)) {
              if (value && key !== 'bbox' && !key.includes('geom')) {
                htmlInfo += `<li class="mb-1"><strong>${key}:</strong> ${value}</li>`;
              }
            }
            htmlInfo += '</ul></div>';

            L.popup()
              .setLatLng(e.latlng)
              .setContent(htmlInfo)
              .openOn(mapasLeaflet.value[id]);

        }
    } catch (err) {
        console.warn('Não foi possivel buscar as propriedades do shapefile ou o clique foi fora do elemento.')
    }
  })

  setTimeout(() => {
    mapasLeaflet.value[id].invalidateSize();
  }, 300);

  await ajustarZoomCamadaWms(mapasLeaflet.value[id], `${item.workspace}:${item.layer_name}`);
};

const enviarShapefile = async (campo) => {
  const item = formMetodologia.value[campo.nome];

  if (!item.arquivo) return;

  const formData = new FormData();
  formData.append('patrimonio_paipa_id', props.paipaId);
  formData.append('nome_campo', campo.nome);
  formData.append('shapefile', item.arquivo);

  item.enviando = true;

  try {
    const response = await axios.post(route('patrimonio.shapefile.store', {
      contrato: props.contrato,
      produto: 'patrimonio',
    }), formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    });

    const shapefile = response.data.data;

    item.workspace = shapefile.workspace ?? shapefile.layer?.workspace ?? null;
    item.layer_name = shapefile.layer_name ?? shapefile.layer?.layer_name ?? null;
    item.shapefile_id = shapefile.id;

    if (!item.workspace || !item.layer_name) {
      console.warn('O upload foi concluido, mas o backend nao retornou workspace/layer_name para renderizar WMS.', shapefile);
      return;
    }

    await nextTick();
    await renderizarMapaWms(campo);
  } catch (error) {
    console.error('Erro ao enviar shapefile:', error.response?.data || error);
  } finally {
    item.enviando = false;
  }
};

defineEmits(['voltar']);
</script>

<template>
  <div>
    <h4>Metodologia</h4>

    <div class="row mb-4">
      <div class="col-md-4">
        <label class="form-label">Nível do empreendimento</label>
        <select v-model="nivelSelecionado" class="form-select">
          <option value="">Selecione</option>
          <option :value="NivelEmpreendimento.NIVEL_I">Nível I</option>
          <option :value="NivelEmpreendimento.NIVEL_II">Nível II</option>
          <option :value="NivelEmpreendimento.NIVEL_III">Nível III</option>
          <option :value="NivelEmpreendimento.NIVEL_IV">Nível IV</option>
        </select>
      </div>
    </div>

    <div v-if="nivelSelecionado">
      <div
        v-for="(campos, grupo) in gruposCampos"
        :key="grupo"
        class="mb-4"
      >
        <h5 class="mb-3">{{ labelsGrupo[grupo] }}</h5>

        <div class="row g-3">
          <div
            v-for="campo in campos"
            :key="campo.nome"
            :class="[
              campo.tipo === TipoVariavel.GEO || campo.tipo === TipoVariavel.GEO_TEXTO
                ? 'col-md-12'
                : 'col-md-6'
            ]"
          >
            <div
              :class="[
                campo.tipo === TipoVariavel.GEO || campo.tipo === TipoVariavel.GEO_TEXTO
                  ? 'border rounded p-3'
                  : ''
              ]"
            >
              <div
                v-if="campo.tipo === TipoVariavel.GEO || campo.tipo === TipoVariavel.GEO_TEXTO"
                class="d-flex justify-content-between align-items-center mb-2"
              >
                <strong>{{ campo.nome }}</strong>
                <span class="badge bg-secondary">
                  {{ campo.tipo === TipoVariavel.GEO ? 'Shapefile' : 'Texto + Shapefile' }}
                </span>
              </div>

              <label
                v-else
                class="form-label"
              >
                {{ campo.nome }}
              </label>

              <input
                v-if="campo.campoCodigoSei"
                v-model="formMetodologia[campo.nome].codigo_sei"
                type="text"
                class="form-control mb-2"
                placeholder="Código SEI"
              />

              <div v-if="campo.tipo === TipoVariavel.BINARIO" class="d-flex gap-3">
                <label class="form-check">
                  <input
                    v-model="formMetodologia[campo.nome].valor"
                    class="form-check-input"
                    type="radio"
                    value="sim"
                  />
                  <span class="form-check-label">Sim</span>
                </label>

                <label class="form-check">
                  <input
                    v-model="formMetodologia[campo.nome].valor"
                    class="form-check-input"
                    type="radio"
                    value="nao"
                  />
                  <span class="form-check-label">Não</span>
                </label>
              </div>

              <textarea
                v-else-if="campo.tipo === TipoVariavel.TEXTO"
                v-model="formMetodologia[campo.nome].texto"
                class="form-control"
                rows="3"
              />

              <div v-else-if="campo.tipo === TipoVariavel.GEO">
                <input
                  type="file"
                  class="form-control"
                  accept=".zip"
                  @change="event => selecionarShapefile(campo, event.target.files[0])"
                />

                <div class="mt-2 d-flex gap-2">
                  <button
                    type="button"
                    class="btn btn-sm btn-success"
                    :disabled="!formMetodologia[campo.nome].arquivo || formMetodologia[campo.nome].enviando"
                    @click="enviarShapefile(campo)"
                  >
                    {{ formMetodologia[campo.nome].enviando ? 'Enviando...' : 'Enviar' }}
                  </button>

                  <button
                    type="button"
                    class="btn btn-sm btn-outline-primary"
                    :disabled="!formMetodologia[campo.nome].workspace || !formMetodologia[campo.nome].layer_name"
                    @click="renderizarMapaWms(campo)"
                  >
                    Visualizar
                  </button>
                </div>

                <div
                  v-if="formMetodologia[campo.nome].workspace && formMetodologia[campo.nome].layer_name"
                  class="mt-3"
                >
                    <div
                        v-if="formMetodologia[campo.nome].workspace && formMetodologia[campo.nome].layer_name"
                        class="mt-3"
                    >
                        <div :id="getMapaId(campo)" class="mapa-shapefile" />

                        <div class="mt-2 p-2 border rounded bg-light text-center">
                            <span class="d-block mb-1 text-muted small fw-bold">Legenda do Mapa</span>
                                    <img
                                    :src="`/sgc/contratada/mapa/wms?REQUEST=GetLegendGraphic&VERSION=1.0.0&FORMAT=image/png&WIDTH=20&HEIGHT=20&LAYER=${formMetodologia[campo.nome].
                            workspace}:${formMetodologia[campo.nome].layer_name}`"
                                    alt="Carregando Legenda..."
                                    />
                        </div>
                    </div>
                </div>
              </div>

              <div v-else-if="campo.tipo === TipoVariavel.GEO_TEXTO">
                <textarea
                  v-model="formMetodologia[campo.nome].texto"
                  class="form-control mb-2"
                  rows="3"
                  placeholder="Texto livre"
                />

                <input
                  type="file"
                  class="form-control"
                  accept=".zip"
                  @change="event => selecionarShapefile(campo, event.target.files[0])"
                />

                <div class="mt-2 d-flex gap-2">
                  <button
                    type="button"
                    class="btn btn-sm btn-success"
                    :disabled="!formMetodologia[campo.nome].arquivo || formMetodologia[campo.nome].enviando"
                    @click="enviarShapefile(campo)"
                  >
                    {{ formMetodologia[campo.nome].enviando ? 'Enviando...' : 'Enviar shapefile' }}
                  </button>

                  <button
                    type="button"
                    class="btn btn-sm btn-outline-primary"
                    :disabled="!formMetodologia[campo.nome].workspace || !formMetodologia[campo.nome].layer_name"
                    @click="renderizarMapaWms(campo)"
                  >
                    Visualizar mapa
                  </button>
                </div>

                <div
                  v-if="formMetodologia[campo.nome].workspace && formMetodologia[campo.nome].layer_name"
                  class="mt-3"
                >
                  <div
                    :id="getMapaId(campo)"
                    class="mapa-shapefile"
                  />
                </div>
              </div>

              <input
                v-else-if="campo.tipo === TipoVariavel.DATA"
                v-model="formMetodologia[campo.nome].data"
                type="date"
                class="form-control"
              />
            </div>
          </div>
        </div>
      </div>
    </div>

    <div v-else class="text-muted">
      Selecione um nível para exibir os campos de metodologia.
    </div>

    <div class="d-flex justify-content-between mt-4">
      <button type="button" class="btn btn-secondary" @click="$emit('voltar')">
        Voltar
      </button>

      <button type="button" class="btn btn-primary" :disabled="!nivelSelecionado">
        Avançar
      </button>
    </div>
  </div>
</template>

<style scoped>
  .mapa-shapefile {
    height: 350px;
    width: 100%;
  }
</style>
