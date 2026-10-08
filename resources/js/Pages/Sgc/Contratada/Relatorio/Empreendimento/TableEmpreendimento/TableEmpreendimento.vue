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
    });
  };
  
  const coordenadas = props.empreendimentos.map(trecho => trecho.coordenadas);
  const mapaVisualizarTrecho = ref();
  
  let totalR_ose = reactive({ value: 0 });
  let saldoMedir = reactive({ value: 0 });
  let diferenca = reactive({ value: 0 });

  const formatarMoeda = valor => new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(valor);
  const chartOptionsRadio = reactive({
    chart: { type: 'pie', backgroundColor: 'transparent', height: 250, spacing: [8, 8, 8, 8] },
    title: { text: null },
    credits: { enabled: false },
    accessibility: { enabled: false },
    plotOptions: { pie: { innerSize: '72%', dataLabels: { enabled: false }, borderWidth: 2, showInLegend: false } },
    series: [{ name: 'Valor', data: [
      { name: 'Saldo a medir da OSE', y: 0, color: '#3b82f6' },
      { name: 'Valor medido', y: 0, color: '#16a34a' },
    ] }],
    tooltip: { formatter() { return this.point.name + ': <b>' + formatarMoeda(this.y) + '</b>'; } },
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
    <div class="empreendimento-resumo-grid">
      <div class="card resumo-card">
        <Map ref="mapaVisualizarTrecho" height="450px" width="100%"/>
      </div>
      <div class="card resumo-card">
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
      <div v-for="empreendimento in empreendimentos" :key="empreendimento.id" class="card resumo-card">
        <section class="financeiro-resumo">
          <h3 class="financeiro-titulo">R$ OSE x Medido</h3>
          <div class="financeiro-total"><span>Total da OSE</span><strong>{{ formatarMoeda(totalR_ose.value) }}</strong></div>
          <Chart v-if="totalR_ose.value > 0" :options="chartOptionsRadio" />
          <p v-else class="financeiro-vazio">Sem valores de OSE para exibir.</p>
          <div class="financeiro-legenda">
            <div v-for="item in chartOptionsRadio.series[0].data" :key="item.name" class="financeiro-item">
              <span class="financeiro-rotulo"><i :style="{ backgroundColor: item.color }" aria-hidden="true"></i>{{ item.name }}</span>
              <strong>{{ formatarMoeda(item.y) }}</strong>
            </div>
          </div>
        </section>
      </div>
    </div>
    <br>
  </div>
</template>
  
<style scoped>
.empreendimento-resumo-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 16px; }
.resumo-card { min-width: 0; border-color: #e2e8f0; border-radius: 8px; overflow: hidden; }
.financeiro-resumo { padding: 18px; }
.financeiro-titulo { margin: 0 0 16px; font-size: 1rem; font-weight: 600; text-align: center; color: #334155; }
.financeiro-total { text-align: center; }
.financeiro-total span { display: block; color: #64748b; font-size: .85rem; }
.financeiro-total strong { display: block; margin-top: 4px; font-size: 1.35rem; color: #0f172a; overflow-wrap: anywhere; }
.financeiro-legenda { border-top: 1px solid #e2e8f0; padding-top: 12px; }
.financeiro-item { display: flex; flex-direction: column; gap: 4px; padding: 8px 0; }
.financeiro-rotulo { display: flex; align-items: center; gap: 8px; color: #475569; font-size: .85rem; }
.financeiro-rotulo i { width: 10px; height: 10px; flex-shrink: 0; border-radius: 3px; }
.financeiro-item strong { padding-left: 18px; color: #334155; font-weight: 600; }
.financeiro-vazio { padding: 40px 0; text-align: center; color: #64748b; }
@media (max-width: 991px) { .empreendimento-resumo-grid { grid-template-columns: 1fr; } }
</style>
