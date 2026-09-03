<script setup>
import { defineProps, defineEmits } from 'vue';
import InputLabel from '@/Components/InputLabel.vue';
import NavButton from '@/Components/NavButton.vue';

defineProps({
  formModuloAmostral: {
    type: Array,
    default: () => [],
  },
  subStep: {
    type: Number,
    default: 3,
  },
});

defineEmits(['next', 'prev']);

// Função para formatar a data no formato DD/MM/YYYY
const formatDate = (date) => {
  if (!date) return 'Não informado';
  const [year, month, day] = date.split('-');
  return `${day}/${month}/${year}`;
};
</script>

<template>
  <div class="card">
    <div class="card-body">
      <h4 class="mb-3" style="text-align: center;">MÓDULOS AMOSTRAIS</h4>

      <!-- Tabela de Módulos Amostrais -->
      <div v-if="formModuloAmostral && formModuloAmostral.length > 0" class="table-responsive mb-6">
        <table class="min-w-full bg-white border border-gray-300">
          <thead>
            <tr class="bg-gray-100">
              <th class="py-2 px-4 border-b text-left">ID</th>
              <th class="py-2 px-4 border-b text-left">Data</th>
              <th class="py-2 px-4 border-b text-left">Tamanho do Módulo</th>
              <th class="py-2 px-4 border-b text-left">UF</th>
              <th class="py-2 px-4 border-b text-left">Município</th>
              <th class="py-2 px-4 border-b text-left">Bioma</th>
              <th class="py-2 px-4 border-b text-left">Fitofisionomia</th>
              <th class="py-2 px-4 border-b text-left">Latitude Inicial</th>
              <th class="py-2 px-4 border-b text-left">Longitude Inicial</th>
              <th class="py-2 px-4 border-b text-left">Latitude Final</th>
              <th class="py-2 px-4 border-b text-left">Longitude Final</th>
              <th class="py-2 px-4 border-b text-left">Shapefile</th>
              <th class="py-2 px-4 border-b text-left">Observações</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="modulo in formModuloAmostral" :key="modulo.id" class="hover:bg-gray-50">
              <td class="py-2 px-4 border-b">{{ modulo.id || 'Não informado' }}</td>
              <td class="py-2 px-4 border-b">{{ formatDate(modulo.data_cadastro) }}</td>
              <td class="py-2 px-4 border-b">{{ modulo.tamanho_modulo ? `${modulo.tamanho_modulo}km` : 'Não informado' }}</td>
              <td class="py-2 px-4 border-b">{{ modulo.uf || 'Não informado' }}</td>
              <td class="py-2 px-4 border-b">{{ modulo.municipio || 'Não informado' }}</td>
              <td class="py-2 px-4 border-b">{{ modulo.bioma || 'Não informado' }}</td>
              <td class="py-2 px-4 border-b">{{ modulo.fitofisionomia || 'Não informado' }}</td>
              <td class="py-2 px-4 border-b">{{ modulo.latitude_inicial || 'Não informado' }}</td>
              <td class="py-2 px-4 border-b">{{ modulo.longitude_inicial || 'Não informado' }}</td>
              <td class="py-2 px-4 border-b">{{ modulo.latitude_final || 'Não informado' }}</td>
              <td class="py-2 px-4 border-b">{{ modulo.longitude_final || 'Não informado' }}</td>
              <td class="py-2 px-4 border-b">{{ modulo.arquivo || 'Não informado' }}</td>
              <td class="py-2 px-4 border-b">{{ modulo.obs || 'Não informado' }}</td>
            </tr>
          </tbody>
        </table>
      </div>
      <div v-else class="alert alert-info text-center">
        Nenhum módulo amostral disponível.
      </div>

      <!-- Navegação -->
      <div class="d-flex justify-content-between mt-4">
        <NavButton type="button" type-button="secondary" title="Voltar" @click="$emit('prev')" />
        <NavButton type="button" type-button="primary" title="Avançar" @click="$emit('next')" />
      </div>
      <h4 class="text-center mt-4 font-weight-bold text-muted">3/5</h4>
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
</style>