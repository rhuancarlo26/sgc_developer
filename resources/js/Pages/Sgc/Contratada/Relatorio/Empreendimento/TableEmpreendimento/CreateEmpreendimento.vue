<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { nextTick, onMounted, watch, ref } from 'vue';
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import { IconPlus, IconX } from "@tabler/icons-vue";
import { useToast } from "vue-toastification";
import Map from '@/Components/MapSgc.vue';
import axios from 'axios';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import Navbar from '../../../Navbar.vue';
import { router } from '@inertiajs/vue3';


const props = defineProps({
  contrato: Object,
  ufs: Object,
  rodovias: Object,
  empreendimento: Object,
  tipo: Object
});

const formKey = ref(0);
const mapaVisualizarTrecho = ref();
const uf_rodovias = ref([]);
const showMap = ref(false);
const toast = useToast();
const trechosAcumulados = ref([]);
const ufsProcessadas = ref(new Set());

const form_trecho = useForm({
  contrato_est_ambiental: props.empreendimento?.contrato_est_ambiental ?? null,
  cod_emp: props.empreendimento?.cod_emp ?? null,
  ose_sei: props.empreendimento?.ose_sei ?? null,
  uf: props.empreendimento?.uf ?? null,
  rodovia: props.empreendimento?.br ?? null,
  km_inicial: props.empreendimento?.km_inicial ?? null,
  km_final: props.empreendimento?.km_final ?? null,
  tipo_trecho: props.empreendimento?.tipo_trecho ?? 'B',
  cd_tipo: props.empreendimento?.cd_tipo ?? null,
  coordenada: props.empreendimento?.coordenadas ?? ''
});


const visualizarTrecho = async () => {
  await nextTick();
  if (mapaVisualizarTrecho.value) {
    mapaVisualizarTrecho.value.renderMapa();
    setTimeout(() => {
      mapaVisualizarTrecho.value.setGeoJson(form_trecho.coordenada, 'red', 6, 'teste');
    }, 500);
  } else {
    console.error('mapaVisualizarTrecho não está definido.');
  }
};

const removerDuplicados = (coordenadas) => {
  if (coordenadas.length <= 1) return coordenadas;

  // Passo 1: Remove duplicatas em qualquer posição
  const coordenadasVistas = new Set();
  const coordenadasUnicas = [];

  for (let i = 0; i < coordenadas.length; i++) {
    const coordStr = JSON.stringify(coordenadas[i]);
    if (!coordenadasVistas.has(coordStr)) {
      coordenadasVistas.add(coordStr);
      coordenadasUnicas.push(coordenadas[i]);
    }
  }

  // Passo 2: Verifica novamente se o primeiro e o último ponto são iguais
  if (coordenadasUnicas.length > 1) {
    const primeiroPonto = coordenadasUnicas[0];
    const ultimoPonto = coordenadasUnicas[coordenadasUnicas.length - 1];
    if (JSON.stringify(primeiroPonto) === JSON.stringify(ultimoPonto)) {
      coordenadasUnicas.pop(); // Remove o último ponto para evitar loop
    }
  }

  return coordenadasUnicas;
};
// Função para concatenar dois GeoJSONs (LineString)
const concatenarGeoJson = (trechoAnterior, novoTrecho) => {
  if (!trechoAnterior || !novoTrecho) {
    return novoTrecho || trechoAnterior || { type: 'LineString', coordinates: [] };
  }

  let trechoAnteriorObj = trechoAnterior;
  if (typeof trechoAnterior[0] === 'string') {
    try {
      trechoAnteriorObj = JSON.parse(trechoAnterior[0]);
    } catch (e) {
      console.error('Erro ao parsear trecho anterior:', e);
      throw new Error('Formato inválido para trecho anterior');
    }
  }

  const novoTrechoObj = typeof novoTrecho === 'string' ? JSON.parse(novoTrecho) : novoTrecho;

  if (trechoAnteriorObj.type !== 'LineString' || novoTrechoObj.type !== 'LineString') {
    console.error('Ambos os trechos devem ser LineString:', { trechoAnteriorObj, novoTrechoObj });
    throw new Error('Ambos os trechos devem ser LineString');
  }

  const coordsAnterior = trechoAnteriorObj.coordinates;
  const coordsNovo = novoTrechoObj.coordinates;

  const primeiroAnterior = coordsAnterior[0];
  const ultimoAnterior = coordsAnterior[coordsAnterior.length - 1];
  const primeiroNovo = coordsNovo[0];
  const ultimoNovo = coordsNovo[coordsNovo.length - 1];

  // Decide se novoTrecho deve vir antes ou depois com base na distância
  const dist1 = getDistancia(ultimoAnterior, primeiroNovo); // concat normal
  const dist2 = getDistancia(ultimoNovo, primeiroAnterior); // concat invertido

  let coordsConcatenadas;
  if (dist1 <= dist2) {
    // concat: anterior + novo
    const mesmoPonto = JSON.stringify(ultimoAnterior) === JSON.stringify(primeiroNovo);
    coordsConcatenadas = [
      ...coordsAnterior,
      ...(mesmoPonto ? coordsNovo.slice(1) : coordsNovo)
    ];
  } else {
    // concat: novo + anterior
    const mesmoPonto = JSON.stringify(ultimoNovo) === JSON.stringify(primeiroAnterior);
    coordsConcatenadas = [
      ...coordsNovo,
      ...(mesmoPonto ? coordsAnterior.slice(1) : coordsAnterior)
    ];
  }

  return {
    type: 'LineString',
    coordinates: removerDuplicados(coordsConcatenadas)
  };
};

