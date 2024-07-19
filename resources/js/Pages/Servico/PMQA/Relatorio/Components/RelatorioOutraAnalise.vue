<script setup>
import { usePage } from "@inertiajs/vue3";

const props = defineProps({
  relatorio: { type: Object }
});

</script>
<template>
  <h4>Outras analises</h4>
  <div class="mb-4" v-for="outraAnalise in relatorio.resultado?.outras_analises" :key="outraAnalise.id">
    <hr>
    <h4>{{ outraAnalise.nome }}</h4>
    <img class="mb-2" :src="usePage().props.app_url + '/storage/' + outraAnalise.caminho" alt="Gráfico">
    <div>
      <span><strong>Análise: </strong>{{ outraAnalise.analise }}</span>
    </div>
  </div>
</template>