<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import { ref, computed, onMounted, reactive } from "vue";
import axios from "axios";
import Navbar from "../Navbar.vue";
import Tabs from "./Tabs.vue";
import ListaModal from "./ListaModal.vue";
import DashboardMap from "@/Components/DashboardMap.vue";
import NavLink from "@/Components/NavLink.vue";
import { IconPencil, IconPlus, IconTrash, IconZoomCheck } from "@tabler/icons-vue";
import SgcLinkConfirmation from "@/Components/SgcLinkConfirmation.vue";
import PaginationSgc from '@/Components/PaginationSgc.vue';

const listaModal = ref();

// Variáveis para ordenação
const sortColumn = ref("");
const sortOrder = ref("asc");

const props = defineProps({
  contrato: Object,
  contratos: Object,
  tipo: Object,
  posts: Object,
  empreendimentos: Object,
  estudos: Object,
  ufs: Array,
  rodovias: Array
});

const mapaVisualizarTrecho = ref();

const filtersEmp = reactive({
  empreendimento: "",
  criticidade: "",
  prioridade: "",
});

let contagemDeItens = ref(0);
let lista = ref([]);
let currentFilter = ref(null);
const listaEmp = ref([]);
const mostraTable = ref(true);

// Função para ordenar a tabela
const ordenarTabela = (coluna) => {
  if (sortColumn.value === coluna) {
    sortOrder.value = sortOrder.value === "asc" ? "desc" : "asc";
  } else {
    sortColumn.value = coluna;
    sortOrder.value = "asc";
  }

  listaEmp.value.sort((a, b) => {
    const aValue = a[coluna] !== null ? a[coluna] : '';
    const bValue = b[coluna] !== null ? b[coluna] : '';

    if (!isNaN(aValue) && !isNaN(bValue)) {
      return sortOrder.value === "asc" ? aValue - bValue : bValue - aValue;
    } else {
      return sortOrder.value === "asc"
        ? aValue.toString().localeCompare(bValue.toString())
        : bValue.toString().localeCompare(aValue.toString());
    }
  });

  updateDisplayedItems();
};

const visualizarTrecho = () => {
  mapaVisualizarTrecho.value.renderMapa();

  setTimeout(() => {
    const empreendimentos = props.empreendimentos.filter(trecho => trecho.coordenadas);
    mapaVisualizarTrecho.value.setGeoJson(empreendimentos, 6);
  }, 500);
};

const submitForm = () => {
  const file = listaModal.value?.$refs?.fileInput?.files[0];
  if (!file) return;

  const formData = new FormData();
  formData.append("excel_file", file);
  formData.append("contrato", 1);

  axios.post(route("sgc.gestao.dashboard.import", { contrato: 1 }), formData, {
    headers: {
      "Content-Type": "multipart/form-data",
    },
  })
    .then((response) => {
      console.log(response.data);
      location.reload();
    })
    .catch((error) => {
      console.error(error);
    });
};

const toggleFilter = (filter) => {
  currentFilter.value = currentFilter.value === filter ? null : filter;

  setTimeout(() => {
    const empreendimentos = props.empreendimentos.filter(trecho => trecho.coordenadas);
    mapaVisualizarTrecho.value.setGeoJson(empreendimentos, 6, currentFilter.value);
  }, 500);
};

onMounted(() => {
  lista.value = props.posts;
  contagemDeItens.value = lista.value.length;
  visualizarTrecho();
  filtrarempreendimentos();
});

defineExpose({ visualizarTrecho });

// Paginação Página Principal
const currentPage = ref(1);
const itemsPerPage = ref(20);
const displayedItems = ref([]);
const totalPages = computed(() => Math.ceil(listaEmp.value.length / itemsPerPage.value));

const handlePageChange = (newPage) => {
  currentPage.value = newPage;
  updateDisplayedItems();
};

const handleItemsPerPageChange = (newItemsPerPage) => {
  itemsPerPage.value = newItemsPerPage;
  currentPage.value = 1;
  updateDisplayedItems();
};

// Atualizar os itens da página atual
const updateDisplayedItems = () => {
  const start = (currentPage.value - 1) * itemsPerPage.value;
  const end = start + itemsPerPage.value;
  displayedItems.value = listaEmp.value.slice(start, end);
};

