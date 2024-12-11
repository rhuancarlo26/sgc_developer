<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
  contrato: Object,
  empreendimentos: Array, 
  empreendimentos2: Object, 
  estudos: Object,
  subprodutos: Object
});

// Função para formatar a situação substituindo # por <br>
const formatarSituacao = (situacao) => {
  return situacao ? situacao.replace(/#/g, '<br><br>') : 'N/A';
};

// Filtra por empreendimento
const codEmp = props.empreendimentos2?.cod_emp;

const empreendimentoFiltrado = computed(() => {
  return props.empreendimentos2 && props.empreendimentos2.cod_emp === codEmp ? props.empreendimentos2 : null;
});

// Exibir os dados
function exibirDados(entidade) {
  return {
    processo: empreendimentoFiltrado.value ? empreendimentoFiltrado.value[`processo_${entidade}`] : 'N/A',
    situacao: empreendimentoFiltrado.value ? empreendimentoFiltrado.value[`situacao_${entidade}`] : 'N/A',
    criticidade: empreendimentoFiltrado.value ? empreendimentoFiltrado.value[`criticidade_${entidade}`] : 'N/A',
    situacaodnit: empreendimentoFiltrado.value ? empreendimentoFiltrado.value[`situacao_${entidade}`] : 'N/A',
  };
}

const dadosIbamaOema = computed(() => exibirDados('ibama_oema'));
const dadosFunai = computed(() => exibirDados('funai'));
const dadosIphan = computed(() => exibirDados('iphan'));
const dadosIncra = computed(() => exibirDados('incra'));
const dadosICMBio = computed(() => exibirDados('icmbio'));
const dadosMS = computed(() => exibirDados('ms'));
const dadosDNIT = computed(() => exibirDados('licenciamento_dnit'));
const dadosDNITsit = computed(() => exibirDados('processo_licenciamento_dnit'));

const dados = {
  dnit: { processo: dadosDNIT.value.processo, situacaodnit: dadosDNITsit.value.situacao },
  ibamaOema: { processo: dadosIbamaOema.value.processo, situacao: dadosIbamaOema.value.situacao, criticidade: dadosIbamaOema.value.criticidade },
  funai: { processo: dadosFunai.value.processo, situacao: dadosFunai.value.situacao, criticidade: dadosFunai.value.criticidade },
  iphan: { processo: dadosIphan.value.processo, situacao: dadosIphan.value.situacao, criticidade: dadosIphan.value.criticidade },
  incra: { processo: dadosIncra.value.processo, situacao: dadosIncra.value.situacao, criticidade: dadosIncra.value.criticidade },
  icmbio: { processo: dadosICMBio.value.processo, situacao: dadosICMBio.value.situacao, criticidade: dadosICMBio.value.criticidade },
  ms: { processo: dadosMS.value.processo, situacao: dadosMS.value.situacao, criticidade: dadosMS.value.criticidade }
};

const organizarCards = computed(() => {
  const cards = [
    { nome: 'DNIT', dados: dados.dnit },
    { nome: 'IBAMA/OEMA', dados: dados.ibamaOema },
    { nome: 'FUNAI', dados: dados.funai },
    { nome: 'IPHAN', dados: dados.iphan },
    { nome: 'INCRA', dados: dados.incra },
    { nome: 'ICMBio', dados: dados.icmbio },
    { nome: 'MS', dados: dados.ms }
  ];

  return cards.sort((a, b) => {
    // Mantém "DNIT" como o primeiro card
    if (a.nome === 'DNIT') return -1;
    if (b.nome === 'DNIT') return 1;

    // Verifica se os cards têm dados preenchidos
    const aHasData = a.dados && (a.dados.processo || a.dados.situacao || a.dados.criticidade);
    const bHasData = b.dados && (b.dados.processo || b.dados.situacao || b.dados.criticidade);

    // Coloca os cards com dados preenchidos antes dos cards sem dados
    if (aHasData && !bHasData) return -1;
    if (!aHasData && bHasData) return 1;

    // Mantém a ordem original se ambos têm ou não têm dados
    return 0;
  });
});
</script>

<template>
  <div class="container mt-4">
    <div class="row">
      <div class="col-md-3 mb-4" v-for="(card, index) in organizarCards.slice(0, 4)" :key="index">
        <div class="card h-100">
          <h3 class="card-title text-center">{{ card.nome }}</h3>
          <div class="card-body">
            <p><strong class="label">PROCESSO:</strong> {{ card.dados.processo }}</p>
            <p>
              <strong class="label">SITUAÇÃO: </strong>
              <span v-html="formatarSituacao(card.dados.situacao || card.dados.situacaodnit)"></span>
            </p>
            <p v-if="card.dados.criticidade"><strong class="label">CRITICIDADE:</strong> {{ card.dados.criticidade }}</p>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-4 mb-4" v-for="(card, index) in organizarCards.slice(4, 7)" :key="index + 4">
        <div class="card h-100">
          <h3 class="card-title text-center">{{ card.nome }}</h3>
          <div class="card-body">
            <p><strong class="label">PROCESSO:</strong> {{ card.dados.processo }}</p>
            <p>
              <strong class="label">SITUAÇÃO:</strong>
              <span v-html="formatarSituacao(card.dados.situacao || card.dados.situacaodnit)"></span>
            </p>
            <p v-if="card.dados.criticidade"><strong class="label">CRITICIDADE:</strong> {{ card.dados.criticidade }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.cards-container {
  background-color: white;
  padding: 20px;
}

.card-row {
  display: flex;
  justify-content: space-between;
  gap: 20px;
  margin-bottom: 20px;
}

.card {
  flex: 1;
  border: 1px solid #bdbdbd;
  padding: 15px;
  background-color: #f8fcff;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
  border-radius: 8px;
}

.card-title {
  font-size: 18px; /* Aumenta o tamanho do título */
  font-weight: bold;
  text-align: center;
  margin-bottom: 15px;
  border-bottom: 1px solid #ddd;
  padding-bottom: 10px;
}

.card-content p {
  font-size: 14px; /* Aumenta o tamanho do texto dentro dos cards */
  margin: 8px 0;
}

.card-content p strong.label {
  font-weight: bold;
  text-transform: uppercase;
}

.card-content {
  margin-top: 10px;
  line-height: 1.7;
}
</style>