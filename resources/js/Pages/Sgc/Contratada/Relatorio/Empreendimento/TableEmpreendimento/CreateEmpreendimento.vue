<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { nextTick, onMounted, watch, ref } from 'vue';
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import { IconPlus, IconX } from "@tabler/icons-vue";
import { useToast } from "vue-toastification";
import Map from '@/Components/Map.vue';
import axios from 'axios';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import Navbar from '../../../Navbar.vue';


const props = defineProps({
  contrato: Object,
  ufs: Object,
  empreendimento: Object,
  tipo: Object
});

const mapaVisualizarTrecho = ref();
const uf_rodovias = ref([]);
const showMap = ref(false);
const toast = useToast();
let coordenadasIniciais = []; // Mantém as coordenadas acumuladas
let contadorGet = 0; // Contador persistente entre chamadas

const form_trecho = useForm({
  contrato_est_ambiental: props.empreendimento?.contrato_est_ambiental ?? null,
  cod_emp: props.empreendimento?.cod_emp ?? null,
  ose_sei: props.empreendimento?.ose_sei ?? null,
  uf: props.empreendimento?.uf ?? null,
  rodovia: props.empreendimento?.br ?? null,
  km_inicial: props.empreendimento?.km_inicial ?? null,
  km_final: props.empreendimento?.km_final ?? null,
  tipo_trecho: props.empreendimento?.tipo_trecho ?? 'B',
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

const getCoordenadas = async () => {
  const parametros = {
    uf: form_trecho.uf.uf, 
    rodovia: form_trecho.rodovia.rodovia, 
    km_inicial: form_trecho.km_inicial,
    km_final: form_trecho.km_final,
    trecho_tipo: form_trecho.tipo_trecho
  };

  try {

    const retorno = await axios.get(route("sgc.gestao.dashboard.geojson", parametros));
    const novoTrecho = JSON.parse(retorno.data);

    if (contadorGet === 0) {
      coordenadasIniciais.push(novoTrecho);
      form_trecho.cod_emp = `${form_trecho.rodovia.rodovia}/${form_trecho.uf.uf}-${form_trecho.km_inicial}_${form_trecho.km_final}`;

    } else {

      coordenadasIniciais[0].coordinates = coordenadasIniciais[0].coordinates.concat(novoTrecho.coordinates);
      const array = form_trecho.cod_emp.split('-')
      const uf = form_trecho.uf.uf; 

      array.splice(1, 0, uf);

      const resultadoFinal = `${array[0]}/${array[1]}-${array[2]}`;
      form_trecho.cod_emp = resultadoFinal;
    }

    form_trecho.coordenada = JSON.stringify(coordenadasIniciais[0]);

    contadorGet++;

    showMap.value = true;

    await visualizarTrecho();

  } catch (e) {
    console.log(e);
    if (typeof toast !== 'undefined') {
      toast.error('Erro ao buscar as coordenadas');
    } else {
      console.error('Toast não definido. Verifique a implementação.');
    }
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

      // Se showMap foi ativado, ocultar o mapa novamente
      showMap.value = false;
    }

  } catch (e) {
    console.error(e);
    // Lidar com erros
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
    uf_rodovias.value = props.base_rodovia.filter((rodovia) => {
      return rodovia.uf_id === form_trecho.uf.id;
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
                ]"/>
        </div>
      </template>
      <Navbar >

        <template #body>
          <div :key="formKey" class="space-y-3 card card-body">
            <div class="row my-4">
              <!-- Seção do formulário -->
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
                <input type="text" id="contrato_est_ambiental" name="contrato_est_ambiental" class="form-control" v-model="form_trecho.contrato_est_ambiental"/>
                <InputError />
              </div>
              <div class="col-md-4">
                <InputLabel value="OSE" for="ose_sei" />
                <input type="text" step="any" id="ose_sei" name="ose_sei" class="form-control" v-model="form_trecho.ose_sei"/>
                <InputError />
              </div>

              <div class="col-md-4 my-4">
                <InputLabel value="Km Inicial" for="km_inicial" />
                <input type="number" step="any" id="km_inicial" name="km_inicial" class="form-control" v-model="form_trecho.km_inicial"/>
                <InputError />
              </div>

              <div class="col-md-4 my-4">
                <InputLabel value="Km Final" for="km_final" />
                <input type="number" step="any" id="km_final" name="km_final" class="form-control"  v-model="form_trecho.km_final"/>
                <InputError />
              </div>

              <div class="col-md-4 my-4">
                <InputLabel value="Tipo de trecho" for="tipo_trecho" />
                <select name="trecho_tipo" id="trecho_tipo" class="form-control form-select" @change="form_trecho.tipo_trecho = $event.target.value">
                  <option value="B">B</option>
                  <option value="U">U</option>
                  <option value="A">A</option>
                  <option value="C">C</option>
                  <option value="N">N</option>
                  <option value="V">V</option>
                </select>
              </div>
              <div class="col-md-4">
                <InputLabel value="UF" for="ufs" />
                <select name="uf" id="uf" class="form-control form-select" v-model="form_trecho.uf">
                  <option v-for="uf in props.ufs" :key="uf.id" :value="uf">
                    {{ uf.uf }}
                  </option>
                </select>
              </div>
              <div class="col-md-4">
                <div class="row g-2">
                  <div class="col">
                    <InputLabel value="BR" for="ufs" />
                    <select name="brs" id="brs" class="form-control form-select" v-model="form_trecho.rodovia">
                      <option v-for="br in uf_rodovias" :key="br.id" :value="br">
                        {{ br.rodovia }}
                      </option>
                    </select>
                  </div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="row mt-5">
                  <div class="col  d-flex justify-content-end">
                    <button @click="getCoordenadas" class="btn btn-primary w-75">Gerar coordenadas</button>
                  </div>
                </div>
              </div>
            </div>
            <div class="table-responsive my-5">
              <div class="form-group col-md-12 mb-4">
                <textarea class="form-control" aria-label="With textarea" v-model="form_trecho.coordenada"></textarea>
              </div>
              <!-- Renderiza o mapa apenas se showMap for verdadeiro -->
              <Map ref="mapaVisualizarTrecho" height="400px" width="100%"/>
            </div>
            <div class="d-flex justify-content-end">
              <div class="row w-25">
                <div class="col-auto w-50">
                  <button @click="storeEmpreendimentos" type="button" class="btn btn-icon btn-success w-100">
                    <IconPlus />
                  </button>
                </div>
                <div class="col-auto w-50">
                  <button @click="limparForm" type="button" class="btn btn-icon btn-danger w-100">
                    <IconX />
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