<script setup>
import { dateTimeFormat } from "@/Utils/DateTimeUtils";

const props = defineProps({
  relatorio: { type: Object }
});
</script>
<template>
  <div>
    <h4>Campanhas</h4>
    <div class="card">
      <div class="table-responsive">
        <table class="table card-table table-bordered">
          <thead>
            <tr>
              <th class="text-center">Nome da campanha</th>
              <th class="text-center">Data de início</th>
              <th class="text-center">Data de fim</th>
              <th class="text-center">Lista de pontos</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="campanha in relatorio.resultado?.campanhas" :key="campanha.id">
              <td class="text-center">{{ campanha.nome }}</td>
              <td class="text-center">{{ dateTimeFormat(campanha.data_inicio) }}</td>
              <td class="text-center">{{ dateTimeFormat(campanha.data_termino) }}</td>
              <td>
                <span v-for="br in campanha.pontos.map(ponto => ponto.nomepontocoleta)" :key="br"
                  class="badge bg-warning text-white m-1">
                  {{ br }}
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>