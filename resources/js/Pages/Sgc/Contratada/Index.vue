<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import Navbar from "./Navbar.vue";
import { Chart } from "highcharts-vue";
import { onMounted, reactive, ref, computed } from "vue";
import PaginationSgc from '@/Components/PaginationSgc.vue';
import { IconClipboardData } from "@tabler/icons-vue";

const props = defineProps({
  contrato: Object,
  empreendimentos: Object,
  estudos: Object,
  subprodutos: Object
});

console.log(props.subprodutos[0]);




// Configuração do gráfico
const chartOptions_radio = reactive({
  chart: {
    type: "bar",
    backgroundColor: "transparent",
    height: 500,
  },
  title: {
    text: "Contrato x R$-OSE x Medido",
    align: "left",
    margin: 40,
  },
  xAxis: {
    categories: ["R$-OSE", "Medido", "Contrato"], 
  },
  yAxis: {
    min: 0,
    title: {
      text: "Valores",
    },
    labels: {
      formatter: function () {
        return 'R$ ' + this.value.toLocaleString('pt-BR', { minimumFractionDigits: 2 });
      }
    },
  },
  plotOptions: {
    series: {
      allowPointSelect: true,
      cursor: "pointer",
      dataLabels: {
        enabled: true,
        formatter: function () {
          return 'R$ ' + this.y.toLocaleString('pt-BR', { minimumFractionDigits: 2 });
        },
        style: {
          fontFamily: 'Arial, sans-serif',
          fontSize: '12px',
          color: '#000',
        }
      }
    },
  },
  series: [
    {
      name: 'Valores',
      data: [
        { y: 6737143.32, color: '#679eaa' },
        { y: 0, color: '#45818e' } 
      ],
    },
  ]
});

// Função de tabela para o gráfico
const empreendimentoTable = (emp) => {
  emp.forEach(element => {
    let soma_ose = 0;
    let soma_medidas = 0;
    let valor_total = 0;

    props.estudos.forEach((value) => {
      soma_ose += Number(value.r_ose);
      soma_medidas += (Number(value.medicao_40_qtd) + Number(value.medicao_60_qtd)) * Number(value.qtd_ose);
      valor_total = props.contrato.total;
    });

    // chartOptions_radio.series[0].data = [
    //   { name: 'Valor Medido', y: Number(soma_medidas.toFixed(0)), color: '#8cbbc4' },
    //   { name: 'R$ OSE', y: Number(soma_ose.toFixed(0)), color: '#46aabd' },
    //   { name: 'Total contrato', y: Number(valor_total.toFixed(0)), color: '#037c91' }
    // ];
  });
};

// Controle e dados da tabela
const tabelaItens = reactive([]);
const displayedItems = ref([]);
const currentPage = ref(1);
const itemsPerPage = ref(15);

const totalPages = computed(() => Math.ceil(tabelaItens.length / itemsPerPage.value));

// Sincronização da rolagem
const scrollbarTop = ref(null);
const tableWrapper = ref(null);
const syncScroll = (source, target) => {
  target.scrollLeft = source.scrollLeft;
};

const verificarScroll = () => {
  const tabela = tableWrapper.value;
  return tabela && tabela.scrollWidth > tabela.clientWidth;
};

// Funções de criação e atualização da tabela
onMounted(() => {
  empreendimentoTable(props.empreendimentos);
  criarTabelaItens();
  
  scrollbarTop.value.addEventListener('scroll', () => syncScroll(scrollbarTop.value, tableWrapper.value));
  tableWrapper.value.addEventListener('scroll', () => syncScroll(tableWrapper.value, scrollbarTop.value));
});

