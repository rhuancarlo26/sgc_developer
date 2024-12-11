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
        { y: 0, color: '#679eaa' }, // Inicializa com zero, atualizado posteriormente
        { y: 0, color: '#45818e' } 
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
          color: '#8cbbc4'
        },
        {
          name: 'VALOR MEDIDO',
          y: 0, 
          color: 'green'
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
            { y: valorEmpenhado - valorMedicao, color: '#679eaa' },
            { y: chartOptions_emp1.series[0].data[1].y, color: '#45818e' }
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

    chartOptions_radio.annotations[0].labels[0].text = `R$ ${soma_ose.toLocaleString('pt-BR', { minimumFractionDigits: 2 })}`;
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
  <div class="clearfix"><br/><br/></div>
  
  <div class="col-md-12">
    <div class="d-flex justify-content-between">
      <div class="card ms-3" style="flex: 1;">
        <Chart :options="chartOptions_emp1"></Chart>
      </div>
      
      <div class="card ms-3" style="flex: 1; margin-right: 20px; position: relative;">
        <Chart :options="chartOptions_radio"></Chart>
        
        <div 
          style="
            position: absolute; 
            top: 57.5%; 
            left: 50%; 
            transform: translate(-50%, -50%); 
            text-align: center; 
            font-size: 20px; 
            color: #333; 
            pointer-events: none;
          "
        >
          R$ {{ totalR_ose.value.toLocaleString('pt-BR', { minimumFractionDigits: 2 }) }}
          <div style="font-size: 14px; color: #555;">
            Saldo Total de OSE
          </div>
        </div>
        
      </div>
    </div>
  </div>
</template>

    
