<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import Map from "@/Components/Map.vue";
import { useToast } from "vue-toastification";
import LinkConfirmation from "@/Components/LinkConfirmation.vue";
import { ref, watch, computed } from "vue";
import Table from "@/Components/Table.vue";
import { IconTrash, IconDeviceFloppy, IconPlus, IconDots, IconMap } from "@tabler/icons-vue";
import axios from "axios";
import { IconPencil } from "@tabler/icons-vue";
import { IconMapPin } from "@tabler/icons-vue";
import { onMounted } from "vue";
import { IconX } from "@tabler/icons-vue";

const props = defineProps({
  ufs: {
    type: Array
  },
  rodovias: {
    type: Array
  },
  tipos: {
    type: Array
  },
  tipo: {
    type: Object
  },
  situacoes: {
    type: Array
  },
  contrato: {
    type: Object,
    default: null
  }
});


let uf_rodovias = [];
const toast = useToast();
const form = useForm({
  numero_contrato: null,
  tipo_id: null,
  cnpj: null,
  contratada: null,
  objeto: null,
  processo_sei: null,
  data_inicio_vigencia: null,
  data_termino_vigencia: null,
  situacao: null,
  edital: null,
  tipo_licitacao: null,
  modalidade: null,
  unidade_gestora: null,
  fiscal_contrato: null,
  snv: null,
  preco_inicial: null,
  total_aditivo: null,
  total_reajuste: null,
  total: null,
  ...props.contrato
});
const form_trecho = useForm({
  contrato_id: null,
  tipo_id: null,
  uf: {},
  rodovia: {},
  km_inicial: null,
  km_final: null,
  trecho_tipo: null
});

const mapContainer = ref();

onMounted(() => {
  if (props.contrato.id) {
    renderTrecho();
  }
});

watch(
  () => form_trecho.uf,
  () => {
    // Filtra rodovias de acordo com UF selecionada;
    if (form_trecho.uf) {
      uf_rodovias = props.rodovias.filter((rodovia) => {
        return rodovia.uf_id === form_trecho.uf.id;
      });
    } else {
      uf_rodovias = [];
    }
  }
);

watch(
  () => props.contrato.trechos,
  () => {
    renderTrecho();
  }
);

const renderTrecho = () => {
  mapContainer.value.setLinestrings(coordenadas.value, true);
}

const coordenadas = computed(() => {
  return props.contrato.trechos.map(function (objeto) {
    return [objeto.coordenada, modalTechoMap(objeto), objeto];
  });
});

const modalTechoMap = (objeto) => {
  return `
  <span><strong>UF: </strong> ${objeto.uf.uf}</span><br>
  <span><strong>BR: </strong> ${objeto.rodovia.rodovia}</span><br>
  <span><strong>Km Inicial: </strong> ${objeto.km_inicial}</span><br>
  <span><strong>Km Final: </strong> ${objeto.km_final}</span><br>
  <span><strong>Tipo: </strong> ${objeto.trecho_tipo}</span>
  `;
}

const zoomTrecho = (coordenada) => {
  mapContainer.value.zoomToLinestring(coordenada);
}

const zoomFitBounds = () => {
  mapContainer.value.zoomFitBounds();
}

const YYYYmmdd = (data) => {
  var data = new Date(data);
  // Obtém os componentes da data
  var ano = data.getFullYear();
  var mes = (data.getMonth() + 1).toString().padStart(2, '0'); // Mês começa do zero, então adicionamos 1
  var dia = data.getDate().toString().padStart(2, '0');

  // Formata a data como YYYY-mm-dd
  var dataFormatada = ano + '-' + mes + '-' + dia;

  return dataFormatada;
}

