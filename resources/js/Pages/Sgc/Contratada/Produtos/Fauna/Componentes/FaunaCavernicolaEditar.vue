<script setup>
import { defineProps, defineEmits } from 'vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import NavButton from '@/Components/NavButton.vue';

defineProps({
  form: {
    type: Object,
    required: true,
  },
  pontoRecords: {
    type: Array,
    default: () => [],
  },
  subStep: {
    type: Number,
    default: 5,
  },
});

defineEmits(['next', 'prev']);

const addPonto = () => {
  form.pontos_cavernicola.push({
    cavidade: '',
    latitude: null,
    longitude: null,
    distancia_eixo_rodovia: null,
    formacao_associada: '',
    temperatura_media_interna: null,
    temperatura_media_externa: null,
    umidade_relativa_interna: null,
    umidade_relativa_externa: null,
  });
};

const removePonto = (index) => {
  form.pontos_cavernicola.splice(index, 1);
};
</script>

<template>
  <div class="card">
    <div class="card-body">
      <h4 class="mb-3" style="text-align: center;">FAUNA CAVERNÍCOLA</h4>

      <div class="mb-4">
        <InputLabel value="NÃO SE APLICA" for="nao_se_aplica" />
        <input type="checkbox" v-model="form.nao_se_aplica" id="nao_se_aplica" />
        <InputError :message="form.errors.nao_se_aplica" />
      </div>

      <div v-if="!form.nao_se_aplica" class="mb-6">
        <div v-for="(ponto, index) in pontoRecords" :key="index" class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
          <div>
            <InputLabel value="CAVIDADE" for="cavidade" />
            <input v-model="form.pontos_cavernicola[index].cavidade" type="text" class="form-control" :id="'cavidade_' + index" />
            <InputError :message="form.errors[`pontos_cavernicola.${index}.cavidade`]" />
          </div>
          <div>
            <InputLabel value="LATITUDE" for="latitude" />
            <input v-model="form.pontos_cavernicola[index].latitude" type="number" step="any" class="form-control" :id="'latitude_' + index" />
            <InputError :message="form.errors[`pontos_cavernicola.${index}.latitude`]" />
          </div>
          <div>
            <InputLabel value="LONGITUDE" for="longitude" />
            <input v-model="form.pontos_cavernicola[index].longitude" type="number" step="any" class="form-control" :id="'longitude_' + index" />
            <InputError :message="form.errors[`pontos_cavernicola.${index}.longitude`]" />
          </div>
          <div>
            <InputLabel value="DISTÂNCIA DO EIXO DA RODOVIA (m)" for="distancia_eixo_rodovia" />
            <input v-model="form.pontos_cavernicola[index].distancia_eixo_rodovia" type="number" step="any" class="form-control" :id="'distancia_eixo_rodovia_' + index" />
            <InputError :message="form.errors[`pontos_cavernicola.${index}.distancia_eixo_rodovia`]" />
          </div>
          <div>
            <InputLabel value="FORMAÇÃO ASSOCIADA" for="formacao_associada" />
            <input v-model="form.pontos_cavernicola[index].formacao_associada" type="text" class="form-control" :id="'formacao_associada_' + index" />
            <InputError :message="form.errors[`pontos_cavernicola.${index}.formacao_associada`]" />
          </div>
          <div>
            <InputLabel value="TEMPERATURA MÉDIA INTERNA (°C)" for="temperatura_media_interna" />
            <input v-model="form.pontos_cavernicola[index].temperatura_media_interna" type="number" step="any" class="form-control" :id="'temperatura_media_interna_' + index" />
            <InputError :message="form.errors[`pontos_cavernicola.${index}.temperatura_media_interna`]" />
          </div>
          <div>
            <InputLabel value="TEMPERATURA MÉDIA EXTERNA (°C)" for="temperatura_media_externa" />
            <input v-model="form.pontos_cavernicola[index].temperatura_media_externa" type="number" step="any" class="form-control" :id="'temperatura_media_externa_' + index" />
            <InputError :message="form.errors[`pontos_cavernicola.${index}.temperatura_media_externa`]" />
          </div>
          <div>
            <InputLabel value="UMIDADE RELATIVA INTERNA (%)" for="umidade_relativa_interna" />
            <input v-model="form.pontos_cavernicola[index].umidade_relativa_interna" type="number" step="any" class="form-control" :id="'umidade_relativa_interna_' + index" />
            <InputError :message="form.errors[`pontos_cavernicola.${index}.umidade_relativa_interna`]" />
          </div>
          <div>
            <InputLabel value="UMIDADE RELATIVA EXTERNA (%)" for="umidade_relativa_externa" />
            <input v-model="form.pontos_cavernicola[index].umidade_relativa_externa" type="number" step="any" class="form-control" :id="'umidade_relativa_externa_' + index" />
            <InputError :message="form.errors[`pontos_cavernicola.${index}.umidade_relativa_externa`]" />
          </div>
          <div class="col-span-full">
            <button type="button" class="btn btn-danger btn-sm" @click="removePonto(index)">Remover Ponto</button>
          </div>
        </div>
        <button type="button" class="btn btn-primary btn-sm mt-2" @click="addPonto">Adicionar Ponto</button>
      </div>
      <div v-else-if="!form.nao_se_aplica" class="alert alert-info text-center">
        Nenhum ponto de fauna cavernícola disponível. Clique em "Adicionar Ponto" para começar.
      </div>

      <div class="d-flex justify-content-between mt-4">
        <NavButton type="button" type-button="secondary" title="Voltar" @click="$emit('prev')" />
        <NavButton type="button" type-button="primary" title="Avançar" @click="$emit('next')" />
      </div>
      <h4 class="text-center mt-4 font-weight-bold text-muted">{{ subStep }}/5</h4>
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
.form-control {
  border: 1px solid #ced4da;
  border-radius: 4px;
  padding: 0.375rem 0.75rem;
  width: 100%;
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
.btn-sm {
  padding: 0.25rem 0.5rem;
  font-size: 0.875rem;
}
</style>