<script setup>
import { ref, watch } from 'vue';
import Modal from '@/Components/Modal.vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
  subprodutos: Object,
  produtos: Object,
  empreendimentos: Object,
  contrato: Object,
  profissionais: Object
})


const produtoSelecionado = ref("");
const modalVisualizarForm = ref(null);
const profissionais = ref([1]);
const origem = ref([1]);
const destino = ref([1]);
const subprodutosFiltrados = ref([]); 

const formDav = useForm({
  contrato_id: props.contrato.id,
  empreendimento: "",
  coordenador: "",
  finalidade: "",
  escopo: "",
  dataInicio: "",
  dataFinal: "",
  produto: "",
  subproduto: "",
  origem: [],
  destino: [],
  profissionais: [],
  transporte: [],
  status: "pendente"
})

const opcoesProfissionais = [];

props.profissionais.map(profissional => (
  opcoesProfissionais.push(profissional)
))



const adicionarProfissional = () => {
  profissionais.value.push(profissionais.value.length + 1);
};

const filterProdutos = () => {
  const produtos = []; 

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

const abrirModal = () => {
  modalVisualizarForm.value.getBsModal().show();
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

  console.log("Dados antes do envio:", formDav.data()); 

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

defineExpose({ abrirModal });

</script>

<template>
  <Modal ref="modalVisualizarForm" modal-dialog-class="modal-xl">
    <template #body>
      <form class="row g-3" @submit.prevent="submitForm">
        <div class="col-md-6">
          <label for="inputCoordenador" class="form-label">Coordenador</label>
          <input type="text" class="form-control" id="inputCoordenador" v-model="formDav.coordenador">
        </div>
        <div class="col-md-6">
          <label for="inputEmpreendimento" class="form-label">Empreendimento</label>
          <select id="inputEmpreendimento" class="form-select" v-model="formDav.empreendimento">
            <option selected >...</option>
            <option v-for="(empreendimento, index) of props.empreendimentos" :key="index">
              {{ empreendimento.cod_emp }}
            </option>
          </select>
        </div>

        <div class="col-12">
          <label for="inputProduto" class="form-label">Produto</label>
          <select id="inputProduto" class="form-select" v-model="produtoSelecionado">
            <option selected>...</option>
            <option v-for="(produto, index) of filterProdutos()" :key="index">
              {{ produto }}
            </option>
          </select>
        </div>
        <div class="col-12">
          <label for="inputSubproduto" class="form-label">Subproduto</label>
            <select id="inputSubproduto" class="form-select" v-model="formDav.subproduto">
              <option selected>...</option>
              <option v-for="(item, index) of subprodutosFiltrados" :key="index" >
                {{ item.label }}
              </option>
            </select>
        </div>

        <div class="col-md-6">
          <label for="inputFinalidade" class="form-label">Finalidade</label>
          <input type="text" class="form-control" id="inputFinalidade" v-model="formDav.finalidade">
        </div>
        <div class="col-md-6">
          <label for="inputEscopo" class="form-label">Escopo da Atividade</label>
          <input type="text" class="form-control" id="inputEscopo" v-model="formDav.escopo">
        </div>

        <div class="col-md-6">
          <label for="dataInicio" class="form-label">Data início</label>
          <input type="date" class="form-control" id="dataInicio" v-model="formDav.dataInicio"/>
        </div>
        <div class="col-md-6">
          <label for="dataFinal" class="form-label">Data final</label>
          <input type="date" class="form-control" id="dataFinal" v-model="formDav.dataFinal"/>
        </div>

        <div class="col-md-6">
          <label for="inputOrigem" class="form-label">Origem</label>
          <div v-for="(item, index) in origem" :key="index" class="d-flex align-items-center mb-3">
            <input type="text" class="form-control me-2" :id="'inputOrigem' + index" v-model="formDav.origem[index]">
            <button
              type="button"
              class="btn btn-success btn-md"
              @click="adicionarDestinos"
              v-if="index === origem.length - 1"
            >
              +
            </button>
          </div>
        </div>
        <div class="col-md-6">
          <label for="inputDestino" class="form-label">Destino</label>
          <div v-for="(item, index) in destino" :key="index" class="d-flex align-items-center mb-3">
            <input type="text" class="form-control me-2" :id="'inputDestino' + index" v-model="formDav.destino[index]">
            <button
              type="button"
              class="btn btn-success btn-md"
              @click="adicionarDestinos"
              v-if="index === destino.length - 1"
            >
              +
            </button>
          </div>
        </div>

        <div class="col-md-6">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="gridCheckAereo"
              value="Aéreo" v-model="formDav.transporte">
            <label class="form-check-label" for="gridCheckAereo">Aéreo</label>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="gridCheckTerrestre"
              value="Terrestre" v-model="formDav.transporte">
            <label class="form-check-label" for="gridCheckTerrestre">Terrestre</label>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="gridCheckAquatico"
              value="Aquático" v-model="formDav.transporte">
            <label class="form-check-label" for="gridCheckAquatico">Aquático</label>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="gridCheckOutros"
              value="Outros" v-model="formDav.transporte">
            <label class="form-check-label" for="gridCheckOutros">Outros</label>
          </div>
        </div>

        <div class="col-12">
          <label for="inputProfissionais" class="form-label">Profissionais</label>
          <div v-for="(profissional, index) in profissionais" :key="index" class="mb-3 d-flex align-items-center">
            <select
              class="form-select me-2"
              style="flex: 1;"
              v-model="formDav.profissionais[index]"
            >
              <option :value="null">Selecione um profissional...</option>
              <option
                v-for="(opcao, opcaoIndex) in opcoesProfissionais"
                :key="opcaoIndex"
                :value="{ nome: opcao.nome, formacao: opcao.formacao }"
              >
                {{ `${opcao.nome} - ${opcao.formacao}` }}
              </option>
            </select>
            <button
              type="button"
              class="btn btn-success btn-md"
              @click="adicionarProfissional"
              v-if="index === profissionais.length - 1"
            >
              +
            </button>
          </div>
        </div>

        <div class="col-12">
          <button type="submit" class="btn btn-primary">Salvar</button>
        </div>
      </form>
    </template>
  </Modal>
</template>