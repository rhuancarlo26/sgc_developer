<script setup>
import { onMounted, reactive } from 'vue';
import { Chart } from "highcharts-vue";

const props = defineProps({
    contrato: Object,
    empreendimentos: Object,
    estudos: Object,
    subprodutos: Object
});

const chartOptions_emp1 = reactive({
  chart: {
    type: "bar",
    backgroundColor: "transparent",
    textColor: "red",
  },
  title: {
    text: "SALDO DE EMPENHO E A MEDIR:",
    align: "left",
    margin: 40,
  },
  xAxis: {
    categories: ["Saldo de Empenho", "Saldo a Medir OSE:"], 
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
        { y: 0, color: '#64748b' }, // Inicializa com zero, atualizado posteriormente
        { y: 0, color: '#3b82f6' }
      ],
    },
  ]
});

const chartOptions_radio = reactive({
  chart: {
    type: "pie",
    backgroundColor: "transparent",
    height: 400,
    legend: { enabled: true },
  },
  title: {
    text: "EXECUÇÃO DO ESTUDO AMBIENTAL",
    align: "left",
    color: "white",
    margin: 40,
  },
  plotOptions: {
    pie: {
      innerSize: '80%', 
      startAngle: 20,
      dataLabels: {
        enabled: true,
        distance: 30, 
        format: '{point.name}: R$ {point.y:,.2f}', 
        style: {
          fontSize: "12px",
          fontFamily: 'Arial, sans-serif',
          color: "#000",
          fontWeight: 'normal'
        }
      }
    },
  },
  series: [
    {
      data: [
        {
          name: 'SALDO A MEDIR DA OSE:',
          y: 0, 
          color: '#3b82f6'
        },
        {
          name: 'VALOR MEDIDO',
          y: 0, 
          color: '#16a34a'
        }
      ],
    },
  ],
  tooltip: {
    enabled: false 
  },
  annotations: [
    {
      labels: [{
        point: {
          xAxis: 0,
          yAxis: 0,
          x: 0,
          y: 0
        },
        text: 'R$ 0,00', 
        style: {
          fontSize: '16px',
          color: '#000',
          fontWeight: 'bold'
        },
        align: 'center',
        verticalAlign: 'middle',
        x: 0,
        y: 0
      }]
    }
  ]
});

