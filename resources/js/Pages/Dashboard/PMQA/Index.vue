<script setup>
import BarChart from '@/Components/BarChart.vue';
import LineChart from '@/Components/LineChart.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Map from '@/Components/MapPontos.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';

import DivTabelaMedirIqaVue from '@/Pages/Servico/PMQA/Configuracao/Parametro/DivTabelaMedirIqa.vue';
import DivTabelaParametro from '@/Pages/Servico/PMQA/Configuracao/Parametro/DivTabelaParametro.vue';
import TabColeta from '@/Pages/Servico/PMQA/Execucao/TabColeta.vue';
import TabDadosPonto from "@/Pages/Servico/PMQA/Execucao/TabDadosPonto.vue";
import TabMedicao from "@/Pages/Servico/PMQA/Execucao/TabMedicao.vue";
import { controllers } from 'chart.js';

const registro = ref('resultado');
const mapaVisualizarTrecho = ref(null);
const coleta_realizada = ref([]);

const props = defineProps({
  contrato: Object,
  servico: Object,
  pontos: Object,
  resultados: Object,
  chartDataIqa: Object,
  parametros: Array,
  uniqueParametros: Object,
  campanhas: Object,
  responses: Array

});

const chartDataIqa = ref(props.chartDataIqa ?? { labels: [], datasets: [] });
const uniqueParametros = ref({
  1: {
    "id": null,
    "parametro": null,
    "unidade": null,
    "sigla": null,
    "classe_1": null,
    "classe_2": null,
    "classe_3": null,
    "classe_4": null,
    "limite": null,
    "condicao_especial": null,
    "condicao": null,
    "limite_descricao": null,
    "datasets": {
      "labels": [],
      "datasets": []
    }
  }
});

const selectedParametro = ref(uniqueParametros.value[0]);
const selectedLineParametro = ref(null);

const selectedResultado = ref(null);
const selectedPonto = ref(null);
const selectedCampanha = ref(null);


const chartDataLine = computed(() => {

  if (!selectedPonto.value || !selectedLineParametro.value) {
    return null;
  }

  const labels = props.responses.map(response => response.resultado.nome);
  const data = props.responses
    .map(response => {

      const uniqueParams = Array.isArray(response.uniqueParametros)
        ? response.uniqueParametros
        : Object.values(response.uniqueParametros);
      return uniqueParams.find(param => param.id === selectedLineParametro.value.id);
    })
    .filter(parametro => parametro !== undefined);

  const singleValueArray = data.map((campanha) => {
    const foundDataset = campanha.datasets.datasets.find(ds => ds.id === selectedPonto.value.fk_ponto);

    return foundDataset && foundDataset.data.length > 0
      ? foundDataset.data[0]
      : null;
  });

  return {
    labels: labels,
    responsive: true,
    scales: {
      y: {
        min: 0,
        max: 50,
        ticks: { stepSize: 10 }
      }
    },
    datasets: [{
      label: selectedPonto.value.ponto.nome_ponto_coleta,
      data: singleValueArray,
      borderColor: '#0054a6',
      backgroundColor: "transparent",
      pointBorderColor: "black",
      pointBackgroundColor: "white",
      pointRadius: 4,
      borderWidth: 2,
      datalabels: {
        display: false,
      }
    }]
  };
});


const buscarResultado = async () => {
  if (!selectedResultado.value) return;

  try {
    const response = await axios.get(
      route('contratos.contratada.servicos.pmqa.resultado.resultado.get', {
        contrato: props.contrato.id,
        servico: props.servico.id,
        resultado: selectedResultado.value
      })
    );

    chartDataIqa.value = response.data.chartDataIqa;
    uniqueParametros.value = response.data.uniqueParametros;

    coleta_realizada.value = response.data.medicao;

  } catch (error) {
    console.error('Erro ao buscar resultado:', error);
  }
};

const moverParaPonto = () => {

  if (selectedPonto.value.ponto && mapaVisualizarTrecho.value) {
    const longitude = Number(selectedPonto.value.ponto.long_y);
    const latitude = Number(selectedPonto.value.ponto.lat_x);

    // Criando GeoJSON do tipo "Point"
    const geojsonFeature = {
      coordinates: [[longitude, latitude]]
    };

    // Chamando a função para dar zoom no ponto
    mapaVisualizarTrecho.value.zoomToLinestring(JSON.stringify(geojsonFeature));

  } else {
    console.warn("Ponto não contém geojson_linestring válido!");
  }
};

watch(() => props.chartDataIqa, (newData) => {

  if (newData) {
    chartDataIqa.value = newData;

  }
}, { deep: true });

