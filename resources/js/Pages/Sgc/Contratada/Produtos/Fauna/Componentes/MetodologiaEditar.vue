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
  metodologiaRecords: {
    type: Array,
    default: () => [],
  },
});

defineEmits(['next', 'prev']);

const addMetodologia = () => {
  form.metodologias.push({
    grupo_faunistico: '',
    metodologia: '',
  });
};

const removeMetodologia = (index) => {
  form.metodologias.splice(index, 1);
};
</script>

<template>
  <div class="card">
    <div class="card-body">
      <h4 class="mb-3" style="text-align: center;">METODOLOGIA</h4>

      <div v-if="metodologiaRecords.length" class="mb-6">
        <div v-for="(metodologia, index) in metodologiaRecords" :key="index" class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
          <div>
            <InputLabel value="GRUPO FAUNÍSTICO" :for="'grupo_faunistico_' + index" />
            <input v-model="form.metodologias[index].grupo_faunistico" type="text" class="form-control" :id="'grupo_faunistico_' + index" />
            <InputError :message="form.errors[`metodologias.${index}.grupo_faunistico`]" />
          </div>
          <div class="sm:col-span-2">
            <InputLabel value="METODOLOGIA" :for="'metodologia_' + index" />
            <textarea v-model="form.metodologias[index].metodologia" class="form-control" :id="'metodologia_' + index" rows="4"></textarea>
            <InputError :message="form.errors[`metodologias.${index}.metodologia`]" />
          </div>
          <div class="col-span-full">
            <button type="button" class="btn btn-danger btn-sm" @click="removeMetodologia(index)">Remover Metodologia</button>
          </div>
        </div>
        <button type="button" class="btn btn-primary btn-sm mt-2" @click="addMetodologia">Adicionar Metodologia</button>
      </div>
      <div v-else class="alert alert-info text-center">
        Nenhuma metodologia disponível. Clique em "Adicionar Metodologia" para começar.
      </div>

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
.form-control {
  border: 1px solid #ced4da;
  border-radius: 4px;
  padding: 0.375rem 0.75rem;
  width: 100%;
}
textarea.form-control {
  resize: vertical;
  min-height: 100px;
}
.alert-info {
  font-size: 1rem;
  padding: 1rem;
  border-radius: 6px;
  background-color: #e7f1ff;
  color: #084298;
}
.btn-sm {
  padding: 0.25rem 0.5rem;
  font-size: 0.875rem;
}
</style>