const getDadosContrato = () => {
  // Regex para permitir apenas numeros no nr_contrato
  const nr_contrato = form.numero_contrato?.replace(/\D/g, '');

  // Contrato não informado
  if (!nr_contrato) {
    toast.error('Não dados deste contrato na API');
    return;
  }

  // Tenta buscar dados do contrato
  axios.get(route('contratos.get_contrato', nr_contrato))
    .then(response => {

      // Contrato Não Localizado
      if (!response.data.length) {
        toast.error("Servidor fora de serviço");
        return;
      }

      // Armazena retorno do contrato
      toast.success("Contrato encontrado!");
      form.cnpj = response.data[0].NU_CNPJ_CPF;
      form.numero_contrato = response.data[0].NU_CON_FORMATADO;
      form.contratada = response.data[0].NO_EMPRESA;
      form.objeto = response.data[0].DS_OBJETO;
      form.processo_sei = response.data[0].NU_PROCESSO;
      form.data_inicio_vigencia = YYYYmmdd(response.data[0].DT_INICIO);
      form.data_termino_vigencia = YYYYmmdd(response.data[0].DT_TERMINO_VIGENCIA);
      form.situacao = response.data[0].DS_FAS_CONTRATO;
      form.edital = response.data[0].NU_EDITAL;
      form.tipo_licitacao = response.data[0].TIPO_LICITACAO;
      form.edital = response.data[0].NU_EDITAL;
      form.modalidade = response.data[0].MODALIDADE_LICITACAO;
      form.unidade_gestora = response.data[0].SG_UND_GESTORA;
      form.fiscal_contrato = response.data[0].NM_FISCAL;
      form.preco_inicial = response.data[0].Valor_Inicial;
      form.total_aditivo = response.data[0].Valor_Total_de_Aditivos;
      form.total_reajuste = response.data[0].Valor_Total_de_Reajuste;
      form.total = response.data[0].Valor_Inicial_Adit_Reajustes;
    })
    .catch(response => {
      return console.log(response);
    });
}

const salvarContrato = () => {
  form.tipo_id = props.tipo.id;

  form.transform((data) => Object.assign({}, data))

  if (props.contrato.id) {
    form.patch(route('contratos.gestao.atualizar', props.contrato.id));
    return;
  }

  form.post(route('contratos.gestao.store'));
}

const salvarTrecho = () => {
  form_trecho.contrato_id = props.contrato.id;
  form_trecho.tipo_id = props.tipo.id;

  form_trecho.transform((data) => Object.assign({}, data))

  if (form_trecho.id) {
    form_trecho.patch(route('contratos.gestao.update_trecho', form_trecho.id), {
      preserveScroll: true
    });

    return;
  }

  form_trecho.post(route('contratos.gestao.store_trecho'), {
    preserveScroll: true
  });
}

const editarTrecho = (trecho) => {
  Object.assign(form_trecho, trecho)
}

const limparFormTrecho = () => {
  Object.assign(form_trecho, {})
}
</script>

