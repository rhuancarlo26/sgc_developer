<script setup>
import { computed } from "vue";

const props = defineProps({
  servico: { type: Object },
  relatorio: { type: Object }
});

const parametrosVinculados = computed(() => {
  return props.relatorio.resultado?.campanhas.map(campanha => campanha.pontos.map(ponto => ponto.lista.parametros)).flat(2).reduce((acc, curr) => {
    if (!acc.some(item => item.id === curr.id)) {
      acc.push(curr);
    }

    return acc;
  }, []);
})
</script>
<template>
  <div>
    <h4>Parâmetros</h4>
    <div class="card">
      <div class="table-responsive">
        <table class="table card-table table-bordered">
          <thead>
            <tr>
              <th rowspan="3">Parâmetro</th>
              <th rowspan="3">Unidade</th>
              <th colspan="4">Limites - resoluções CONAMA N° 357/2005</th>
            </tr>
            <tr>
              <th colspan="4">Água doce</th>
            </tr>
            <tr>
              <th>Classe 1</th>
              <th>Classe 2</th>
              <th>Classe 3</th>
              <th>Classe 4</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="parametro in parametrosVinculados" :key="parametro.id">
              <td>{{ parametro.nome }}</td>
              <td>{{ parametro.unidade }}</td>
              <td>{{ parametro.classe_1 }}</td>
              <td>{{ parametro.classe_2 }}</td>
              <td>{{ parametro.classe_3 }}</td>
              <td>{{ parametro.classe_4 }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>