const horizontalLine = ref({
  plugins: {
    tooltip: {
      enabled: false
    },
    datalabels: {
      display: true,
      color: 'white',
      font: {
        weight: 'bold',
      },
    },
    annotation: {
      annotations: {
        line1: {
          type: 'line',
          yMin: 20,
          yMax: 20,
          borderColor: '#667382',
          borderWidth: 2,
          label: {
            content: 'Péssimo',
            enabled: true,
            position: 'start'
          }
        },
        line2: {
          type: 'line',
          yMin: 36,
          yMax: 36,
          borderColor: '#d63939',
          borderWidth: 2,
          label: {
            content: 'Threshold',
            enabled: true,
            position: 'start'
          }
        },
        line3: {
          type: 'line',
          yMin: 51,
          yMax: 51,
          borderColor: '#f76707',
          borderWidth: 2,
          label: {
            content: 'Threshold',
            enabled: true,
            position: 'start'
          }
        },
        line4: {
          type: 'line',
          yMin: 79,
          yMax: 79,
          borderColor: '#2fb344',
          borderWidth: 2,
          label: {
            content: 'Threshold',
            enabled: true,
            position: 'start'
          }
        },
        line5: {
          type: 'line',
          yMin: 100,
          yMax: 100,
          borderColor: '#0054a6',
          borderWidth: 2,
          label: {
            content: 'Threshold',
            enabled: true,
            position: 'start'
          }
        },
      }
    }
  }
})

const trechosVisualizacao = computed(() => {
  let geojson = [];

  props.pontos.forEach(ponto => {
    const longitude = Number(ponto.long_y);
    const latitude = Number(ponto.lat_x);

    geojson.push([
      JSON.stringify({
        type: "Feature",
        geometry: {
          type: "Point",
          coordinates: [longitude, latitude],
        }
      }),
      modalPontoMap(ponto),
      ponto
    ]);
  });

  return geojson;
});

const chartOptionsLine = ref({
  responsive: true,
  plugins: {
    legend: { display: false },
    tooltip: { enabled: true },
  },
  scales: {
    y: {
      beginAtZero: true,
      grid: { drawBorder: false },
    },
  },
});

const modalPontoMap = (ponto) => {
  return `
    <div class="popup-point">
      <h4>Dados do Ponto de Coleta</h4>
      <dl>
        <dt>Nome do Ponto:</dt><dd>${ponto.nome_ponto_coleta}</dd>
        <dt>Classificação:</dt><dd>${ponto.classificacao}</dd>
        <dt>Classe:</dt><dd>${ponto.classe}</dd>
        <dt>Tipo de Ambiente:</dt><dd>${ponto.tipo_ambiente}</dd>
        <dt>Latitude:</dt><dd>${ponto.lat_x}</dd>
        <dt>Longitude:</dt><dd>${ponto.long_y}</dd>
        <dt>UF:</dt><dd>${ponto.UF}</dd>
        <dt>Município:</dt><dd>${ponto.municipio !== '-' ? ponto.municipio : 'N/A'}</dd>
        <dt>Bacia Hidrográfica:</dt><dd>${ponto.bacia_hidrografica}</dd>
        <dt>Km da Rodovia:</dt><dd>${ponto.km_rodovia}</dd>
        <dt>Estaca:</dt><dd>${ponto.estaca !== '-' ? ponto.estaca : 'N/A'}</dd>
        <dt>Observações:</dt><dd>${ponto.observacoes || 'Nenhuma'}</dd>
      </dl>
    </div>
  `;
};

const modalPontoMap1 = (ponto) => {
  return `
    <span><strong>Dados do Ponto de Coleta</span></strong><br>
    <span><strong>Nome do Ponto: </strong> ${ponto.nome_ponto_coleta}</span><br>
    <span><strong>Classificação: </strong> ${ponto.classificacao}</span><br>
    <span><strong>Classe: </strong> ${ponto.classe}</span><br>
    <span><strong>Tipo de Ambiente: </strong> ${ponto.tipo_ambiente}</span><br>
    <span><strong>Latitude: </strong> ${ponto.lat_x}</span><br>
    <span><strong>Longitude: </strong> ${ponto.long_y}</span><br>
    <span><strong>UF: </strong> ${ponto.UF}</span><br>
    <span><strong>Município: </strong> ${ponto.municipio !== '-' ? ponto.municipio : 'N/A'}</span><br>
    <span><strong>Bacia Hidrográfica: </strong> ${ponto.bacia_hidrografica}</span><br>
    <span><strong>Km da Rodovia: </strong> ${ponto.km_rodovia}</span><br>
    <span><strong>Estaca: </strong> ${ponto.estaca !== '-' ? ponto.estaca : 'N/A'}</span><br>
    <span><strong>Observações: </strong> ${ponto.observacoes ? ponto.observacoes : 'Nenhuma'}</span><br>
    `;
};

