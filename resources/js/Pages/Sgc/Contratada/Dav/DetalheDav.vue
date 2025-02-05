<script setup>
import Breadcrumb from '@/Components/Breadcrumb.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import Navbar from '../Navbar.vue';
import { onMounted, ref, watch } from 'vue';

const props = defineProps({
  contrato: Object,
  dav: Object,
  subprodutos: Object,
  produtos: Object,
  empreendimentos: Object,
});

const produtoSelecionado = ref("");
const modalVisualizarForm = ref(null);
const profissionais = ref([1]);
const origem = ref([1]);
const destino = ref([1]);
const subprodutosFiltrados = ref([]); 

const formDav = useForm({
  empreendimento: props.dav.empreendimento || "",
  coordenador: props.dav.coordenador || "",
  finalidade: props.dav.finalidade || "",
  escopo: props.dav.escopo || "",
  dataInicio: props.dav.dataInicio || "",
  dataFinal: props.dav.dataFinal || "",
  produto: props.dav.produto || "",
  subproduto: props.dav.subproduto || "",
  origem: props.dav.origem || [""],
  destino: props.dav.destino || [""],
  profissionais: props.dav.profissionais || [""],
  transporte: props.dav.transporte || [],
  status: props.dav.status || "pendente"
});

console.log(formDav.profissionais)

onMounted(() => {
  if (props.dav.produto) {
    produtoSelecionado.value = props.dav.produto;
    subprodutosFiltrados.value = props.subprodutos
      .filter(subProduto => subProduto.subproduto.startsWith(props.dav.produto.split(".")[0] + '.'))
      .map(subProduto => ({
        value: subProduto.subproduto,
        label: `${subProduto.subproduto} ${subProduto.descricao_revisada}`
      }));
  }


});

const opcoesProfissionais = ["Profissional 1", "Profissional 2", "Profissional 3"];

const adicionarProfissional = () => {
  profissionais.value.push(profissionais.value.length + 1);
};

const filterProdutos = () => {
  const produtos = []; // Array para armazenar os subprodutos filtrados

  const listaExclusao = [
    "2", "9", "10", "11", "12", "14"
  ];

  for (const produto of props.produtos) {
    if (!listaExclusao.includes(produto.produto)) {
      const descricaoCompleta = `${produto.produto}.${produto.descricao_produto}`;
      produtos.push(descricaoCompleta); 
    }
  }

  return produtos;
};

const adicionarDestinos = () => {
  if (origem.value.length === destino.value.length) {
    origem.value.push(origem.value.length + 1);
    formDav.origem.push("");
  } else {
    destino.value.push(destino.value.length + 1);
    formDav.destino.push("");
  }
};

watch(produtoSelecionado, (novoValor) => {
  formDav.produto = novoValor;
  if (!novoValor) {
    subprodutosFiltrados.value = [];
    return;
  }

  const numeroProduto = novoValor.split(".")[0];

  subprodutosFiltrados.value = props.subprodutos
    .filter(subProduto => subProduto.subproduto.startsWith(numeroProduto+'.'))
    .map(subProduto => ({
      value: subProduto.subproduto,
      label: `${subProduto.subproduto} ${subProduto.descricao_revisada}`
    }));

  console.log("Subprodutos filtrados:", subprodutosFiltrados.value);
});

const submitForm = () => {
  formDav.dataInicio = new Date(formDav.dataInicio);
  formDav.dataFinal = new Date(formDav.dataFinal);

  formDav.post(route('sgc.gestao.storeDav'), {
    onSuccess: () => {
      formDav.reset();
      modalVisualizarForm.value.getBsModal().hide();
    },
    onError: (errors) => {
      console.error('Erro ao enviar o formulário:', errors);
    }
  });
};

</script>

