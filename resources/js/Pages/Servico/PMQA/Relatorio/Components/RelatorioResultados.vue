<script setup>
import { usePage } from '@inertiajs/vue3';
import { ref } from 'vue';
import { computed } from 'vue';

const props = defineProps({
  relatorio: { type: Object }
})

const parametrosVinculados = computed(() => {
  let parametrosUnicos = new Array();

  parametrosUnicos = props.relatorio.resultado?.campanhas.map(campanha => campanha.pontos.map(ponto => ponto.lista.parametros)).flat(2).reduce((acc, curr) => {
    if (!acc.some(item => item.id === curr.id)) {
      acc.push(curr);
    }
    return acc;
  }, []);

  let parametrosCompletos = [];

  if (parametrosUnicos) {
    parametrosCompletos = parametrosUnicos.map(parametro => {
      let analise = props.relatorio.resultado?.analises.find(analise => analise.parametro_id === parametro.id);
      if (analise) {
        return {
          ...parametro,
          analise,
        };
      }

      return parametro;
    });
  }

  return parametrosCompletos;
});
</script>
<template>
  <div>
    <h4>Resultados</h4>
    <hr>
    <div class="mb-4" v-if="relatorio.resultado?.analise_iqa">
      <h4>IQA</h4>
      <img class="mb-2" :src="usePage().props.app_url + '/storage/' + relatorio.resultado?.analise_iqa?.caminho"
        alt="Gráfico">

      <div>
        <span><strong>Análise: </strong>{{ relatorio.resultado?.analise_iqa?.analise }}</span>
      </div>
    </div>

    <div class="mb-4" v-for="parametro in parametrosVinculados" :key="parametro.id">
      <hr>
      <h4>{{ parametro.nome }}</h4>
      <img class="mb-2" :src="usePage().props.app_url + '/storage/' + parametro.analise?.caminho" alt="Gráfico">

      <div>
        <span><strong>Análise: </strong>{{ parametro.analise?.analise }}</span>
      </div>
    </div>
  </div>
</template>