<template>
  <Head title="Gestão de Contratos" />

  <AuthenticatedLayout>

    <template #header>
      <div class="w-100 d-flex justify-content-between">
        <Breadcrumb :links="[
          { route: route('contratos.gestao.listagem', tipo.id), label: 'Gestão de Contratos' },
          { route: '#', label: 'Formulário' }
        ]" />
      </div>
    </template>

    <div class="card mb-4">
      <div class="card">
        <div class="card-header">
          <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs" role="tablist">
            <li class="nav-item" role="presentation">
              <a href="#tabs-home-1" class="nav-link active" data-bs-toggle="tab" aria-selected="true" role="tab">Dados
                Básicos</a>
            </li>
            <li v-if="contrato.id" class="nav-item" role="presentation">
              <a href="#tabs-profile-1" class="nav-link" data-bs-toggle="tab" aria-selected="false" role="tab"
                tabindex="-1">Trechos</a>
            </li>
          </ul>
        </div>
        <div class="card-body">
          <div class="tab-content">
            <div class="tab-pane active show" id="tabs-home-1" role="tabpanel">
              <form @submit.prevent="salvarContrato()" :disabled="form.processing">
                <div class="card-header">
                  <h3 class="my-0">Dados Básicos</h3>
                </div>
                <div class="card-body">
                  <div class="row mb-4">
                    <div class="col">
                      <InputLabel value="Contrato" for="contrato" class="form-label" />
                      <div class="row g-2">
                        <div class="col">
                          <input type="text" id="contrato" name="contrato" class="form-control"
                            v-model="form.numero_contrato" />
                        </div>
                        <div class="col-auto">
                          <button @click="getDadosContrato()" type="button" class="btn btn-primary">
                            Buscar
                          </button>
                        </div>
                      </div>
                    </div>
                    <div class="col">
                      <InputLabel value="Tipo de Contrato" for="tipo" />
                      <input id="tipo" name="tipo" :value="tipo.nome" class="form-control" disabled />
                      <InputError :message="form.errors.tipo" />
                    </div>
                  </div>
                  <div class="row mb-4">
                    <div class="col-4">
                      <InputLabel value="CNPJ" for="cnpj" />
                      <input type="text" id="cnpj" name="cnpj" class="form-control" v-model="form.cnpj" disabled />
                      <InputError :message="form.errors.cnpj" />
                    </div>
                    <div class="col">
                      <InputLabel value="Empresa" for="contratada" />
                      <input type="text" id="contratada" name="contratada" class="form-control" v-model="form.contratada"
                        disabled />
                      <InputError :message="form.errors.contratada" />
                    </div>
                  </div>
                  <div class="row mb-4">
                    <div class="col">
                      <InputLabel value="Objeto do Contrato" for="objeto" />
                      <textarea name="objeto" id="objeto" class="form-control" rows="5" v-model="form.objeto"
                        disabled></textarea>
                      <InputError :message="form.errors.objeto" />
                    </div>
                  </div>
                  <div class="row">
                    <div class="col">
                      <InputLabel value="Numero do Processo" for="processo_sei" />
                      <input type="text" id="processo_sei" name="processo_sei" class="form-control"
                        v-model="form.processo_sei" disabled />
                      <InputError :message="form.errors.processo_sei" />
                    </div>
                    <div class="col">
                      <InputLabel value="Início da Vigência" for="data_inicio_vigencia" />
                      <input type="date" id="data_inicio_vigencia" name="data_inicio_vigencia" class="form-control"
                        v-model="form.data_inicio_vigencia" disabled />
                      <InputError :message="form.errors.data_inicio_vigencia" />
                    </div>
                    <div class="col">
                      <InputLabel value="Término da Vigência" for="data_termino_vigencia" />
                      <input type="date" id="data_termino_vigencia" name="data_termino_vigencia" class="form-control"
                        v-model="form.data_termino_vigencia" disabled />
                      <InputError :message="form.errors.data_termino_vigencia" />
                    </div>
                    <div class="col">
                      <InputLabel value="Situação" for="situacao" />
                      <input type="text" id="situacao" name="situacao" class="form-control" v-model="form.situacao"
                        disabled />
                      <InputError :message="form.errors.situacao" />
                    </div>
                  </div>
                </div>
                <div class="card-header">
                  <h3 class="my-0">Licitação</h3>
                </div>
                <div class="card-body">
                  <div class="row">
                    <div class="col">
                      <InputLabel value="Edital" for="edital" />
                      <input type="text" id="edital" name="edital" class="form-control" v-model="form.edital" disabled />
                      <InputError :message="form.errors.edital" />
                    </div>
                    <div class="col">
                      <InputLabel value="Tipo de Licitação" for="tipo_licitacao" />
                      <input type="text" id="tipo_licitacao" name="tipo_licitacao" class="form-control"
                        v-model="form.tipo_licitacao" disabled />
                      <InputError :message="form.errors.tipo_licitacao" />
                    </div>
                    <div class="col">
                      <InputLabel value="Modalidade" for="modalidade" />
                      <input type="text" id="modalidade" name="modalidade" class="form-control" v-model="form.modalidade"
                        disabled />
                      <InputError :message="form.errors.modalidade" />
                    </div>
                  </div>
                </div>
                <div class="card-header">
                  <h3 class="my-0">Fiscalização</h3>
                </div>
                <div class="card-body">
                  <div class="row">
                    <div class="col">
                      <InputLabel value="Unidade Gestora" for="unidade_gestora" />
                      <input type="text" id="unidade_gestora" name="unidade_gestora" class="form-control"
                        v-model="form.unidade_gestora" disabled />
                      <InputError :message="form.errors.unidade_gestora" />
                    </div>
                    <div class="col">
                      <InputLabel value="Fiscal do Contrato" for="fiscal_contrato" />
                      <input type="text" id="fiscal_contrato" name="fiscal_contrato" class="form-control"
                        v-model="form.fiscal_contrato" disabled />
                      <InputError :message="form.errors.fiscal_contrato" />
                    </div>
                  </div>
                </div>
                <div class="card-header">
                  <h3 class="my-0">Valores</h3>
                </div>
                <div class="card-body">
                  <div class="row">
                    <div class="col">
                      <InputLabel value="SNV" for="snv" />
                      <input type="text" id="snv" name="snv" class="form-control" v-model="form.snv" />
                      <InputError :message="form.errors.snv" />
                    </div>
                    <div class="col">
                      <InputLabel value="Preço Inicial" for="preco_inicial" />
                      <input type="text" id="preco_inicial" name="preco_inicial" class="form-control"
                        v-model="form.preco_inicial" disabled />
                      <InputError :message="form.errors.preco_inicial" />
                    </div>
                    <div class="col">
                      <InputLabel value="Preço Aditivos" for="total_aditivo" />
                      <input type="text" id="total_aditivo" name="total_aditivo" class="form-control"
                        v-model="form.total_aditivo" disabled />
                      <InputError :message="form.errors.total_aditivo" />
                    </div>
                    <div class="col">
                      <InputLabel value="Preço Reajuste" for="total_reajuste" />
                      <input type="text" id="total_reajuste" name="total_reajuste" class="form-control"
                        v-model="form.total_reajuste" disabled />
                      <InputError :message="form.errors.total_reajuste" />
                    </div>
                  </div>
                </div>
                <div class="card-body">
                  <div class="d-flex justify-content-end">
                    <button @click="salvarContrato()" type="button" class="btn btn-primary" :disabled="form.processing">
                      <IconDeviceFloppy class="me-2" />
                      Salvar
                    </button>
                  </div>
                </div>
              </form>
            </div>
            <div class="tab-pane" id="tabs-profile-1" role="tabpanel">
              <div class="card mb-4">
                <form @submit.prevent="salvarTrecho()" :disabled="form_trecho.processing">
                  <div class="card-header">
                    <h3 class="my-0">Trecho</h3>
                  </div>
                  <div class="card-body">
                    <div class="row">
                      <div class="col">
                        <InputLabel value="UF" for="uf" />
                        <select name="uf" id="uf" class="form-control" v-model="form_trecho.uf">
                          <option v-for="uf in ufs" :key="uf.id" :value="uf">{{ uf.uf }}</option>
                        </select>
                        <InputError :message="form_trecho.errors.uf_id" />
                      </div>
                      <div class="col">
                        <InputLabel value="Rodovia" for="rodovia" />
                        <select name="rodovia" id="rodovia" class="form-control" v-model="form_trecho.rodovia">
                          <option v-for="rodovia in uf_rodovias" :key="rodovia.id" :value="rodovia">{{ rodovia.rodovia
                          }}
                          </option>
                        </select>
                        <InputError :message="form_trecho.errors.rodovia_id" />
                      </div>
                      <div class="col">
                        <InputLabel value="Km Inicial" for="km_inicial" />
                        <input type="number" step="any" id="km_inicial" name="km_inicial" class="form-control"
                          v-model="form_trecho.km_inicial" />
                        <InputError :message="form_trecho.errors.km_inicial" />
                      </div>
                      <div class="col">
                        <InputLabel value="Km Final" for="km_final" />
                        <input type="number" step="any" id="km_final" name="km_final" class="form-control"
                          v-model="form_trecho.km_final" />
                        <InputError :message="form_trecho.errors.km_final" />
                      </div>
                      <div class="col">
                        <InputLabel value="Tipo" for="trecho_tipo" />
                        <div class="row g-2">
                          <div class="col">
                            <select name="trecho_tipo" id="trecho_tipo" class="form-control"
                              v-model="form_trecho.trecho_tipo">
                              <option value="B">B</option>
                              <option value="U">U</option>
                              <option value="A">A</option>
                              <option value="C">C</option>
                              <option value="N">N</option>
                              <option value="V">V</option>
                            </select>
                          </div>
                          <div class="col-auto">
                            <button type="submit" class="btn btn-icon btn-success">
                              <IconPlus />
                            </button>
                          </div>
                          <div v-if="form_trecho.id" class="col-auto">
                            <button @click="limparFormTrecho()" type="button" class="btn btn-icon btn-danger">
                              <IconX />
                            </button>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </form>

                <div class="card-body">
                  <div class="table-responsive mb-4">
                    <table class="table card-table table-bordered table-hover">
                      <thead>
                        <tr>
                          <th>UF</th>
                          <th>BR</th>
                          <th>Km Inicial</th>
                          <th>Km Final</th>
                          <th>Tipo</th>
                          <th></th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr v-for="trecho in contrato.trechos" :key="trecho.id" class="cursor-pointer">
                          <td>{{ trecho.uf?.uf }}</td>
                          <td>{{ trecho.rodovia?.rodovia }}</td>
                          <td>{{ trecho.km_inicial }}</td>
                          <td>{{ trecho.km_final }}</td>
                          <td>{{ trecho.trecho_tipo }}</td>
                          <td class="w-1">
                            <div class="d-flex">
                              <button @click="zoomTrecho(trecho.coordenada)" type="button"
                                class="btn btn-icon btn-primary me-2" :disabled="form.processing">
                                <IconMapPin />
                              </button>
                              <button @click="editarTrecho(trecho)" type="button" class="btn btn-icon btn-info me-2"
                                :disabled="form.processing">
                                <IconPencil />
                              </button>
                              <LinkConfirmation v-slot="confirmation"
                                :options="{ text: 'Contratos removidos não podem ser restaurados' }">
                                <Link :onBefore="confirmation.show"
                                  :href="route('contratos.gestao.delete_trecho', [tipo.id, trecho.id])" method="DELETE"
                                  as="button" type="button" class="btn btn-icon btn-danger">
                                <IconTrash />
                                </Link>
                              </LinkConfirmation>
                            </div>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>

              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div v-if="contrato.id">
      <div class="card mb-4">
        <div class="card-header d-flex justify-content-between">
          <h3 class="my-0">Mapa</h3>
          <button @click="zoomFitBounds()" class="btn btn-icon btn-success">
            <IconMap />
          </button>
        </div>
        <div class="card-body">
          <Map ref="mapContainer" :height="'350px'" />
        </div>
      </div>
      <div class="card">
        <div class="card-body">
          <div class="d-flex justify-content-end gap-2">
            <LinkConfirmation v-slot="confirmation" :options="{ text: 'Contratos removidos não podem ser restaurados' }">
              <Link :onBefore="confirmation.show" :href="route('contratos.gestao.delete', contrato.id)" method="DELETE"
                as="button" type="button" class="btn btn-danger">
              <IconTrash />
              Deletar
              </Link>
            </LinkConfirmation>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>