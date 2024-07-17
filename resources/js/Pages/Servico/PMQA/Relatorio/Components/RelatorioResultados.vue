<script setup>
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
    <div class="mb-4" v-for="parametro in parametrosVinculados" :key="parametro.id">
      <hr>
      <h4>{{ parametro.nome }}</h4>

      <div>
        <span><strong>Análise: </strong>{{ parametro.analise?.analise }}</span>
      </div>
    </div>
  </div>
</template>