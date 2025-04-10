<script setup>
import { onMounted, reactive, ref, computed } from 'vue';
import PaginationSgc from '@/Components/PaginationSgc.vue';

const props = defineProps({
  contrato: Object,
  empreendimentos: Object,
  estudos: Object,
  subprodutos: Object
});

const tabelaItens = reactive([]);
const displayedItems = ref([]);
const scrollbarTop = ref(null);
const tableWrapper = ref(null);
const sortColumn = ref('');
const sortOrder = ref('asc');

// Controle de paginação
const currentPage = ref(1);
const itemsPerPage = ref(15);

// Calcular o número total de páginas
const totalPages = computed(() => Math.ceil(tabelaItens.length / itemsPerPage.value));

onMounted(() => {
  criarTabelaItens();

  // Sincronizar a rolagem entre a barra superior e a tabela
  const syncScroll = (source, target) => {
    target.scrollLeft = source.scrollLeft;
  };

  scrollbarTop.value.addEventListener('scroll', () => syncScroll(scrollbarTop.value, tableWrapper.value));
  tableWrapper.value.addEventListener('scroll', () => syncScroll(tableWrapper.value, scrollbarTop.value));

});

const criarTabelaItens = () => {
  tabelaItens.splice(0); 
  props.empreendimentos.forEach(element => {
    props.estudos.forEach(estudo => {
      if (estudo.cod_emp === element.cod_emp) {
        const {
          cod_siac,
          produto,
          item_edital,
          subproduto,
          familia,
          qtd_ose,
          r_ose,
          campo,
          relatorio,
          situacao_dnit,
          situacao_ext,
          avanco,
          req_ext,
          apto_medicao_40,
          medicao_40,
          medicao_40_qtd,
          processo_medicao_40,
          apto_medicao_60,
          medicao_60,
          medicao_60_qtd,
          processo_medicao_60
        } = estudo;

        // Processar 'avanco' para formato percentual
        let avancoPercentual = '';
        const avancoValor = parseFloat(avanco);
        if (!isNaN(avancoValor)) {
          avancoPercentual = (avancoValor * 100).toFixed(2) + '%';
        }

        tabelaItens.push({
          cod_siac,
          produto,
          item_edital,
          subproduto,
          familia,
          qtd_ose,
          r_ose,
          campo,
          relatorio,
          situacao_dnit,
          situacao_ext,
          avanco: avancoPercentual, 
          req_ext,
          apto_medicao_40,
          medicao_40,
          medicao_40_qtd,
          processo_medicao_40,
          apto_medicao_60,
          medicao_60,
          medicao_60_qtd,
          processo_medicao_60
        });
      }
    });
  });

  // Atualiza os itens a serem exibidos na página inicial
  updateDisplayedItems();
};

// Função para ordenar os itens com clique na coluna
const ordenarTabela = (coluna) => {
  if (sortColumn.value === coluna) {
    sortOrder.value = sortOrder.value === 'asc' ? 'desc' : 'asc';
  } else {
    sortColumn.value = coluna;
    sortOrder.value = 'asc';
  }

  tabelaItens.sort((a, b) => {
    const aValue = a[coluna] !== null ? a[coluna] : '';
    const bValue = b[coluna] !== null ? b[coluna] : '';

    if (coluna === 'item_edital') {
      return compararVersoes(aValue, bValue);
    } else if (!isNaN(aValue) && !isNaN(bValue)) {
      return sortOrder.value === 'asc' ? aValue - bValue : bValue - aValue;
    } else {
      return sortOrder.value === 'asc'
        ? aValue.toString().localeCompare(bValue.toString())
        : bValue.toString().localeCompare(aValue.toString());
    }
  });

  updateDisplayedItems(); // Atualiza os itens exibidos após a ordenação
};

// Comparar versões de "item_edital" no formato "3.1.1, 3.2.1.3"
const compararVersoes = (a, b) => {
  const aPartes = a.split('.').map(Number);
  const bPartes = b.split('.').map(Number);

  for (let i = 0; i < Math.max(aPartes.length, bPartes.length); i++) {
    const aParte = aPartes[i] || 0;
    const bParte = bPartes[i] || 0;

    if (aParte !== bParte) {
      return sortOrder.value === 'asc' ? aParte - bParte : bParte - aParte;
    }
  }

  return 0;
};

// Paginação
const handlePageChange = (newPage) => {
  currentPage.value = newPage;
  updateDisplayedItems();
};

const handleItemsPerPageChange = (newItemsPerPage) => {
  itemsPerPage.value = newItemsPerPage;
  currentPage.value = 1; 
  updateDisplayedItems();
};

const updateDisplayedItems = () => {
  const start = (currentPage.value - 1) * itemsPerPage.value;
  const end = start + itemsPerPage.value;
  displayedItems.value = tabelaItens.slice(start, end);
};
</script>