// Função auxiliar para distância Euclidiana simples (pode trocar por Haversine se quiser mais precisão)
const getDistancia = (p1, p2) => {
  const dx = p1[0] - p2[0];
  const dy = p1[1] - p2[1];
  return Math.sqrt(dx * dx + dy * dy);
};



const getCoordenadas = async () => {
  const params = {
    uf: form_trecho.uf.uf,
    rodovia: form_trecho.rodovia.rodovia,
    km_inicial: form_trecho.km_inicial,
    km_final: form_trecho.km_final,
    trecho_tipo: form_trecho.tipo_trecho,
    cd_tipo: form_trecho.cd_tipo,
    concatenar: trechosAcumulados.value.length > 0
  };

  try {
    const {data} = await axios.get(route("sgc.gestao.dashboard.geojson", params));
    console.log(data)
    ufsProcessadas.value.add(form_trecho.uf.uf);

    if (params.concatenar) {
      const geojsonConcatenado = concatenarGeoJson(trechosAcumulados.value, data);

      trechosAcumulados.value = [geojsonConcatenado]; 
      form_trecho.coordenada = JSON.stringify(geojsonConcatenado);
    } else {
      trechosAcumulados.value.push(data);
      form_trecho.coordenada = data;
    }

    form_trecho.cod_emp = `${form_trecho.rodovia.rodovia}/${
      Array.from(ufsProcessadas.value).join('+')
    }-${form_trecho.km_inicial}_${form_trecho.km_final}`;
    
    showMap.value = true;
    await visualizarTrecho();

  } catch (error) {
    console.error("Erro ao buscar coordenadas:", error);
    toast.error(error.response?.data?.message || 'Erro ao processar trecho');
  }
};



const storeEmpreendimentos = async () => {
  const parametros = {
    cod_emp: form_trecho.cod_emp  ,
    contrato_est_ambiental: form_trecho.contrato_est_ambiental,
    uf: form_trecho.uf.uf,
    br: form_trecho.rodovia.rodovia,
    km_ini: form_trecho.km_inicial,
    km_fin: form_trecho.km_final,
    br_uf: `${form_trecho.rodovia.rodovia}/${form_trecho.uf.uf}`,
    extensao: form_trecho.km_final - form_trecho.km_inicial,
    ose_sei: form_trecho.ose_sei,
    coordenadas: form_trecho.coordenada
  };

  if (props.empreendimento != null) {
    parametros.id = props.empreendimento.id;
    parametros.cod_emp = form_trecho.cod_emp;
  }

  try {
    const response = await axios.post(route("sgc.gestao.dashboard.empreendimento.store"), parametros);

    if (response) {
      toast.success("Empreendimento cadastrado");

      // Limpar o formulário
      form_trecho.contrato_est_ambiental = null;
      form_trecho.cod_emp = null;
      form_trecho.ose_sei = null;
      form_trecho.uf = null;
      form_trecho.rodovia = null;
      form_trecho.km_inicial = null;
      form_trecho.km_final = null;
      form_trecho.tipo_trecho = 'B';
      form_trecho.coordenada = '';

      showMap.value = false;
    }

  } catch (e) {
    console.error(e);
  }
};

const limparForm = () => {
  form_trecho.contrato_est_ambiental = null;
  form_trecho.cod_emp = null;
  form_trecho.ose_sei = null;
  form_trecho.uf = null;
  form_trecho.rodovia = null;
  form_trecho.km_inicial = null;
  form_trecho.km_final = null;
  form_trecho.tipo_trecho = 'B';
  form_trecho.coordenada = '';

  formKey.value++;

  coordenadasIniciais = [];
  contadorGet = 0;
};



watch(() => form_trecho.uf, () => {
  if (form_trecho.uf) {
    uf_rodovias.value = props.rodovias.filter((rodovia) => {
      return rodovia.estados_id === form_trecho.uf.id;
    });
  } else {
    uf_rodovias.value = [{ rodovia: 'Selecione uma UF' }];
  }
});

onMounted(() => {
  visualizarTrecho();
});

</script>

