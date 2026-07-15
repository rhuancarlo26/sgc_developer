<script setup>
import { ref, computed, watch, defineProps, nextTick } from 'vue';
import MapSgc from '@/Components/MapSgc.vue';

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
const mapasRef = ref({});

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
          geo_json: null,
          shapefile_id: null,
          codigo_sei: '',
          enviando: false,
        };
      }
    });
  },
  { immediate: true }
);
const setMapaRef = (nomeCampo, el) => {
  if (el) {
    mapasRef.value[nomeCampo] = el;
  }
};

const selecionarShapefile = (campo, arquivo) => {
  formMetodologia.value[campo.nome].arquivo = arquivo;
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

    item.geo_json = typeof response.data.data.geo_json === 'string'
      ? response.data.data.geo_json
      : JSON.stringify(response.data.data.geo_json);

    const geojsonDebug = JSON.parse(item.geo_json);

    console.log('GeoJSON recebido:', geojsonDebug);
    console.log('Total de features:', geojsonDebug.features?.length);
    console.log('Primeira geometria:', geojsonDebug.features?.[0]?.geometry);
    item.shapefile_id = response.data.data.id;

    await nextTick();
    await visualizarShapefile(campo);
  } catch (error) {
    console.error('Erro ao enviar shapefile:', error.response?.data || error);
  } finally {
    item.enviando = false;
  }
};

const visualizarShapefile = async (campo) => {
  const item = formMetodologia.value[campo.nome];
  const mapa = mapasRef.value[campo.nome];

  if (!mapa || !item.geo_json) return;

  await nextTick();

  mapa.renderMapa();
  mapa.setGeoJson(item.geo_json);
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
                    :disabled="!formMetodologia[campo.nome].geo_json"
                    @click="visualizarShapefile(campo)"
                  >
                    Visualizar
                  </button>
                </div>

                <div v-if="formMetodologia[campo.nome].geo_json" class="mt-3">
                  <MapSgc
                    :ref="el => setMapaRef(campo.nome, el)"
                    height="350px"
                    width="100%"
                    :manual-render="true"
                  />
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
                    :disabled="!formMetodologia[campo.nome].geo_json"
                    @click="visualizarShapefile(campo)"
                  >
                    Visualizar mapa
                  </button>
                </div>

                <div v-if="formMetodologia[campo.nome].geo_json" class="mt-3">
                  <MapSgc
                    :ref="el => setMapaRef(campo.nome, el)"
                    height="350px"
                    width="100%"
                    :manual-render="true"
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

</style>
