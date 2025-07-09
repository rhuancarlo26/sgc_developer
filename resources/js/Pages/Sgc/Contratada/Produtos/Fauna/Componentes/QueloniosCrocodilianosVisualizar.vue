<script setup>
import { defineProps, defineEmits } from 'vue';
import InputLabel from '@/Components/InputLabel.vue';
import NavButton from '@/Components/NavButton.vue';

defineProps({
  formPontosAmostragem: {
    type: Object,
    default: () => ({}),
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

      <!-- Dados do Ponto de Amostragem -->
      <div v-if="formPontosAmostragem && Object.keys(formPontosAmostragem).length" class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
        <div>
          <InputLabel value="NÃO SE APLICA" for="nao_se_aplica" />
          <input
            type="text"
            class="form-control"
            id="nao_se_aplica"
            :value="naoSeAplica ? 'Sim' : 'Não'"
            disabled
          />
        </div>
        <div>
          <InputLabel value="PONTO DE COLETA" for="ponto_de_coleta" />
          <input
            type="text"
            class="form-control"
            id="ponto_de_coleta"
            :value="formPontosAmostragem.ponto_de_coleta || 'Não informado'"
            disabled
          />
        </div>
        <div>
          <InputLabel value="NOME DO CURSO HÍDRICO" for="nome_curso_hidrico" />
          <input
            type="text"
            class="form-control"
            id="nome_curso_hidrico"
            :value="formPontosAmostragem.nome_curso_hidrico || 'Não informado'"
            disabled
          />
        </div>
        <div>
          <InputLabel value="COORDENADAS" for="coordenadas" />
          <input
            type="text"
            class="form-control"
            id="coordenadas"
            :value="formPontosAmostragem.coordenadas || 'Não informado'"
            disabled
          />
        </div>
        <div>
          <InputLabel value="BACIA HIDROGRÁFICA" for="bacia" />
          <input
            type="text"
            class="form-control"
            id="bacia"
            :value="formPontosAmostragem.bacia || 'Não informado'"
            disabled
          />
        </div>
        <div>
          <InputLabel value="PROFUNDIDADE (m)" for="profundidade" />
          <input
            type="text"
            class="form-control"
            id="profundidade"
            :value="formPontosAmostragem.profundidade || 'Não informado'"
            disabled
          />
        </div>
        <div>
          <InputLabel value="LARGURA (m)" for="largura" />
          <input
            type="text"
            class="form-control"
            id="largura"
            :value="formPontosAmostragem.largura || 'Não informado'"
            disabled
          />
        </div>
        <div class="col-span-full sm:col-span-2">
          <InputLabel value="TIPO DE SUBSTRATO" for="tipo_substrato" />
          <textarea
            class="form-control"
            id="tipo_substrato"
            rows="5"
            :value="formPontosAmostragem.tipo_substrato || 'Não informado'"
            disabled
          ></textarea>
        </div>
      </div>
      <div v-else class="alert alert-info text-center">
        Nenhum dado de ponto de amostragem disponível.
      </div>

      <!-- Navegação -->
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
  margin: 1.5rem; /* Alinhado com etapas 2/5 e 3/5 */
}
.card-body {
  padding: 2rem; /* Alinhado com etapas 2/5 e 3/5 */
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
  min-width: 200px; /* Alinhado com etapas 2/5 e 3/5 */
  max-width: 100%;
  padding: 0.5rem 1rem; /* Alinhado com etapas 2/5 e 3/5 */
  font-size: 1rem;
}
textarea.form-control:disabled {
  resize: none;
  min-height: 120px;
  max-height: 400px; /* Alinhado com etapas 2/5 e 3/5 */
  overflow-y: auto;
  width: 100%;
  min-width: 300px; /* Alinhado com etapas 2/5 e 3/5 */
  max-width: 100%;
  padding: 0.5rem 1rem;
  box-sizing: border-box;
  font-size: 1rem;
}
.grid {
  display: grid;
  gap: 1rem;
  grid-template-columns: repeat(1, 1fr);
}
@media (min-width: 640px) {
  .grid {
    grid-template-columns: repeat(2, 1fr); /* Alinhado com a view de cadastro */
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