<template>
  <div class="clearfix"><br /><br /></div>

  <div class="col-md-12">
    <!-- Barra de rolagem superior -->
    <div class="scrollbar-top" ref="scrollbarTop" style="overflow-x: auto; height: 15px; position: sticky; top: 0; background-color: white; z-index: 1000;">
      <div style="width: 2800px; height: 1px;"></div>
    </div>

    <div class="table-responsive" ref="tableWrapper" style="overflow-x: auto; background-color: white;">
      <div>
        <table class="table table-bordered" style="min-width: 2000px;">
          <thead>
            <tr>
              <th class="text-center" style="font-size: 12px; cursor: pointer;" @click="ordenarTabela('cod_siac')">Cod SIAC</th>
              <th style="font-size: 12px; cursor: pointer;" @click="ordenarTabela('produto')">Produto</th>
              <th style="font-size: 12px; cursor: pointer; position: sticky; left: 0; background-color: white; z-index: 10;" @click="ordenarTabela('item_edital')">Item Edital</th>
              <th style="font-size: 12px; position: sticky; left: 90px; background-color: white; z-index: 10;" class="subproduto-col text-center" @click="ordenarTabela('subproduto')">Descrição do Subproduto</th>
              <th class="text-center" style="font-size: 12px; cursor: pointer;" @click="ordenarTabela('familia')">Família</th>
              <th class="text-center" style="font-size: 12px; cursor: pointer;" @click="ordenarTabela('qtd_ose')">Qtd OSE</th>
              <th class="text-center" style="font-size: 12px; cursor: pointer;" @click="ordenarTabela('r_ose')">R$ OSE</th>
              <th class="text-center" style="font-size: 12px; cursor: pointer;" @click="ordenarTabela('campo')">Campo</th>
              <th class="text-center" style="font-size: 12px; cursor: pointer;" @click="ordenarTabela('relatorio')">Relatório</th>
              <th class="text-center" style="font-size: 12px; cursor: pointer;" @click="ordenarTabela('situacao_dnit')">Situação DNIT</th>
              <th class="text-center" style="font-size: 12px; cursor: pointer;" @click="ordenarTabela('situacao_ext')">Situação Externo</th>
              <th class="text-center" style="font-size: 12px;">Avanço</th>
              <th class="text-center" style="font-size: 12px; cursor: pointer;" @click="ordenarTabela('req_ext')">Requerimento Externo</th>
              <th class="text-center" style="font-size: 12px; cursor: pointer;" @click="ordenarTabela('apto_medicao_40')">Apto p/ Medição de 40%</th>
              <th class="text-center" style="font-size: 12px;">Nº da Medição Processada 40%</th>
              <th class="text-center" style="font-size: 12px;">Qtd. Considerada na Medição 40%</th>
              <th class="text-center" style="font-size: 12px;">Processo da Medição 40%</th>
              <th class="text-center" style="font-size: 12px; cursor: pointer;" @click="ordenarTabela('apto_medicao_60')">Apto p/ Medição de 60%</th>
              <th class="text-center" style="font-size: 12px;">Nº da Medição Processada 60%</th>
              <th class="text-center" style="font-size: 12px;">Qtd. Considerada na Medição 60%</th>
              <th class="text-center" style="font-size: 12px;">Processo da Medição 60%</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(item, index) in displayedItems" :key="index">
              <td class="text-center">{{ item.cod_siac }}</td>
              <td class="text-center">{{ item.produto }}</td>
              <td class="text-center" style="position: sticky; left: 0; background-color: white;">{{ item.item_edital }}</td>
              <td class="subproduto-col" :title="item.subproduto" style="position: sticky; left: 90px; background-color: white; z-index: 10;">{{ item.subproduto }}</td>
              <td class="text-center">{{ item.familia }}</td>
              <td class="text-center">{{ item.qtd_ose }}</td>
              <td class="text-center">{{ item.r_ose.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }) }}</td>
              <td class="text-center">{{ item.campo }}</td>
              <td class="text-center">{{ item.relatorio }}</td>
              <td class="text-center">{{ item.situacao_dnit }}</td>
              <td class="text-center">{{ item.situacao_ext }}</td>
              <td class="text-center">{{ item.avanco }}</td>
              <td class="text-center">{{ item.req_ext }}</td>
              <td class="text-center">{{ item.apto_medicao_40 }}</td>
              <td class="text-center">{{ item.medicao_40 }}</td>
              <td class="text-center">{{ item.medicao_40_qtd }}</td>
              <td class="text-center">{{ item.processo_medicao_40 }}</td>
              <td class="text-center">{{ item.apto_medicao_60 }}</td>
              <td class="text-center">{{ item.medicao_60 }}</td>
              <td class="text-center">{{ item.medicao_60_qtd }}</td>
              <td class="text-center">{{ item.processo_medicao_60 }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <!-- Componente de Paginação -->
    <PaginationSgc
      :totalItems="tabelaItens.length"
      :itemsPerPageOptions="[15, 20, 50]"
      @pageChanged="handlePageChange"
      @itemsPerPageChanged="handleItemsPerPageChange"
    />
  </div>
</template>

<style scoped>
  /* Barra de rolagem na parte superior*/
  .scrollbar-top::-webkit-scrollbar {
    height: 4px;
  }

  .scrollbar-top::-webkit-scrollbar-thumb {
    background-color: #cccccc;
    border-radius: 4px;
  }

  /* Defina a largura da coluna Subproduto */
  .subproduto-col {
    width: 315px;
    max-width: 315px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  /* Estilo para a coluna "Item Edital" quando fixada */
  th[style*="sticky"],
  td[style*="sticky"] {
    background-color: white; 
    z-index: 10; 
  }

</style>