const moeda = valor => new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(valor);
const tooltipFinanceiro = { formatter() { return this.point.name || this.key ? String(this.point.name || this.key) + ': <b>' + moeda(this.y) + '</b>' : moeda(this.y); } };
chartOptions_emp1.title = { text: null };
chartOptions_emp1.chart.height = 280;
chartOptions_emp1.legend = { enabled: false };
chartOptions_emp1.credits = { enabled: false };
chartOptions_emp1.tooltip = tooltipFinanceiro;
chartOptions_emp1.xAxis.categories = ['Saldo de empenho', 'Saldo a medir da OSE'];
chartOptions_emp1.yAxis.title = { text: null };
chartOptions_emp1.yAxis.tickAmount = 4;
chartOptions_emp1.yAxis.gridLineColor = '#e2e8f0';
chartOptions_emp1.yAxis.labels = { formatter() { return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL', notation: 'compact', maximumFractionDigits: 1 }).format(this.value); } };
chartOptions_emp1.plotOptions.series.dataLabels = { enabled: false };
chartOptions_emp1.plotOptions.series.borderRadius = 4;
chartOptions_emp1.plotOptions.series.pointWidth = 24;
chartOptions_radio.title = { text: null };
chartOptions_radio.chart.height = 260;
chartOptions_radio.credits = { enabled: false };
chartOptions_radio.plotOptions.pie.innerSize = '72%';
chartOptions_radio.plotOptions.pie.dataLabels = { enabled: false };
chartOptions_radio.series[0].data[0].name = 'Saldo a medir da OSE';
chartOptions_radio.series[0].data[1].name = 'Valor medido';
chartOptions_radio.tooltip = tooltipFinanceiro;
delete chartOptions_radio.annotations;

let totalR_ose = reactive({ value: 0 });
let saldoMedir = reactive({ value: 0 });
let diferenca = reactive({ value: 0 });

const fetchSaldoEmpenho = async (numeroContrato) => {
  try {
    const response = await fetch(`https://servicos.dnit.gov.br/DPP/api/contrato/dnit/${numeroContrato}`);
    if (!response.ok) {
      throw new Error('Erro na requisição da API');
    }

    const data = await response.json();
    console.log('Dados da API:', data); 

    // Acessa os valores dentro do array `data`
    if (data.data && data.data.length > 0) {
      const contrato = data.data[0];
      const valorEmpenhado = contrato.VALOR_EMPENHADO || 0;
      const valorMedicao = contrato.VALOR_MEDICAO_PI_R || 0;

      chartOptions_emp1.series[0].data[0].y = valorEmpenhado - valorMedicao;

      // Força a reatividade para atualização
      chartOptions_emp1.series = [
        {
          name: 'Valores',
          data: [
            { y: valorEmpenhado - valorMedicao, color: '#64748b' },
            { y: chartOptions_emp1.series[0].data[1].y, color: '#3b82f6' }
          ],
        },
      ];
    } else {
      console.warn('Nenhum dado encontrado na resposta da API.');
    }

  } catch (error) {
    console.error('Erro ao buscar dados da API:', error);
  }
};

const formatarContratoParaApi = (contratoEstAmbiental) => {
  // Extrai o número do contrato e formata para o padrão da API
  const numeroContrato = contratoEstAmbiental.replace('/', '').padStart(11, '0');
  return numeroContrato;
};

const calcularSomaSaldoMedir = (codEmp) => {
  let somaSaldoMedir = 0;

  props.estudos.forEach(estudo => {
    if (estudo.cod_emp === codEmp) {
      const subprodutoRelacionado = props.subprodutos.find(subproduto => subproduto.subproduto === estudo.item_edital);

      if (subprodutoRelacionado) {
        const medicaoTotal = Number(estudo.medicao_40_qtd) + Number(estudo.medicao_60_qtd);
        somaSaldoMedir += medicaoTotal * Number(subprodutoRelacionado.r_preco_unitario);
      }
    }
  });

  return somaSaldoMedir;
};

const empreendimentoTable = (emp) => {
  emp.forEach(element => {
    var soma_ose = 0;
    var soma_medidas = 0;

    props.estudos.forEach(value => {
      if (value.cod_emp == element.cod_emp) {
        soma_ose += Number(value.r_ose);

        const subprodutoRelacionado = props.subprodutos.find(subproduto => subproduto.subproduto === value.item_edital);

        if (subprodutoRelacionado) {
          soma_medidas += (Number(value.medicao_40_qtd) + Number(value.medicao_60_qtd)) * Number(subprodutoRelacionado.r_preco_unitario);
        }
      }
    });

    totalR_ose.value = soma_ose;

    const saldoAMedirOSE = soma_ose - soma_medidas;

    chartOptions_radio.series[0].data[0].y = saldoAMedirOSE;
    chartOptions_radio.series[0].data[1].y = soma_medidas;

    chartOptions_emp1.series[0].data[1].y = saldoAMedirOSE;
  });
};

onMounted(async () => {
  for (const empreendimento of props.empreendimentos) {
    const numeroContrato = formatarContratoParaApi(empreendimento.contrato_est_ambiental);
    await fetchSaldoEmpenho(numeroContrato);
  }

  props.empreendimentos.forEach(empreendimento => {
    empreendimentoTable([empreendimento]);

    const somaSaldoMedir = calcularSomaSaldoMedir(empreendimento.cod_emp);
    saldoMedir.value = somaSaldoMedir;

    diferenca.value = totalR_ose.value - saldoMedir.value;
  });
});
</script>

<template>
  <div class="financeiro-grid">
    <section class="card financeiro-card">
      <h3>Saldo de empenho e a medir</h3>
      <Chart :options="chartOptions_emp1" />
      <div class="financeiro-valores">
        <div v-for="(item, indice) in chartOptions_emp1.series[0].data" :key="indice">
          <span><i :style="{ backgroundColor: item.color }" aria-hidden="true"></i>{{ chartOptions_emp1.xAxis.categories[indice] }}</span>
          <strong>{{ moeda(item.y) }}</strong>
        </div>
      </div>
    </section>
    <section class="card financeiro-card">
      <h3>Execução do estudo ambiental</h3>
      <div class="financeiro-total"><span>Total da OSE</span><strong>{{ moeda(totalR_ose.value) }}</strong></div>
      <Chart :options="chartOptions_radio" />
      <div class="financeiro-valores">
        <div v-for="item in chartOptions_radio.series[0].data" :key="item.name">
          <span><i :style="{ backgroundColor: item.color }" aria-hidden="true"></i>{{ item.name }}</span>
          <strong>{{ moeda(item.y) }}</strong>
        </div>
      </div>
    </section>
  </div>
</template>
<style scoped>
.financeiro-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px; padding-top: 20px; }
.financeiro-card { min-width: 0; padding: 18px; border: 1px solid #e2e8f0; border-radius: 8px; box-shadow: none; }
.financeiro-card h3 { font-size: 1rem; font-weight: 600; text-align: center; color: #334155; margin: 0 0 18px; }
.financeiro-total { text-align: center; }
.financeiro-total span { display: block; font-size: .85rem; color: #64748b; }
.financeiro-total strong { display: block; font-size: 1.35rem; color: #0f172a; margin-top: 4px; }
.financeiro-valores { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px; border-top: 1px solid #e2e8f0; padding-top: 14px; margin-top: auto; }
.financeiro-valores span { display: flex; align-items: center; gap: 8px; font-size: .85rem; color: #475569; }
.financeiro-valores i { width: 10px; height: 10px; flex-shrink: 0; border-radius: 3px; }
.financeiro-valores strong { display: block; margin-top: 6px; color: #334155; font-weight: 600; overflow-wrap: anywhere; }
@media (max-width: 991px) { .financeiro-grid { grid-template-columns: 1fr; } }
@media (max-width: 575px) { .financeiro-valores { grid-template-columns: 1fr; } }
</style>
