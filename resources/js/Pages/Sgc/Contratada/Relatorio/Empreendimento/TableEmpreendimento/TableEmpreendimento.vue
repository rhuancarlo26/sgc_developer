<script setup>
  import { onMounted, reactive, ref } from 'vue';
  import Map from "@/Components/MapSgc.vue";
  import { Chart } from "highcharts-vue";
  
  const props = defineProps({
      contrato: Object,
      empreendimentos: Object,
      estudos: Object,
      subprodutos: Object,
  });
  
  function formatarSegmento(empreendimento) {
    if (!empreendimento) return 'Dados não encontrados';
  
    const segmentos = [];
  
    if (empreendimento.km_ini && empreendimento.km_fin) {
      segmentos.push(`km ${empreendimento.km_ini} ao km ${empreendimento.km_fin}`);
    }
  
    if (empreendimento.km_ini2 && empreendimento.km_fin2) {
      segmentos.push(`km ${empreendimento.km_ini2} ao km ${empreendimento.km_fin2}`);
    }
  
    if (empreendimento.km_ini3 && empreendimento.km_fin3) {
      segmentos.push(`km ${empreendimento.km_ini3} ao km ${empreendimento.km_fin3}`);
    }
  
    return segmentos.length > 0 ? segmentos.join(' / ') : 'Segmento não informado';
  }

  function formatarSubtrecho(empreendimento) {
    if (!empreendimento) return 'Dados não encontrados';
  
    const subtrecho = [];
  
    if (empreendimento.subtrecho_ini && empreendimento.subtrecho_fin) {
      subtrecho.push(`${empreendimento.subtrecho_ini} - ${empreendimento.subtrecho_fin}`);
    }
  
    if (empreendimento.subtrecho_ini2 && empreendimento.subtrecho_fin3) {
      subtrecho.push(`${empreendimento.subtrecho_ini2} - ${empreendimento.subtrecho_fin3}`);
    }
  
    if (empreendimento.subtrecho_ini3 && empreendimento.subtrecho_fin32) {
      subtrecho.push(`${empreendimento.subtrecho_ini3} - ${empreendimento.subtrecho_fin32}`);
    }
  
    return subtrecho.length > 0 ? subtrecho.join(' / ') : 'Segmento não informado';
  }

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
      let soma_ose = 0;
      let soma_medidas = 0;

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

      chartOptionsRadio.series[0].data[0].y = saldoAMedirOSE;
      chartOptionsRadio.series[0].data[1].y = soma_medidas;

      chartOptionsRadio.annotations[0].labels[0].text = `R$ ${soma_ose.toLocaleString('pt-BR', { minimumFractionDigits: 2 })}`;
    });
  };
  
  const coordenadas = props.empreendimentos.map(trecho => trecho.coordenadas);
  const mapaVisualizarTrecho = ref();
  
  let totalR_ose = reactive({ value: 0 });
  let saldoMedir = reactive({ value: 0 });
  let diferenca = reactive({ value: 0 });

  const chartOptionsRadio = reactive({
    chart: {
      type: "pie",
      backgroundColor: "transparent",
      height: 400,
      legend: { enabled: true },
    },
    title: {
      text: "R$ OSE x Medido",
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
  
  const visualizarTrecho = () => {
    mapaVisualizarTrecho.value.renderMapa();
    setTimeout(() => {
      mapaVisualizarTrecho.value.setGeoJson(coordenadas, 'blue', 6, 'teste');
    }, 500);
  };
  
  onMounted(() => {
    visualizarTrecho();
    props.empreendimentos.forEach(empreendimento => {
      empreendimentoTable([empreendimento]);
      const somaSaldoMedir = calcularSomaSaldoMedir(empreendimento.cod_emp);
      saldoMedir.value = somaSaldoMedir;
      diferenca.value = totalR_ose.value - somaSaldoMedir;
    });
  });
  
  defineExpose({ visualizarTrecho });
</script>
  
<template>
  <div class="col-md-12">
    <div class="clearfix mb-4"></div>
    <div class="d-flex justify-content-between">
      <div class="card me-3" style="flex: 1;">
        <Map ref="mapaVisualizarTrecho" height="450px" width="100%"/>
      </div>
      <div class="card ms-3" style="flex: 1;">
        <div v-for="empreendimento in empreendimentos" :key="empreendimento.id">
          <ul class="list-group list-group-flush">
            <li class="list-group-item"><strong>BR/UF:</strong> {{ empreendimento.br }}/{{ empreendimento.uf }}</li>
            <li class="list-group-item"><strong>Subtrecho:</strong> {{ formatarSubtrecho(empreendimento) }}</li>
            <li class="list-group-item"><strong>Segmento:</strong> {{ formatarSegmento(empreendimento) }}</li>
            <li class="list-group-item"><strong>Extensão:</strong> {{ empreendimento.extensao }}</li>
            <li class="list-group-item"><strong>Tipo de Intervenção:</strong> {{ empreendimento.tipo_de_intervencao }}</li>
            <li class="list-group-item"><strong>Descrição:</strong> {{ empreendimento.descricao }}</li>
            <li class="list-group-item"><strong>Bioma:</strong> {{ empreendimento.bioma }}</li>
          </ul>
        </div>
      </div>
      <div v-for="empreendimento in empreendimentos" :key="empreendimento.id" class="card ms-3" style="flex: 1;">
        <h3 class="ms-3">LP</h3>
        <div class="progress mx-3" role="progressbar" aria-label="Progress LP" :aria-valuenow="empreendimento.lp_avanco != '#DIV/0!' ? Number(empreendimento.lp_avanco) * 100 : 0" aria-valuemin="0" aria-valuemax="100">
          <div class="progress-bar progress-bar-striped progress-bar-animated" :style="{ width: (empreendimento.lp_avanco != '#DIV/0!' ? Number(empreendimento.lp_avanco) * 100 : 0) + '%' }">
            {{ empreendimento.lp_avanco != '#DIV/0!' ? (Number(empreendimento.lp_avanco) * 100).toFixed(0) + ' %' : '' }}
          </div>
        </div>
        <h3 class="ms-3">LI</h3>
        <div class="progress mx-3" role="progressbar" aria-label="Progress LI" :aria-valuenow="empreendimento.li_avanco != '#DIV/0!' ? Number(empreendimento.li_avanco) * 100 : 0" aria-valuemin="0" aria-valuemax="100">
          <div class="progress-bar progress-bar-striped progress-bar-animated" :style="{ width: (empreendimento.li_avanco != '#DIV/0!' ? Number(empreendimento.li_avanco) * 100 : 0) + '%' }">
            {{ empreendimento.li_avanco != '#DIV/0!' ? (Number(empreendimento.li_avanco) * 100).toFixed(0) + ' %' : '' }}
          </div>
        </div>
        <div style="position: relative;">
          <Chart :options="chartOptionsRadio"></Chart>
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
    <br>
  </div>
</template>
  
<style>
.progress {
    height: 30px;
    width: 70%;
    margin: 10px 20px;
}

.progress-bar {
    font-size: 1rem;
}

h3 {
    margin: 0 20px;
}
</style>