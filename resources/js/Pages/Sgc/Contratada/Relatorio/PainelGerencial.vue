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
import LinkConfirmation from "@/Components/LinkConfirmation.vue";
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
  ose_sei: "",
});

let contagemDeItens = ref(0);
let lista = ref([]);
let currentFilter = ref(null);
const listaEmp = ref([]);
const mostraTable = ref(true);

// Redireciona ao clicar na linha ou na lupa
const acessarEmpreendimento = (trecho) => {
  window.location.href = route('sgc.gestao.dashboard.empreendimento.index', {
    tipo: props.tipo,
    empreendimento: trecho.id,
  });
};

// Contagem de Estudos em Andamento 
const estudosEmAndamento = computed(() => {
  return props.empreendimentos.filter(emp => emp.ose_sei && emp.ose_sei.trim() !== '').length;
});

// Contagem de Estudos no Total 
const estudosTotal = computed(() => {
  return props.empreendimentos.filter(emp => emp.contrato_est_ambiental && emp.contrato_est_ambiental.trim() !== '').length;
});

const formatarData = (data) => {
  if (!data) return ''; 
  const dataObj = new Date(data);
  if (isNaN(dataObj)) return data; 
  const dia = String(dataObj.getDate()).padStart(2, '0');
  const mes = String(dataObj.getMonth() + 1).padStart(2, '0');
  const ano = dataObj.getFullYear();
  return `${dia}/${mes}/${ano}`;
};

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

// Valores únicos de contrato_est_ambiental para o dropdown
const valoresContratoEstAmbiental = computed(() => {
  const valores = props.empreendimentos
    .map(emp => emp.contrato_est_ambiental)
    .filter(val => val && val.trim() !== ''); 
  return [...new Set(valores)];
});

const contratoEstAmbientalSelecionado = ref(null);

const atualizarFiltroContratoEstAmbiental = () => {
  setTimeout(() => {
    let empreendimentosFiltrados = props.empreendimentos.filter(trecho => trecho.coordenadas);

    if (contratoEstAmbientalSelecionado.value) {
      empreendimentosFiltrados = empreendimentosFiltrados.filter(
        trecho => trecho.contrato_est_ambiental === contratoEstAmbientalSelecionado.value
      );
    }

    if (currentFilter.value === 'ose') {
      empreendimentosFiltrados = empreendimentosFiltrados.filter(
        trecho => trecho.ose_sei && trecho.ose_sei.trim() !== ''
      );
    } else if (currentFilter.value === 's/ose') {
      empreendimentosFiltrados = empreendimentosFiltrados.filter(
        trecho => !trecho.ose_sei || trecho.ose_sei.trim() === ''
      );
    }

    mapaVisualizarTrecho.value.setGeoJson(empreendimentosFiltrados, 6);
  }, 500);
};

