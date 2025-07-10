<script setup>
import { defineProps, defineEmits } from 'vue';
import InputLabel from '@/Components/InputLabel.vue';
import NavButton from '@/Components/NavButton.vue';

defineProps({
  formPontosCavernicola: {
    type: Object,
    default: () => ({}),
  },
  naoSeAplica: {
    type: Boolean,
    default: false,
  },
  subStep: {
    type: Number,
    default: 5,
  },
});

defineEmits(['next', 'prev']);
</script>

<template>
  <div class="card">
    <div class="card-body">
      <h4 class="mb-3" style="text-align: center;">FAUNA CAVERNÍCOLA</h4>

      <!-- Dados do Ponto de Fauna Cavernícola -->
      <div v-if="formPontosCavernicola && Object.keys(formPontosCavernicola).length" class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
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
          <InputLabel value="CAVIDADE" for="cavidade" />
          <input
            type="text"
            class="form-control"
            id="cavidade"
            :value="formPontosCavernicola.cavidade || 'Não informado'"
            disabled
          />
        </div>
        <div>
          <InputLabel value="LATITUDE" for="latitude" />
          <input
            type="text"
            class="form-control"
            id="latitude"
            :value="formPontosCavernicola.latitude || 'Não informado'"
            disabled
          />
        </div>
        <div>
          <InputLabel value="LONGITUDE" for="longitude" />
          <input
            type="text"
            class="form-control"
            id="longitude"
            :value="formPontosCavernicola.longitude || 'Não informado'"
            disabled
          />
        </div>
        <div>
          <InputLabel value="DISTÂNCIA DO EIXO DA RODOVIA (m)" for="distancia_eixo_rodovia" />
          <input
            type="text"
            class="form-control"
            id="distancia_eixo_rodovia"
            :value="formPontosCavernicola.distancia_eixo_rodovia || 'Não informado'"
            disabled
          />
        </div>
        <div>
          <InputLabel value="FORMAÇÃO ASSOCIADA" for="formacao_associada" />
          <input
            type="text"
            class="form-control"
            id="formacao_associada"
            :value="formPontosCavernicola.formacao_associada || 'Não informado'"
            disabled
          />
        </div>
        <div>
          <InputLabel value="TEMPERATURA MÉDIA INTERNA (°C)" for="temperatura_media_interna" />
          <input
            type="text"
            class="form-control"
            id="temperatura_media_interna"
            :value="formPontosCavernicola.temperatura_media_interna || 'Não informado'"
            disabled
          />
        </div>
        <div>
          <InputLabel value="TEMPERATURA MÉDIA EXTERNA (°C)" for="temperatura_media_externa" />
          <input
            type="text"
            class="form-control"
            id="temperatura_media_externa"
            :value="formPontosCavernicola.temperatura_media_externa || 'Não informado'"
            disabled
          />
        </div>
        <div>
          <InputLabel value="UMIDADE RELATIVA INTERNA (%)" for="umidade_relativa_interna" />
          <input
            type="text"
            class="form-control"
            id="umidade_relativa_interna"
            :value="formPontosCavernicola.umidade_relativa_interna || 'Não informado'"
            disabled
          />
        </div>
        <div>
          <InputLabel value="UMIDADE RELATIVA EXTERNA (%)" for="umidade_relativa_externa" />
          <input
            type="text"
            class="form-control"
            id="umidade_relativa_externa"
            :value="formPontosCavernicola.umidade_relativa_externa || 'Não informado'"
            disabled
          />
        </div>
      </div>
      <div v-else class="alert alert-info text-center">
        Nenhum dado de ponto de fauna cavernícola disponível.
      </div>

      <!-- Navegação -->
      <div class="d-flex justify-content-between mt-4">
        <NavButton type="button" type-button="secondary" title="Voltar" @click="$emit('prev')" />
        <NavButton type="button" type-button="primary" title="Avançar" @click="$emit('next')" />
      </div>
      <h4 class="text-center mt-4 font-weight-bold text-muted">5/5</h4>
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
.text-muted {
  color: #6c757d !important;
}
</style>