setTimeout(() => {
  mapaVisualizarTrecho.value.renderMapa()
  mapaVisualizarTrecho.value.setLinestrings(trechosVisualizacao.value, true);
}, 500);
</script>

<template>

  <Head title="Dashboard PMQA" />
  <AuthenticatedLayout>
    <section aria-label="Cabeçalho" class="card mb-4 p-4 shadow-sm section-header">
      <h1 class="h4 fw-bold mb-2 section-title">Programa de Monitoramento da Qualidade da Água</h1>
      <div class="header-info d-flex justify-content-center gap-4 flex-wrap">
        <p class="info-item"><strong>Contratada:</strong> {{ contrato.contratada }}</p>
        <p class="info-item"><strong>Contrato:</strong> {{ contrato.numero_contrato }}</p>
        <p class="info-item"><strong>UFs/BRs:</strong> {{ contrato.ufs }} / {{ contrato.brs }}</p>
      </div>
    </section>
    <div>
      <div class="">
        <div class="d-flex ">
          <div class="col-7 card card-body me-4">
            <Map ref="mapaVisualizarTrecho" height="55vh" :manual-render="true" />
            <div v-if="selectedPonto">
              <div class="card-body">
                <div class="card">
                  <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs" role="tablist">
                      <li class="nav-item" role="presentation">
                        <a href="#tab-dados-ponto" class="nav-link active" data-bs-toggle="tab" aria-selected="true"
                          role="tab">Dados ponto</a>
                      </li>
                      <li class="nav-item" role="presentation">
                        <a href="#tab-dados-coleta" class="nav-link" data-bs-toggle="tab" aria-selected="false"
                          tabindex="-1" role="tab">Dados coleta</a>
                      </li>
                      <li class="nav-item" role="presentation">
                        <a href="#tab-dados-medicao" class="nav-link" data-bs-toggle="tab" aria-selected="false"
                          tabindex="-1" role="tab">Dados medição</a>
                      </li>
                    </ul>
                  </div>
                  <div class="card-body">
                    <div class="tab-content">
                      <div class="tab-pane active show" id="tab-dados-ponto" role="tabpanel">
                        <TabDadosPonto :ponto="selectedPonto" />
                      </div>
                      <div class="tab-pane" id="tab-dados-coleta" role="tabpanel">
                        <TabColeta :contrato="contrato" :servico="servico" :campanha="selectedCampanha"
                          :ponto="selectedPonto" />
                      </div>
                      <div class="tab-pane" id="tab-dados-medicao" role="tabpanel">
                        <TabMedicao :contrato="contrato" :servico="servico" :campanha="selectedCampanha"
                          :ponto="selectedPonto" />
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-5">
            <aside class="card p-3 shadow-sm w-100" style="width: 300px; max-height: 80vh;">
              <div class="btn-group w-100 mb-4" role="group" aria-label="Modo de exibição">
                <button type="button" class="btn"
                  :class="registro === 'resultado' ? 'btn-primary' : 'btn-outline-secondary'"
                  @click="registro = 'resultado'">Resultados</button>
                <button type="button" class="btn"
                  :class="registro === 'registro' ? 'btn-primary' : 'btn-outline-secondary'"
                  @click="registro = 'registro'">Registro</button>
              </div>
            </aside>
            <div v-if="registro === 'resultado'">
              <div class="card mb-4">
                <div class="card-header">
                  IQA
                </div>
                <div class="card-body">

                  <div class="mb-3">
                    <InputLabel value="Selecione um Resultado" />
                    <select v-model="selectedResultado" @change="buscarResultado" class="form-select">
                      <option v-for="resultado in props.resultados" :key="resultado.id" :value="resultado.id">
                        {{ resultado.nome }}
                      </option>
                    </select>
                  </div>

                  <div v-if="selectedResultado && coleta_realizada.medido">
                    <BarChart :key="JSON.stringify(chartDataIqa)" id="div-parametro-iqa"
                      :style="{ height: '70px', position: 'relative' }" :chart_data="chartDataIqa"
                      :options="horizontalLine" />

                    <div class="card mb-4">
                      <DivTabelaMedirIqaVue />
                    </div>
                  </div>
                  <div v-if="selectedResultado && coleta_realizada.medido == false"
                    class="alert alert-info d-flex align-items-start" role="alert">
                    <span class="me-3 fs-3 text-warning">
                      <i class="bi bi-exclamation-triangle-fill"></i>
                    </span>
                    <div>
                      <h5 class="alert-heading mb-1">Coleta não realizada</h5>
                      <p class="mb-2">Não foi possível realizar a coleta.</p>
                      <p class="mb-0">
                        <strong>Justificativa:</strong>
                        {{ coleta_realizada.justificativa || 'Não informada' }}
                      </p>
                    </div>
                  </div>
                </div>
              </div>
              <div class="card" v-if="selectedResultado && coleta_realizada.medido">
                <div class="card-header">
                  Outros Parâmetros
                </div>
                <div class="card-body">
                  <div class="mb-3">
                    <label for="selectParametro" class="form-label">Selecione um Parâmetro:</label>
                    <select v-model="selectedParametro" id="selectParametro" class="form-select">
                      <option v-for="parametro in uniqueParametros" :key="parametro.id" :value="parametro">
                        {{ parametro.parametro }}
                      </option>
                    </select>
                  </div>

                  <div v-if="selectedParametro">
                    <BarChart height="100px" :id="'divs-parametro-' + selectedParametro.id"
                      :name="'divs-parametro-' + selectedParametro.id" :chart_data="selectedParametro.datasets" />

                    <div class="card mb-4">
                      <DivTabelaParametro :parametro="selectedParametro" />
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div v-else>
              <div class="card card-body mb-4">
                <div class="mb-3">
                  <InputLabel value="Selecione uma Campanha" />
                  <select v-model="selectedCampanha" class="form-select">
                    <option v-for="campanha in campanhas" :key="campanha.id" :value="campanha">
                      {{ campanha.nome_campanha }}
                    </option>
                  </select>
                </div>

                <div v-if="selectedCampanha">
                  <div class="mb-3">
                    <InputLabel value="Selecione um Ponto" />
                    <select v-model="selectedPonto" @change="moverParaPonto" class="form-select">
                      <option v-for="ponto in selectedCampanha.campanha_pontos" :key="ponto.id" :value="ponto">
                        {{ ponto.ponto.nome_ponto_coleta }}
                      </option>
                    </select>
                  </div>
                </div>

                <div v-if="selectedPonto">
                  <div class="card card-body">
                    <div class="row mb-4">
                      <div class="col">
                        <div class="mb-3">
                          <label for="selectParametro" class="form-label">Selecione um Parâmetro:</label>
                          <select v-model="selectedLineParametro" id="selectParametro" class="form-select">
                            <option v-for="parametroLine in props.responses[0].uniqueParametros" :key="parametroLine.id"
                              :value="parametroLine">
                              {{ parametroLine.parametro }}
                            </option>
                          </select>
                        </div>
                      </div>
                    </div>
                    <div  v-if="selectedLineParametro">
                      <LineChart :chart_data="chartDataLine" :chart_options="chartOptionsLine" />
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>


