<script setup>
import { defineProps, defineEmits } from 'vue';
import InputLabel from '@/Components/InputLabel.vue';
import NavButton from '@/Components/NavButton.vue';

defineProps({
  formModuloAmostral: {
    type: Object,
    default: () => ({}),
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

      <!-- Dados do Módulo Amostral -->
      <div v-if="formModuloAmostral && Object.keys(formModuloAmostral).length" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div>
          <InputLabel value="ID MÓDULO AMOSTRAL" for="id_modulo" />
          <input
            type="text"
            class="form-control"
            id="id_modulo"
            :value="formModuloAmostral.id || 'Não informado'"
            disabled
          />
        </div>
        <div>
          <InputLabel value="DATA" for="data_cadastro" />
          <input
            type="text"
            class="form-control"
            id="data_cadastro"
            :value="formatDate(formModuloAmostral.data_cadastro)"
            disabled
          />
        </div>
        <div>
          <InputLabel value="TAMANHO DO MÓDULO" for="tamanho_modulo" />
          <input
            type="text"
            class="form-control"
            id="tamanho_modulo"
            :value="formModuloAmostral.tamanho_modulo ? `${formModuloAmostral.tamanho_modulo}km` : 'Não informado'"
            disabled
          />
        </div>
        <div>
          <InputLabel value="UF" for="uf" />
          <input
            type="text"
            class="form-control"
            id="uf"
            :value="formModuloAmostral.uf || 'Não informado'"
            disabled
          />
        </div>
        <div>
          <InputLabel value="MUNICÍPIO" for="municipio" />
          <input
            type="text"
            class="form-control"
            id="municipio"
            :value="formModuloAmostral.municipio || 'Não informado'"
            disabled
          />
        </div>
        <div>
          <InputLabel value="BIOMA" for="bioma" />
          <input
            type="text"
            class="form-control"
            id="bioma"
            :value="formModuloAmostral.bioma || 'Não informado'"
            disabled
          />
        </div>
        <div class="col-span-full sm:col-span-2 lg:col-span-3">
          <InputLabel value="FITOFISIONOMIA" for="fitofisionomia" />
          <input
            class="form-control"
            id="fitofisionomia"
            rows="5"
            :value="formModuloAmostral.fitofisionomia || 'Não informado'"
            disabled
          ></input>
        </div>
        <div>
          <InputLabel value="LATITUDE INICIAL" for="latitude_inicial" />
          <input
            type="text"
            class="form-control"
            id="latitude_inicial"
            :value="formModuloAmostral.latitude_inicial || 'Não informado'"
            disabled
          />
        </div>
        <div>
          <InputLabel value="LONGITUDE INICIAL" for="longitude_inicial" />
          <input
            type="text"
            class="form-control"
            id="longitude_inicial"
            :value="formModuloAmostral.longitude_inicial || 'Não informado'"
            disabled
          />
        </div>
        <div>
          <InputLabel value="LATITUDE FINAL" for="latitude_final" />
          <input
            type="text"
            class="form-control"
            id="latitude_final"
            :value="formModuloAmostral.latitude_final || 'Não informado'"
            disabled
          />
        </div>
        <div>
          <InputLabel value="LONGITUDE FINAL" for="longitude_final" />
          <input
            type="text"
            class="form-control"
            id="longitude_final"
            :value="formModuloAmostral.longitude_final || 'Não informado'"
            disabled
          />
        </div>
        <div>
          <InputLabel value="SHAPEFILE" for="arquivo" />
          <input
            type="text"
            class="form-control"
            id="arquivo"
            :value="formModuloAmostral.arquivo || 'Não informado'"
            disabled
          />
        </div>
        <div class="col-span-full sm:col-span-2 lg:col-span-3">
          <InputLabel value="OBSERVAÇÕES" for="obs_modulo" />
          <input
            class="form-control"
            id="obs_modulo"
            rows="5"
            :value="formModuloAmostral.obs || 'Não informado'"
            disabled
          ></input>
        </div>
      </div>
      <div v-else class="alert alert-info text-center">
        Nenhum dado do módulo amostral disponível.
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
  margin: 1.5rem; /* Alinhado com a etapa 2/5 */
}
.card-body {
  padding: 2rem; /* Alinhado com a etapa 2/5 */
}
.card-title {
  font-size: 1.75rem;
  font-weight: 700;
  color: #343a40;
  margin-bottom: 1.5rem;
  text-align: center;
}
.form-control:disabled {
  background-color: #f8f9fa;
  color: #495057;
  border: 1px solid #ced4da;
  border-radius: 4px;
  box-sizing: border-box;
  width: 100%;
  min-width: 200px; /* Alinhado com a etapa 2/5 */
  max-width: 100%; /* Garante que não ultrapasse a coluna */
  padding: 0.5rem 1rem; /* Alinhado com a etapa 2/5 */
  font-size: 1rem;
}
textarea.form-control:disabled {
  resize: none;
  min-height: 120px;
  max-height: 400px; /* Alinhado com a etapa 2/5 */
  overflow-y: auto;
  width: 100%;
  min-width: 300px; /* Alinhado com a etapa 2/5 */
  max-width: 100%;
  padding: 0.5rem 1rem;
  box-sizing: border-box;
  font-size: 1rem;
}
.grid {
  display: grid;
  gap: 1rem; /* Mantido para consistência, mas ajustável para 4px se necessário */
  grid-template-columns: repeat(1, 1fr);
}
@media (min-width: 640px) {
  .grid {
    grid-template-columns: repeat(2, 1fr);
  }
}
@media (min-width: 1024px) {
  .grid {
    grid-template-columns: repeat(4, 1fr);
  }
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