const criarTabelaItens = () => {
  props.subprodutos.forEach(subproduto => {
    const {
      cod_siac, contrato, descricao_revisada, descricao_siac, etapa, familia, prazo_de_elaboracao,
      produto, qtd_contrato, qtd_medido, qtd_ose, qtd_saldo_medido, qtd_saldo_ose, r_medido, r_ose
    } = subproduto;

    tabelaItens.push({
      cod_siac, contrato, descricao_revisada, descricao_siac, etapa, familia, prazo_de_elaboracao,
      produto, qtd_contrato, qtd_medido, qtd_ose, qtd_saldo_medido, qtd_saldo_ose, r_medido, r_ose
    });
  });

  updateDisplayedItems();
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
  <Head :title="`${contrato.contratada.slice(0, 10)}...`" />

  <AuthenticatedLayout>
    <template #header>
      <div class="w-100 d-flex justify-content-between">
        <Breadcrumb class="align-self-center" :links="[ 
          { route: route('contratos.gestao.listagem', contrato.tipo_contrato), label: `Gestão de Contratos` },
          { route: '#', label: contrato.contratada }
        ]" />
        <div>
          <Link class="btn btn-info me-2 w-500"  :href="route('sgc.contratada.relatorios.index', {contrato: contrato.id})">
            <IconClipboardData class="me-2" /> Relatorio de coordenação
          </Link>
        </div>
      </div>
    </template>

    <Navbar :tipo="contrato">

      <template #body>        
        <div class="card">
          <div class="card-body p-0">
            <Chart :options="chartOptions_radio"></Chart>
          </div>
        </div>

        <!-- Tabela e Paginação -->
        <div class="card mt-4">
          <div class="col-md-12">
            <!-- Barra de rolagem superior -->
            <div class="scrollbar-top" ref="scrollbarTop" style="overflow-x: auto; height: 15px; position: sticky; top: 0; background-color: white; z-index: 1000;">
              <div style="width: 2800px; height: 1px;"></div>
            </div>

            <!-- Tabela com rolagem horizontal -->
            <div class="table-responsive" ref="tableWrapper" style="overflow-x: auto; background-color: white;">
              <table class="table table-bordered" style="min-width: 1240px;"> 
                <thead class="text-center">
                  <tr>
                    <th class="text-center" style="font-size: 12px;">Cod SIAC</th>
                    <th class="text-center" style="font-size: 12px;">Contrato</th>
                    <th class="text-center" style="font-size: 12px;">Descrição Siac</th>
                    <th class="text-center" style="font-size: 12px;">Descrição revisada</th>
                    <th class="text-center" style="font-size: 12px;">produto</th>
                    <th class="text-center" style="font-size: 12px;">Etapa</th>
                    <th class="text-center" style="font-size: 12px;">familia</th>
                    <th class="text-center" style="font-size: 12px;">Prazo de elaboração</th>
                    <th class="text-center" style="font-size: 12px;">qtd contrato</th>
                    <th class="text-center" style="font-size: 12px;">qtd medido</th>
                    <th class="text-center" style="font-size: 12px;">qtd ose</th>
                    <th class="text-center" style="font-size: 12px;">saldo medido</th>
                    <th class="text-center" style="font-size: 12px;">saldo ose</th>
                    <th class="text-center" style="font-size: 12px;">medido</th>
                    <th class="text-center" style="font-size: 12px;">ose</th>
                  </tr>
                </thead>
                <tbody class="text-center">
                  <tr v-for="(item, index) in displayedItems" :key="index">
                    <td class="align-content-center">{{ item.cod_siac }}</td>
                    <td class="align-content-center">{{ item.contrato }}</td>
                    <td class="align-content-center format-col" :title="item.descricao_siac">{{ item.descricao_siac }}</td>
                    <td class="align-content-center format-col" :title="item.descricao_revisada">{{ item.descricao_revisada }}</td>
                    <td class="align-content-center">{{ item.produto }}</td>
                    <td class="align-content-center">{{ item.etapa }}</td>
                    <td class="align-content-center">{{ item.familia }}</td>
                    <td class="align-content-center">{{ item.prazo_de_elaboracao }}</td>
                    <td class="align-content-center">{{ item.qtd_contrato }}</td>
                    <td class="align-content-center">{{ item.qtd_medido }}</td>
                    <td class="align-content-center">{{ item.qtd_ose }}</td>
                    <td class="align-content-center">{{ item.qtd_saldo_medido }}</td>
                    <td class="align-content-center">{{ item.qtd_saldo_ose }}</td>
                    <td class="align-content-center">{{ new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(item.r_medido) }}</td>
                    <td class="align-content-center">{{ new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(item.r_ose) }}</td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- Paginação -->
            <PaginationSgc
              :totalItems="tabelaItens.length"
              :itemsPerPageOptions="[15, 20, 50]"
              @pageChanged="handlePageChange"
              @itemsPerPageChanged="handleItemsPerPageChange"
            />
          </div>
        </div>
      </template>
    </Navbar>
  </AuthenticatedLayout>
</template>

<style scoped>
  .format-col {
    width: 315px;
    max-width: 315px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    cursor: pointer;
  }
</style>