<style>
.section-header {
  background: linear-gradient(135deg, #f0f4ff 0%, #ffffff 100%);
  border-left: 4px solid #0054a6;
  border-radius: 0.5rem;
  padding: 1.5rem 2rem;
  text-align: center;
}

.section-title {
  font-size: 1.75rem;
  color: #0054a6;
  letter-spacing: 0.03rem;
  margin-bottom: 0.5rem;
}

.header-info {
  color: #333;
}

.info-item {
  font-size: 1rem;
}

.info-item strong {
  color: #0054a6;
}

@media (max-width: 768px) {
  .section-header {
    padding: 1rem;
  }

  .section-title {
    font-size: 1.5rem;
  }

  .header-info {
    gap: 1rem !important;
  }
}

/* Container do popup */
.leaflet-popup-custom .leaflet-popup-content-wrapper {
  background: rgba(255, 255, 255, 0.95);
  border-radius: 8px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  padding: 12px 16px;
}

/* A seta abaixo do popup */
.leaflet-popup-custom .leaflet-popup-tip {
  background: rgba(255, 255, 255, 0.95);
}

/* Botão de fechar */
.leaflet-popup-custom .leaflet-popup-close-button {
  color: #555;
  font-size: 16px;
  opacity: 0.7;
}

.leaflet-popup-custom .leaflet-popup-close-button:hover {
  opacity: 1;
}

/* Título do popup */
.popup-point h4 {
  margin: 0 0 8px;
  font-size: 1rem;
  /* color: #0054a6; */
  border-bottom: 1px solid #e0e0e0;
  padding-bottom: 4px;
}

/* Lista de detalhes */
.popup-point dl {
  display: grid;
  grid-template-columns: max-content auto;
  row-gap: 4px;
  column-gap: 8px;
  font-size: 0.875rem;
  color: #333;
}

.popup-point dt {
  font-weight: 600;
  /* color: #0054a6; */
  text-align: right;
}

.popup-point dd {
  margin: 0;
  color: #555;
}
</style>