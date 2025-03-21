<script setup>
import NavButton from '@/Components/NavButton.vue';
import BarChart from '@/Components/BarChart.vue';
import LineChart from '@/Components/LineChart.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Map from '@/Components/MapPontos.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';
import Navbar from '../Navbar.vue';
import ModalVideo from '../ModalVideo.vue';
import { IconPlayerPlay } from '@tabler/icons-vue';
import DivTabelaMedirIqaVue from '@/Pages/Servico/PMQA/Configuracao/Parametro/DivTabelaMedirIqa.vue';
import DivTabelaParametro from '@/Pages/Servico/PMQA/Configuracao/Parametro/DivTabelaParametro.vue';
import TabColeta from '@/Pages/Servico/PMQA/Execucao/TabColeta.vue';
import TabDadosPonto from "@/Pages/Servico/PMQA/Execucao/TabDadosPonto.vue";
import TabMedicao from "@/Pages/Servico/PMQA/Execucao/TabMedicao.vue";



const modalVideoRef = ref({});
const registro = ref('resultado');
const mapaVisualizarTrecho = ref(null);

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
    datasets: [
      {
        label: selectedPonto.value.ponto.nome_ponto_coleta,
        data: singleValueArray,
        borderColor: "black",
        backgroundColor: "transparent",
        pointBorderColor: "black",
        pointBackgroundColor: "white",
        pointRadius: 4,
        borderWidth: 1.5,
        tension: 0.2, // Suaviza a linha
      },
      {
        label: "Área de Qualidade",
        data: Array(12).fill(100), // Apenas para criar o fundo
        backgroundColor: [
          "rgba(173, 216, 230, 0.5)", // Ótima (Azul)
          "rgba(144, 238, 144, 0.5)", // Boa (Verde)
          "rgba(255, 255, 102, 0.5)", // Regular (Amarelo)
          "rgba(255, 165, 0, 0.5)", // Ruim (Laranja)
          "rgba(255, 69, 0, 0.5)",  // Péssima (Vermelho)
        ],
        borderWidth: 0,
        fill: "start",
      }
    ]
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

    // Atualiza os dados do gráfico corretamente
    chartDataIqa.value = response.data.chartDataIqa;
    uniqueParametros.value = response.data.uniqueParametros;

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

const abrirModalVideo = () => {
  modalVideoRef.value.abrirModal()
}

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
          coordinates: [longitude, latitude]
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
    x: {
      grid: { display: false },
    },
    y: {
      beginAtZero: true,
      max: 100,
      grid: { drawBorder: false },
    },
  },
});

const modalPontoMap = (ponto) => {
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

  <Head title="Dashboard" />
  <AuthenticatedLayout>
    <Navbar />
    <div>
      <div class="card card-body mb-4">
        <div class="text-end">
          <NavButton @click="abrirModalVideo()" :icon="IconPlayerPlay" title="Abrir Video" type-button="success" />
        </div>
        <div class="justify-content-center">
          <h1 class="text-center">
            Programa de Monitoramento da Qualidade da Água
          </h1>
          <div class=" d-flex justify-content-end">
            <div class="row w-25">
              <div class="col">
                <InputLabel value="UF" />
                <select name="" id="" class="form-select">
                  <option value="teste">teste</option>
                </select>
              </div>
              <div class="col">
                <InputLabel value="BR" />
                <select name="" id="" class="form-select">
                  <option value="teste">teste</option>
                </select>
              </div>
              <div class="col">
                <InputLabel value="Período" />
                <select name="" id="" class="form-select">
                  <option value="teste">teste</option>
                </select>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="">
        <div class="d-flex ">
          <div class="col-8 card card-body me-4">
            <Map ref="mapaVisualizarTrecho" height="900px" :manual-render="true" />
          </div>
          <div class="col-4">
            <div class="card card-body mb-4">
              <div class="">
                <label class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="radios-inline" value="resultado"
                    v-model="registro">
                  <span class="form-check-label">Resultados</span>
                </label>
                <label class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="radios-inline" value="registro" v-model="registro">
                  <span class="form-check-label">Registro</span>
                </label>
              </div>
            </div>
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

                  <!-- Gráfico IQA só aparece quando um resultado for selecionado -->
                  <div v-if="selectedResultado">
                    <BarChart :key="JSON.stringify(chartDataIqa)" id="div-parametro-iqa"
                      :style="{ height: '70px', position: 'relative' }" :chart_data="chartDataIqa"
                      :options="horizontalLine" />

                    <div class="card mb-4">
                      <DivTabelaMedirIqaVue />
                    </div>
                  </div>
                </div>
              </div>

              <div class="card" v-if="selectedResultado">
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

                  <!-- Gráfico de Parâmetro só aparece quando um resultado for selecionado -->
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
                <!-- Select para escolher um ponto -->
                <div class="mb-3">
                  <InputLabel value="Selecione uma Campanha" />
                  <select v-model="selectedCampanha" class="form-select">
                    <option v-for="campanha in campanhas" :key="campanha.id" :value="campanha">
                      {{ campanha.nome_campanha }}
                    </option>
                  </select>
                </div>

                <div v-if="selectedCampanha">

                  <!-- Select para escolher um ponto -->
                  <div class="mb-3">
                    <InputLabel value="Selecione um Ponto" />
                    <select v-model="selectedPonto" @change="moverParaPonto" class="form-select">
                      <option v-for="ponto in selectedCampanha.campanha_pontos" :key="ponto.id" :value="ponto">
                        {{ ponto.ponto.nome_ponto_coleta }}
                      </option>
                    </select>
                  </div>
                </div>

                <!-- Exibe os detalhes do ponto selecionado -->
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
                    <div v-if="selectedLineParametro">
                      <LineChart  :chart_data="chartDataLine" :chart_options="chartOptionsLine" />
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>

    <ModalVideo ref="modalVideoRef" url="/file/Dashboard/Dashboard_monitora_fauna.mp4" />

  </AuthenticatedLayout>

</template>