<template>
  <div>
    <Head :title="`${props.tipo.nome}...`" />
    <AuthenticatedLayout>
      <template #header>
        <div class="w-100 d-flex justify-content-between">
          <Breadcrumb class="align-self-center" :links="[
            { route: '#', label: `Gestão de Contratos - ${props.tipo.nome}` }
          ]" />
        </div>
      </template>
      <Navbar>
        <template #body>
          <div :key="formKey" class="space-y-3 card card-body">
            <div class="row my-4">
              <div class="col-md-4">
                <InputLabel value="Cód empreedimento" for="cod_emp" />
                <input
                  type="text"
                  id="cod_emp"
                  name="cod_emp"
                  class="form-control"
                  v-model="form_trecho.cod_emp"
                  readonly
                  disabled
                />
                <InputError />
              </div>
              <div class="col-md-4">
                <InputLabel value="Contrato" for="contrato_est_ambiental" />
                <input
                  type="text"
                  id="contrato_est_ambiental"
                  name="contrato_est_ambiental"
                  class="form-control"
                  v-model="form_trecho.contrato_est_ambiental"
                />
                <InputError />
              </div>
              <div class="col-md-4">
                <InputLabel value="OSE" for="ose_sei" />
                <input
                  type="text"
                  step="any"
                  id="ose_sei"
                  name="ose_sei"
                  class="form-control"
                  v-model="form_trecho.ose_sei"
                />
                <InputError />
              </div>

              <div class="col-md-4 my-4">
                <InputLabel value="UF" for="ufs" />
                <select
                  name="uf"
                  id="uf"
                  class="form-control form-select"
                  v-model="form_trecho.uf"
                >
                  <option v-for="uf in props.ufs" :key="uf.id" :value="uf">
                    {{ uf.uf }}
                  </option>
                </select>
              </div>

              <div class="col-md-4 my-4">
                <InputLabel value="BR" for="ufs" />
                <select
                  name="brs"
                  id="brs"
                  class="form-control form-select"
                  v-model="form_trecho.rodovia"
                >
                  <option v-for="br in uf_rodovias" :key="br.id" :value="br">
                    {{ br.rodovia }}
                  </option>
                </select>
              </div>

              <div class="col-md-4 my-4">
                <InputLabel value="Tipo de trecho" for="tipo_trecho" />
                <select
                  name="trecho_tipo"
                  id="trecho_tipo"
                  class="form-control form-select"
                  v-model="form_trecho.tipo_trecho"
                >
                  <option value="B">B</option>
                  <option value="U">U</option>
                  <option value="A">A</option>
                  <option value="C">C</option>
                  <option value="N">N</option>
                  <option value="V">V</option>
                </select>
              </div>

              <div class="col-md-4 my-4" v-if="form_trecho.tipo_trecho !== 'B'">
                <InputLabel value="Código do tipo de trecho" for="codigo_tipo_trecho" />
                <select
                  id="codigo_tipo_trecho"
                  name="codigo_tipo_trecho"
                  class="form-control form-select"
                  v-model="form_trecho.cd_tipo"
                >
                  <option value="10">10</option>
                  <option value="20">20</option>
                </select>
              </div>

              <div class="col-md-4 my-4">
                <InputLabel value="Km Inicial" for="km_inicial" />
                <input
                  type="number"
                  step="any"
                  id="km_inicial"
                  name="km_inicial"
                  class="form-control"
                  v-model="form_trecho.km_inicial"
                />
                <InputError />
              </div>

              <div class="col-md-4 my-4">
                <InputLabel value="Km Final" for="km_final" />
                <input
                  type="number"
                  step="any"
                  id="km_final"
                  name="km_final"
                  class="form-control"
                  v-model="form_trecho.km_final"
                />
                <InputError />
              </div>

              <div class="col-md-4 my-4 d-flex flex-column justify-content-end align-items-start">
                <div class="row w-100">
                  <div class="col d-flex justify-content-start">
                    <button @click="getCoordenadas" class="btn btn-primary w-75">
                      Gerar coordenadas
                    </button>
                  </div>
                </div>
              </div>
            </div>
            <div class="table-responsive my-5">
              <div class="form-group col-md-12 mb-4">
                <textarea
                  class="form-control"
                  aria-label="With textarea"
                  v-model="form_trecho.coordenada"
                ></textarea>
              </div>
              <Map ref="mapaVisualizarTrecho" height="400px" width="100%" />
            </div>
            <div class="d-flex justify-content-end">
              <div class="row w-25">
                <div class="col-auto w-50">
                  <button
                    @click="storeEmpreendimentos"
                    type="button"
                    class="btn btn-icon btn-success w-100"
                  >
                    Adicionar <IconPlus />
                  </button>
                </div>
                <div class="col-auto w-50">
                  <button
                    @click="limparForm"
                    type="button"
                    class="btn btn-icon btn-danger w-100"
                  >
                   Limpar <IconX />
                  </button>
                </div>
              </div>
            </div>
          </div>
        </template>
      </Navbar>
    </AuthenticatedLayout>
  </div>
</template>