<template>
    <Head :title="`${contrato.contratada.slice(0, 10)}...`" />
    <AuthenticatedLayout>
      <template #header>
        <div class="w-100 d-flex justify-content-between">
          <Breadcrumb
            class="align-self-center"
            :links="[
              { route: route('sgc.gestao.listagem', contrato.tipo_contrato), label: `Gestão de Contratos` },
              { route: '#', label: contrato.contratada },
              { route: route('sgc.gestao.listagemDav', { id: contrato.id }), label: 'Dav' },
              { route: '#', label: 'Detalhes' },
            ]"
          />
        </div>
      </template>
  
      <Navbar :tipo="contrato">
        <template #body>
            <div class="card">
                <div class="m-5">
                    <form class="row g-3">
                        <!-- Coordenador e Empreendimento -->
                        <div class="col-md-6">
                        <label for="inputCoordenador" class="form-label">Coordenador</label>
                        <input
                            type="text"
                            class="form-control"
                            id="inputCoordenador"
                            v-model="formDav.coordenador"
                            disabled
                        />
                        </div>
                        <div class="col-md-6">
                        <label for="inputEmpreendimento" class="form-label">Empreendimento</label>
                        <select
                            id="inputEmpreendimento"
                            class="form-select"
                            v-model="formDav.empreendimento"
                            disabled
                        >
                            <option selected>...</option>
                            <option v-for="(empreendimento, index) of props.empreendimentos" :key="index">
                            {{ empreendimento.cod_emp }}
                            </option>
                        </select>
                        </div>
            
                        <!-- Produto e Subproduto -->
                        <div class="col-12">
                        <label for="inputProduto" class="form-label">Produto</label>
                        <select
                            id="inputProduto"
                            class="form-select"
                            v-model="produtoSelecionado"
                            disabled
                        >
                            <option selected>...</option>
                            <option v-for="(produto, index) of filterProdutos()" :key="index">
                            {{ produto }}
                            </option>
                        </select>
                        </div>
                        <div class="col-12">
                        <label for="inputSubproduto" class="form-label">Subproduto</label>
                        <select
                            id="inputSubproduto"
                            class="form-select"
                            v-model="formDav.subproduto"
                            disabled
                        >
                            <option selected>...</option>
                            <option v-for="(item, index) of subprodutosFiltrados" :key="index">
                            {{ item.label }}
                            </option>
                        </select>
                        </div>
            
                        <!-- Finalidade e Escopo -->
                        <div class="col-md-6">
                        <label for="inputFinalidade" class="form-label">Finalidade</label>
                        <input
                            type="text"
                            class="form-control"
                            id="inputFinalidade"
                            v-model="formDav.finalidade"
                            disabled
                        />
                        </div>
                        <div class="col-md-6">
                        <label for="inputEscopo" class="form-label">Escopo da Atividade</label>
                        <input
                            type="text"
                            class="form-control"
                            id="inputEscopo"
                            v-model="formDav.escopo"
                            disabled
                        />
                        </div>
            
                        <!-- Datas (Início e Final) -->
                        <div class="col-md-6">
                        <label for="dataInicio" class="form-label">Data início</label>
                        <input
                            type="date"
                            class="form-control"
                            id="dataInicio"
                            v-model="formDav.dataInicio"
                            disabled
                        />
                        </div>
                        <div class="col-md-6">
                        <label for="dataFinal" class="form-label">Data final</label>
                        <input
                            type="date"
                            class="form-control"
                            id="dataFinal"
                            v-model="formDav.dataFinal"
                            disabled
                        />
                        </div>
            
                        <!-- Origem e Destino -->
                        <div class="col-md-6">
                        <label for="inputOrigem" class="form-label">Origem</label>
                        <div v-for="(item, index) in origem" :key="index" class="d-flex align-items-center mb-3">
                            <input
                            type="text"
                            class="form-control me-2"
                            :id="'inputOrigem' + index"
                            v-model="formDav.origem"
                            disabled
                            />
                        </div>
                        </div>
                        <div class="col-md-6">
                        <label for="inputDestino" class="form-label">Destino</label>
                        <div v-for="(item, index) in destino" :key="index" class="d-flex align-items-center mb-3">
                            <input
                            type="text"
                            class="form-control me-2"
                            :id="'inputDestino' + index"
                            v-model="formDav.destino"
                            disabled
                            />
                        </div>
                        </div>
            
                        <!-- Checkboxes organizados em duas colunas -->
                        <div class="col-md-6">
                        <div class="form-check">
                            <input
                            class="form-check-input"
                            type="checkbox"
                            id="gridCheckAereo"
                            value="Aéreo"
                            v-model="formDav.transporte"
                            disabled
                            />
                            <label class="form-check-label" for="gridCheckAereo">Aéreo</label>
                        </div>
                        <div class="form-check">
                            <input
                            class="form-check-input"
                            type="checkbox"
                            id="gridCheckTerrestre"
                            value="Terrestre"
                            v-model="formDav.transporte"
                            disabled
                            />
                            <label class="form-check-label" for="gridCheckTerrestre">Terrestre</label>
                        </div>
                        </div>
                        <div class="col-md-6">
                        <div class="form-check">
                            <input
                            class="form-check-input"
                            type="checkbox"
                            id="gridCheckAquatico"
                            value="Aquático"
                            v-model="formDav.transporte"
                            disabled
                            />
                            <label class="form-check-label" for="gridCheckAquatico">Aquático</label>
                        </div>
                        <div class="form-check">
                            <input
                            class="form-check-input"
                            type="checkbox"
                            id="gridCheckOutros"
                            value="Outros"
                            v-model="formDav.transporte"
                            disabled
                            />
                            <label class="form-check-label" for="gridCheckOutros">Outros</label>
                        </div>
                        </div>
            
                        <!-- Profissionais -->
                        <div class="col-12">
                          <label for="inputProfissionais" class="form-label">Profissionais</label>
                          <div v-for="(profissional, index) in formDav.profissionais" :key="index" class="mb-3 d-flex align-items-center">
                            <select
                              class="form-select me-2"
                              style="flex: 1;"
                              v-model="formDav.profissionais[index]"
                              disabled
                            >
                              <option :value="profissional">{{ `${profissional.nome} - ${profissional.formacao}` }}</option>
                            </select>
                          </div>
                        </div>
                    </form>
                </div>
            </div>
        </template>
      </Navbar>
    </AuthenticatedLayout>
  </template>