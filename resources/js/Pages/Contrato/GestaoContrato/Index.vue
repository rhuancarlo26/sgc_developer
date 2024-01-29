<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import Table from '@/Components/Table.vue';
import Breadcrumb from "@/Components/Breadcrumb.vue";
import ModelSearchForm from "@/Components/ModelSearchForm.vue";
import { IconDots } from "@tabler/icons-vue";
import Modal from "@/Components/Modal.vue";
import { ref } from "vue";
import Map from "@/Components/Map.vue";
import { onMounted } from "vue";
import axios from "axios";
import { nextTick } from "vue";
import { computed } from "vue";

const props = defineProps({
  contratos: Object
})

const modalMapa = ref();
const mapaTrecho = ref();
let coordenadas = ref();

onMounted(() => {
  getGeoJson();
});

const abrirMapa = () => {
  modalMapa.value.getBsModal().show();

  modalMapa.value
    .getElement()
    .addEventListener('shown.bs.modal', () => mapaTrecho.value.renderMapa());

  setTimeout(() => {
    mapaTrecho.value.setLinestrings(trechos.value, true);
  }, 500);
}

const trechos = computed(() => {
  let geojson = [];

  coordenadas.forEach(contrato => {
    contrato.trechos.forEach(trecho => {
      geojson.push([trecho.coordenada, modalTechoMap(contrato, trecho), trecho]);
    });
  });

  return geojson;
})

const getGeoJson = () => {
  axios.get(route('contratos.gestao.get_geo_json')).then(r => {
    coordenadas = r.data;
  });
};

const modalTechoMap = (contrato, trecho) => {
  return `
  <h3>Dados do Trecho</h3>
  <span><strong>Empresa: </strong> ${contrato.contratada}</span><br>
  <span><strong>Contrato: </strong> ${contrato.numero_contrato}</span><br>
  <span><strong>UF: </strong> ${trecho.uf.uf}</span><br>
  <span><strong>BR: </strong> ${trecho.rodovia.rodovia}</span><br>
  <span><strong>Km Inicial: </strong> ${trecho.km_inicial}</span><br>
  <span><strong>Km Final: </strong> ${trecho.km_final}</span><br>
  <span><strong>Tipo: </strong> ${trecho.trecho_tipo}</span>
  `;
}

</script>

<template>
  <Head title="Gestão de Contratos" />

  <AuthenticatedLayout>

    <template #header>
      <div class="w-100 d-flex justify-content-between">
        <Breadcrumb class="align-self-center" :links="[
          { route: '#', label: 'Gestão de Contratos' }
        ]" />
        <div>
          <Link class="btn btn-success me-2" :href="route('contratos.gestao.create')">
          Importar Contrato
          </Link>
          <button @click="abrirMapa()" type="button" class="btn btn-success">Mapa dos Contratos</button>

        </div>
      </div>
    </template>

    <div class="card card-body space-y-3">

      <!-- Pesquisa-->
      <ModelSearchForm :search-columns="{
        'numero_contrato': 'N° do Contrato',
        'cnpj': 'CNPJ',
        'contratada': 'Contratada',
        'processo_sei': 'Processo SEI',
        'situacao': 'Situação',
        'trechos.uf.uf': 'UF',
        'trechos.rodovia.rodovia': 'Rodovia'
      }" />

      <!-- Listagem-->
      <Table :columns="['UF', 'BR', 'N° do Contrato', 'CNPJ', 'Contratada', 'Processo SEI', 'Situação', 'Ação']"
        :records="contratos" table-class="table-hover" :excelRoute="route('contratos.gestao.excel_export')">
        <template #body="{ item }">
          <tr class="cursor-pointer" @click="router.get(route('contratos.gestao.create', item.id))">
            <td class="w-8"><span v-for="uf in item.ufs" :key="uf" class="badge bg-warning text-white m-1">{{ uf }}</span>
            </td>
            <td class="w-8"><span v-for="br in item.brs" :key="br" class="badge bg-warning text-white m-1">{{ br }}</span>
            </td>
            <td>{{ item.numero_contrato }}</td>
            <td>{{ item.cnpj }}</td>
            <td>{{ item.contratada }}</td>
            <td>{{ item.processo_sei }}</td>
            <td>{{ item.situacao?.nome }}</td>
            <td @click.stop>
              <span class="dropdown">
                <button class="btn dropdown-toggle align-text-top" data-bs-boundary="viewport" data-bs-toggle="dropdown"
                  aria-expanded="false">
                  <IconDots />
                </button>
                <div class="dropdown-menu dropdown-menu-end" style="">
                  <a class="dropdown-item" href="#">
                    Visualizar
                  </a>
                </div>
              </span>

            </td>
          </tr>
        </template>
      </Table>
    </div>

    <Modal ref="modalMapa" title="Mapa agluma coisa" modal-dialog-class="modal-xl">
      <template #body>
        <Map ref="mapaTrecho" height="300px" :manual-render="true" />
      </template>
      <template #footer>
        Rodapé
      </template>
    </Modal>

  </AuthenticatedLayout>
</template>