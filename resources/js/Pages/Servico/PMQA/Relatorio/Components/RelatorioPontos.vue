<script setup>
import { computed } from "vue";

const props = defineProps({
  servico: { type: Object },
  relatorio: { type: Object }
});

const pontosVinculados = computed(() => {
  return props.relatorio.resultado?.campanhas?.map(campanha => campanha.pontos).flat().reduce((acc, curr) => {
    if (!acc.some(item => item.id === curr.id)) {
      acc.push(curr);
    }

    return acc;
  }, []);
});
</script>
<template>
  <div>
    <h4>Pontos</h4>
    <div class="card">
      <div class="table-responsive">
        <table class="table card-table table-bordered">
          <thead>
            <tr>
              <th class="text-center">Cod. ponto</th>
              <th class="text-center">Pt. coleta</th>
              <th class="text-center">Latitude</th>
              <th class="text-center">Longitude</th>
              <th class="text-center">Classificação</th>
              <th class="text-center">Classe</th>
              <th class="text-center">Tipo de ambiente</th>
              <th class="text-center">UF</th>
              <th class="text-center">Municipio</th>
              <th class="text-center">Bacia hidrografica</th>
              <th class="text-center">Km rodovia</th>
              <th class="text-center">Estaca</th>
              <th class="text-center">Observações</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="ponto in pontosVinculados" :key="ponto.id">
              <td class="text-center">{{ ponto.id }}</td>
              <td class="text-center">{{ ponto.nomepontocoleta }}</td>
              <td class="text-center">{{ ponto.lat_x }}</td>
              <td class="text-center">{{ ponto.long_y }}</td>
              <td class="text-center">{{ ponto.classificacao }}</td>
              <td class="text-center">{{ ponto.classe }}</td>
              <td class="text-center">{{ ponto.tipoambiente }}</td>
              <td class="text-center">{{ ponto.uf }}</td>
              <td class="text-center">{{ ponto.municipio }}</td>
              <td class="text-center">{{ ponto.baciahidrografica }}</td>
              <td class="text-center">{{ ponto.km_rodovia }}</td>
              <td class="text-center">{{ ponto.estaca }}</td>
              <td class="text-center">{{ ponto.observacoes }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>