const toggleFilter = (filter) => {
  currentFilter.value = currentFilter.value === filter ? null : filter;
  atualizarFiltroContratoEstAmbiental();
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
  formData.append("ose_sei", filtersEmp.ose_sei);

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

                  <div class="col-md-12 mb-3" v-show="mostraTable">
                    <div class="d-flex">
                      <div>
                        <DashboardMap ref="mapaVisualizarTrecho" height="600px" width="1300px" :manual-render="true" />
                      </div>
                      <div class="ms-3" style="flex: 1;">
                        <div class="col-md-12 mb-3" style="text-align: center;">
                          <h2>Layers</h2>
                        </div>
                        <div style="text-align: center;">
                          <button type="button" class="btn strong btn-outline-success col-4 mb-2 me-3" :class="{ 'ose-active': currentFilter === 'ose' }" @click="toggleFilter('ose')">
                            OSE
                          </button>
                          <button type="button" class="btn strong btn-outline-danger col-4 mb-2" :class="{ 'nao-ose-active': currentFilter === 's/ose' }" @click="toggleFilter('s/ose')">
                            Não tem OSE
                          </button>
                        </div>
                        <!-- Dropdown para filtro de contrato_est_ambiental -->
                        <div style="text-align: center; margin-top: 20px;">
                          <select v-model="contratoEstAmbientalSelecionado" @change="atualizarFiltroContratoEstAmbiental" class="form-control" style="width: 200px; margin: 0 auto;">
                            <option :value="null" >CT de Estudos Ambientais</option>
                            <option
                              v-for="valor in valoresContratoEstAmbiental"
                              :key="valor"
                              :value="valor"
                            >
                              {{ valor }}
                            </option>
                          </select>
                        </div>

                        <!-- <div style="text-align: center; margin-top: 20px;">
                          <h3><strong>Número de Estudos em Andamento:</strong> {{ estudosEmAndamento }}</h3>
                          <h3><strong>Número de Estudos no Total:</strong> {{ estudosTotal }}</h3>
                        </div> -->

                        <div style="text-align: center; margin-top: 50px;">
                          <div class="card text-black bg-light mb-3" style="max-width: 18rem; margin: 0 auto;">
                            <div class="card-header">Número de Estudos em Andamento</div>
                            <div class="card-body">
                              <h3 class="card-text">{{ estudosEmAndamento }}</h3>
                            </div>
                          </div>
                          <div class="card text-gray bg-light mb-3" style="max-width: 18rem; margin: 0 auto;">
                            <div class="card-header">Número de Estudos no Total</div>
                            <div class="card-body">
                              <h3 class="card-text">{{ estudosTotal }}</h3>
                            </div>
                          </div>
                        </div>

                      </div>
                    </div>
                  </div>

                  <div class="col-md-12"><hr /></div>
                  <div class="col-md-12" v-show="mostraTable">
                    <div>
                    <Link class="btn btn-info me-2 w-500" :href="route('sgc.gestao.dashboard.empreendimento.create', { tipo: props.tipo.id })">
                      Inserir empreendimento <IconPlus />
                    </Link>
                  </div>
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
                        <div class="mx-3 col">
                          <label class="form-label">OSE SEI</label>
                          <input class="form-control" v-model="filtersEmp.ose_sei" placeholder="" />
                        </div>
                        <div class="mx-3 col-12">
                          <button class="btn btn-secondary" type="submit">Pesquisar</button>
                        </div>
                      </form>
                      <div class="clearfix"><br /></div>
                    </div>
                    <div class="table-responsive mb-4">
                      <table class="table card-table table-bordered">
                        <thead>
                          <tr>
                            <th @click="ordenarTabela('cod_emp')" style="cursor: pointer">Empreendimento</th>
                            <th @click="ordenarTabela('br_uf')" style="cursor: pointer">BR/UF</th>
                            <th @click="ordenarTabela('km_ini')" style="cursor: pointer">Km Inicial</th>
                            <th @click="ordenarTabela('km_fin')" style="cursor: pointer">Km Final</th>
                            <th @click="ordenarTabela('tipo_de_intervencao')" style="cursor: pointer">Tipo de Intervenção</th>
                            <th @click="ordenarTabela('lp_avanco')" style="cursor: pointer">LP Avanço</th>
                            <th @click="ordenarTabela('li_avanco')" style="cursor: pointer">LI Avanço</th>
                            <th @click="ordenarTabela('criticidade_ibama_oema')" style="cursor: pointer">Criticidade</th>
                            <th @click="ordenarTabela('ose_sei')" style="cursor: pointer">OSE Sei</th>
                            <th @click="ordenarTabela('ose_data')" style="cursor: pointer">OSE data</th>
                            <th class="acao">Ação</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr
                            v-for="trecho in displayedItems"
                            :key="trecho.cod_emp"
                            @click="acessarEmpreendimento(trecho)"
                            style="cursor: pointer"
                          >
                            <td>{{ trecho.cod_emp }}</td>
                            <td>{{ trecho.br_uf }}</td>
                            <td>{{ trecho.km_ini }}</td>
                            <td>{{ trecho.km_fin }}</td>
                            <td>{{ trecho.tipo_de_intervencao }}</td>
                            <td>{{ trecho.lp_avanco != "#DIV/0!" ? (Number(trecho.lp_avanco) * 100).toFixed(0) + ' %' : '' }}</td>
                            <td>{{ trecho.li_avanco != "#DIV/0!" ? (Number(trecho.li_avanco) * 100).toFixed(0) + ' %' : '' }}</td>
                            <td>
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
                            <td>{{ trecho.ose_sei }}</td>
                            <td>{{ formatarData(trecho.ose_data) }}</td>
                            <td class="acao d-inline-flex align-items-center gap-2 pe-0 text-center">
                              <span @click.stop="acessarEmpreendimento(trecho)">
                                <NavLink
                                  class="list-unstyled"
                                  route-name="sgc.gestao.dashboard.empreendimento.index"
                                  :param="{ tipo: props.tipo, empreendimento: trecho.id }"
                                  :icon="IconZoomCheck"
                                />
                              </span>
                              <LinkConfirmation v-slot="confirmation" :options="{ text: `Deseja excluir o empreendimento ${trecho.cod_emp}` }">
                                <Link
                                  :onBefore="confirmation.show"
                                  :href="route('sgc.gestao.dashboard.empreendimento.delete', trecho.id)"
                                  method="DELETE"
                                  class="text-danger"
                                  @click.stop
                                >
                                  <IconTrash />
                                </Link>
                              </LinkConfirmation>
                              <a
                                class="list-unstyled text-warning"
                                :href="route('sgc.gestao.dashboard.empreendimento.create', [tipo.id, trecho.id])"
                                @click.stop
                              >
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

tbody tr:hover td:not(.acao) {
  background-color: #f5f5f5; 
}

</style>