const filtrarempreendimentos = () => {
  const formData = new FormData();
  formData.append("contrato", 1);
  formData.append("cod_emp", filtersEmp.empreendimento);
  formData.append("criticidade", filtersEmp.criticidade);
  formData.append("prioridade", filtersEmp.prioridade);

  axios.post(route('sgc.gestao.dashboard.searchempreendimentos', { tipo: props.tipo }), formData, {
    headers: {
      "Content-Type": "multipart/form-data",
    },
  })
    .then((response) => {
      listaEmp.value = response.data;
      updateDisplayedItems();
    })
    .catch((error) => {
      console.error(error);
    });
};
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
          <div>
            <Link class="btn btn-info me-2 w-500" :href="route('sgc.gestao.dashboard.empreendimento.create', { tipo: props.tipo.id })">
              Inserir empreendimento <IconPlus />
            </Link>
          </div>
        </div>
      </template>
      <Navbar :tipo="tipo">
        <template #body>
          <div class="space-y-3 card card-body" style="width: 100%; height: auto">
            <Tabs :tabs="[
              { label: 'Empreendimentos', name: 'tab2' },
              { label: 'Upload da Planilha', name: 'tab1' }
            ]">
              <template #tab1>
                <div class="row">
                  <div class="my-3 col-12">
                    <form @submit.prevent="submitForm">
                      <h3>Enviar planilha Excel Atualizada</h3>
                      <h5 class="text-warning">Apenas arquivos *.xlsx</h5>
                      <input type="file" ref="fileInput" accept=".xlsx" class="btn" />
                      <button type="submit" class="m-2 btn btn-secondary">Enviar</button>
                    </form>
                  </div>
                </div>
              </template>
              <template #tab2>
                <div class="row my-5">
                  <div class="col-md-12 mb-3">
                    <h2>Layers</h2>
                    <div class="col-md-6 mb-3">
                      <button type="button" class="btn strong btn-outline-success col-4 mb-2 me-3" :class="{ 'ose-active': currentFilter === 'ose' }" @click="toggleFilter('ose')">
                        OSE
                      </button>
                      <button type="button" class="btn strong btn-outline-danger col-4 mb-2" :class="{ 'nao-ose-active': currentFilter === 's/ose' }" @click="toggleFilter('s/ose')">
                        Não tem OSE
                      </button>
                    </div>
                  </div>
                  <div class="col-md-12 mb-3" v-show="mostraTable">
                    <DashboardMap ref="mapaVisualizarTrecho" height="600px" :manual-render="true" />
                  </div>
                  <div class="col-md-12"><hr /></div>
                  <div class="col-md-12" v-show="mostraTable">
                    <div class="my-4 bg-light row">
                      <form @submit.prevent="filtrarempreendimentos" class="row g-3">
                        <div class="mx-3 col">
                          <label class="form-label">Empreendimento</label>
                          <input class="form-control" v-model="filtersEmp.empreendimento" placeholder="Empreendimento" />
                        </div>
                        <div class="mx-3 col">
                          <label class="form-label">Criticidade</label>
                          <select class="form-control form-select" v-model="filtersEmp.criticidade">
                            <option class="form-option" value="criticidade_ibama_oema">IBAMA/OEMA</option>
                            <option class="form-option" value="criticidade_funai">FUNAI</option>
                            <option class="form-option" value="criticidade_iphan">IPHAN</option>
                            <option class="form-option" value="criticidade_incra">INCRA</option>
                          </select>
                        </div>
                        <div class="mx-3 col">
                          <label class="form-label">Prioridade</label>
                          <select class="form-control form-select" v-model="filtersEmp.prioridade">
                            <option class="form-option" value="Normal">Todas as opções</option>
                            <option class="form-option" value="Normal">Normal</option>
                            <option class="form-option" value="Alta">Alta</option>
                          </select>
                        </div>
                        <div class="mx-3 col-12">
                          <button class="btn btn-secondary" type="submit">Pesquisar</button>
                        </div>
                      </form>
                      <div class="clearfix"><br /></div>
                    </div>
                    <div class="table-responsive mb-4">
                      <table class="table card-table table-bordered table-hover">
                        <thead>
                          <tr style="text-align: center;">
                            <th @click="ordenarTabela('cod_emp')" style="cursor: pointer; vertical-align: middle;">Empreendimento</th>
                            <th @click="ordenarTabela('br_uf')" style="cursor: pointer; vertical-align: middle;">BR/UF</th>
                            <th @click="ordenarTabela('km_ini')" style="cursor: pointer; vertical-align: middle;">Km Inicial</th>
                            <th @click="ordenarTabela('km_fin')" style="cursor: pointer; vertical-align: middle;">Km Final</th>
                            <th @click="ordenarTabela('tipo_de_intervencao')" style="cursor: pointer; vertical-align: middle;">Tipo de Intervenção</th>
                            <th @click="ordenarTabela('lp_avanco')" style="cursor: pointer; vertical-align: middle;">LP Avanço</th>
                            <th @click="ordenarTabela('li_avanco')" style="cursor: pointer; vertical-align: middle;">LI Avanço</th>
                            <th @click="ordenarTabela('criticidade_ibama_oema')" style="cursor: pointer; vertical-align: middle;">Criticidade</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr style="text-align: center;" v-for="trecho in displayedItems" :key="trecho.cod_emp">
                            <td style="vertical-align: middle;">{{ trecho.cod_emp }}</td>
                            <td style="vertical-align: middle;">{{ trecho.br_uf }}</td>
                            <td style="vertical-align: middle;">{{ trecho.km_ini }}</td>
                            <td style="vertical-align: middle;">{{ trecho.km_fin }}</td>
                            <td style="vertical-align: middle;">{{ trecho.tipo_de_intervencao }}</td>
                            <td style="vertical-align: middle;">{{ trecho.lp_avanco != "#DIV/0!" ? (Number(trecho.lp_avanco) * 100).toFixed(0) + ' %' : '' }}</td>
                            <td style="vertical-align: middle;">{{ trecho.li_avanco != "#DIV/0!" ? (Number(trecho.li_avanco) * 100).toFixed(0) + ' %' : '' }}</td>
                            <td style="vertical-align: middle;">
                              {{ trecho.criticidade_ibama_oema ? `IBMA/OEMA: ${trecho.criticidade_ibama_oema}` : '' }}
                              <br v-if="trecho.criticidade_ibama_oema" />
                              {{ trecho.criticidade_funai ? `FUNAI: ${trecho.criticidade_funai}` : '' }}
                              <br v-if="trecho.criticidade_funai" />
                              {{ trecho.criticidade_iphan ? `IPHAN: ${trecho.criticidade_iphan}` : '' }}
                              <br v-if="trecho.criticidade_iphan" />
                              {{ trecho.criticidade_incra ? `INCRA: ${trecho.criticidade_incra}` : '' }}
                              <br v-if="trecho.criticidade_incra" />
                              {{ trecho.criticidade_icmbio ? `ICMBio: ${trecho.criticidade_icmbio}` : '' }}
                              <br v-if="trecho.criticidade_icmbio" />
                              {{ trecho.criticidade_ms ? `MS: ${trecho.criticidade_ms}` : '' }}
                              <br v-if="trecho.criticidade_ms" />
                            </td>
                            <td style="display: flex; align-items: center; justify-content: center; gap: 2rem;" class="p-4 text-center">
                              <span>
                                  <NavLink class="list-unstyled" route-name="sgc.gestao.dashboard.empreendimento.index" :param="{ tipo: props.tipo , empreendimento: trecho.id}" :icon="IconZoomCheck" />
                              </span>
                              <SgcLinkConfirmation v-slot="confirmation"
                                :options="{ text: `Deseja excluir o empreendimento ${trecho.cod_emp}` }">
                                <Link
                                :onBefore="() => confirmation.show(
                                      { url: route('sgc.gestao.dashboard.empreendimento.delete', trecho.id), method: 'DELETE' },
                                      () => window.location.reload()
                                  )"
                                  class="text-danger">
                                  <IconTrash />
                                </Link>
                              </SgcLinkConfirmation>
                              <a class="list-unstyled text-warning" :href="route('sgc.gestao.dashboard.empreendimento.create', [tipo.id, trecho.id])">
                                <IconPencil />
                              </a>
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                    <PaginationSgc
                      :totalItems="listaEmp.length"
                      :itemsPerPageOptions="[20, 30, 50]"
                      @pageChanged="handlePageChange"
                      @itemsPerPageChanged="handleItemsPerPageChange"
                    />
                  </div>
                </div>
              </template>
            </Tabs>
          </div>
          <ListaModal ref="listaModal" href="javascript:void(0)" />
        </template>
      </Navbar>
    </AuthenticatedLayout>
  </div>
</template>

<style scoped>
.bg-light {
  background-color: rgba(0, 0, 0, 0.05) !important;
}

.ose-active {
  background-color: #28a745;
  color: white;
}

.nao-ose-active {
  background-color: #dc3545;
  color: white;
}
</style>
