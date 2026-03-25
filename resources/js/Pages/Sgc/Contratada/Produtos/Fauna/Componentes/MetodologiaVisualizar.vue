<script setup>
import { defineProps, defineEmits } from 'vue';
import InputLabel from '@/Components/InputLabel.vue';
import NavButton from '@/Components/NavButton.vue';

defineProps({
  formMetodologia: {
    type: Array, // Alterado de Object para Array
    default: () => [], // Padrão é um array vazio
  },
});

defineEmits(['next', 'prev']);
</script>

<template>
  <div class="card">
    <div class="card-body">
      <h4 class="mb-3" style="text-align: center;">METODOLOGIA</h4>

      <!-- Dados da Metodologia -->
      <div v-if="formMetodologia && formMetodologia.length" class="mb-6">
        <div v-for="(metodologia, index) in formMetodologia" :key="index" class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
          <div>
            <InputLabel :value="'GRUPO FAUNÍSTICO ' + (index + 1)" :for="'grupo_faunistico_' + index" />
            <input
              type="text"
              class="form-control"
              :id="'grupo_faunistico_' + index"
              :value="metodologia.grupo_faunistico || 'Não informado'"
              disabled
            />
          </div>
          <div class="sm:col-span-2">
            <InputLabel :value="'METODOLOGIA ' + (index + 1)" :for="'metodologia_' + index" />
            <textarea
              class="form-control"
              :id="'metodologia_' + index"
              rows="4"
              :value="metodologia.metodologia || 'Não informado'"
              disabled
            ></textarea>
          </div>
        </div>
      </div>
      <div v-else class="alert alert-info text-center">
        Nenhuma metodologia disponível.
      </div>

      <!-- Navegação -->
      <div class="d-flex justify-content-between mt-4">
        <NavButton type="button" type-button="secondary" title="Voltar" @click="$emit('prev')" />
        <NavButton type="button" type-button="primary" title="Avançar" @click="$emit('next')" />
      </div>
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
.form-control:disabled {
  background-color: #f8f9fa;
  color: #495057;
  border: 1px solid #ced4da;
  border-radius: 4px;
  box-sizing: border-box;
  width: 100%;
  min-width: 200px;
  max-width: 100%;
  padding: 0.5rem 1rem;
  font-size: 1rem;
}
textarea.form-control:disabled {
  resize: none;
}
.grid {
  display: grid;
  gap: 1rem;
  grid-template-columns: repeat(1, 1fr);
}
@media (min-width: 640px) {
  .grid {
    grid-template-columns: repeat(2, 1fr);
  }
}
.alert-info {
  font-size: 1rem;
  padding: 1rem;
  border-radius: 6px;
  background-color: #e7f1ff;
  color: #084298;
}
</style>