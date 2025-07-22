<script setup>
import { defineProps, defineEmits } from 'vue';
import InputLabel from '@/Components/InputLabel.vue';
import NavButton from '@/Components/NavButton.vue';

defineProps({
  formPontosAmostragem: {
    type: Array,
    default: () => [],
  },
  naoSeAplica: {
    type: Boolean,
    default: false,
  },
  subStep: {
    type: Number,
    default: 4,
  },
});

defineEmits(['next', 'prev']);
</script>

<template>
  <div class="card">
    <div class="card-body">
      <h4 class="mb-3" style="text-align: center;">QUELÔNIOS E CROCODILIANOS</h4>

      <div v-if="naoSeAplica" class="alert alert-info text-center">
        Não se aplica.
      </div>
      <div v-else-if="formPontosAmostragem && formPontosAmostragem.length" class="overflow-x-auto mb-6">
        <table class="min-w-full bg-white border border-gray-300">
          <thead>
            <tr class="bg-gray-100">
              <th class="py-2 px-4 border-b text-left">ID</th>
              <th class="py-2 px-4 border-b text-left">Ponto de Coleta</th>
              <th class="py-2 px-4 border-b text-left">Nome do Curso Hídrico</th>
              <th class="py-2 px-4 border-b text-left">Coordenadas</th>
              <th class="py-2 px-4 border-b text-left">Bacia Hidrográfica</th>
              <th class="py-2 px-4 border-b text-left">Profundidade (m)</th>
              <th class="py-2 px-4 border-b text-left">Largura (m)</th>
              <th class="py-2 px-4 border-b text-left">Tipo de Substrato</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="ponto in formPontosAmostragem" :key="ponto.id" class="hover:bg-gray-50">
              <td class="py-2 px-4 border-b">{{ ponto.id || 'Não informado' }}</td>
              <td class="py-2 px-4 border-b">{{ ponto.ponto_de_coleta || 'Não informado' }}</td>
              <td class="py-2 px-4 border-b">{{ ponto.nome_curso_hidrico || 'Não informado' }}</td>
              <td class="py-2 px-4 border-b">{{ ponto.coordenadas || 'Não informado' }}</td>
              <td class="py-2 px-4 border-b">{{ ponto.bacia || 'Não informado' }}</td>
              <td class="py-2 px-4 border-b">{{ ponto.profundidade || 'Não informado' }}</td>
              <td class="py-2 px-4 border-b">{{ ponto.largura || 'Não informado' }}</td>
              <td class="py-2 px-4 border-b">{{ ponto.tipo_substrato || 'Não informado' }}</td>
            </tr>
          </tbody>
        </table>
      </div>
      <div v-else class="alert alert-info text-center">
        Nenhum dado de ponto de amostragem disponível.
      </div>

      <div class="d-flex justify-content-between mt-4">
        <NavButton type="button" type-button="secondary" title="Voltar" @click="$emit('prev')" />
        <NavButton type="button" type-button="primary" title="Avançar" @click="$emit('next')" />
      </div>
      <h4 class="text-center mt-4 font-weight-bold text-muted">4/5</h4>
    </div>
  </div>
</template>

<style scoped>
.card {
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  background-color: #fff;
  margin: 1.5rem;
}
.card-body {
  padding: 2rem;
}
.card-title {
  font-size: 1.75rem;
  font-weight: 700;
  color: #343a40;
  margin-bottom: 1.5rem;
  text-align: center;
}
table {
  border-collapse: collapse;
}
th, td {
  padding: 0.5rem 1rem;
  border: 1px solid #dee2e6;
}
thead {
  background-color: #f8f9fa;
}
tr:hover {
  background-color: #f1f5f9;
}
.alert-info {
  font-size: 1rem;
  padding: 1rem;
  border-radius: 6px;
  background-color: #e7f1ff;
  color: #084298;
}
.text-muted {
  color: #6c757d !important;
}
</style>