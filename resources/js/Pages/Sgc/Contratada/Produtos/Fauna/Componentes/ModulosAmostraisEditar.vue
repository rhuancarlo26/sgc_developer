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
  moduloRecords: {
    type: Array,
    default: () => [],
  },
  subStep: {
    type: Number,
    default: 3,
  },
});

defineEmits(['next', 'prev']);

const addModulo = () => {
  form.modulos_amostrais.push({
    data_cadastro: '',
    tamanho_modulo: '',
    uf: '',
    municipio: '',
    bioma: '',
    fitofisionomia: '',
    latitude_inicial: null,
    longitude_inicial: null,
    latitude_final: null,
    longitude_final: null,
    obs: '',
    arquivo: null,
  });
};

const removeModulo = (index) => {
  form.modulos_amostrais.splice(index, 1);
};
</script>

<template>
  <div class="card">
    <div class="card-body">
      <h4 class="mb-3" style="text-align: center;">MÓDULOS AMOSTRAIS</h4>

      <div v-if="moduloRecords.length" class="mb-6">
        <div v-for="(modulo, index) in moduloRecords" :key="index" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
          <div>
            <InputLabel value="DATA" for="data_cadastro" />
            <input v-model="form.modulos_amostrais[index].data_cadastro" type="date" class="form-control" :id="'data_cadastro_' + index" />
            <InputError :message="form.errors[`modulos_amostrais.${index}.data_cadastro`]" />
          </div>
          <div>
            <InputLabel value="TAMANHO DO MÓDULO" for="tamanho_modulo" />
            <input v-model="form.modulos_amostrais[index].tamanho_modulo" type="text" class="form-control" :id="'tamanho_modulo_' + index" placeholder="Ex: 10km" />
            <InputError :message="form.errors[`modulos_amostrais.${index}.tamanho_modulo`]" />
          </div>
          <div>
            <InputLabel value="UF" for="uf" />
            <input v-model="form.modulos_amostrais[index].uf" type="text" class="form-control" :id="'uf_' + index" />
            <InputError :message="form.errors[`modulos_amostrais.${index}.uf`]" />
          </div>
          <div>
            <InputLabel value="MUNICÍPIO" for="municipio" />
            <input v-model="form.modulos_amostrais[index].municipio" type="text" class="form-control" :id="'municipio_' + index" />
            <InputError :message="form.errors[`modulos_amostrais.${index}.municipio`]" />
          </div>
          <div>
            <InputLabel value="BIOMA" for="bioma" />
            <input v-model="form.modulos_amostrais[index].bioma" type="text" class="form-control" :id="'bioma_' + index" />
            <InputError :message="form.errors[`modulos_amostrais.${index}.bioma`]" />
          </div>
          <div class="col-span-full sm:col-span-2 lg:col-span-3">
            <InputLabel value="FITOFISIONOMIA" for="fitofisionomia" />
            <textarea v-model="form.modulos_amostrais[index].fitofisionomia" class="form-control" :id="'fitofisionomia_' + index" rows="5"></textarea>
            <InputError :message="form.errors[`modulos_amostrais.${index}.fitofisionomia`]" />
          </div>
          <div>
            <InputLabel value="LATITUDE INICIAL" for="latitude_inicial" />
            <input v-model="form.modulos_amostrais[index].latitude_inicial" type="number" step="any" class="form-control" :id="'latitude_inicial_' + index" />
            <InputError :message="form.errors[`modulos_amostrais.${index}.latitude_inicial`]" />
          </div>
          <div>
            <InputLabel value="LONGITUDE INICIAL" for="longitude_inicial" />
            <input v-model="form.modulos_amostrais[index].longitude_inicial" type="number" step="any" class="form-control" :id="'longitude_inicial_' + index" />
            <InputError :message="form.errors[`modulos_amostrais.${index}.longitude_inicial`]" />
          </div>
          <div>
            <InputLabel value="LATITUDE FINAL" for="latitude_final" />
            <input v-model="form.modulos_amostrais[index].latitude_final" type="number" step="any" class="form-control" :id="'latitude_final_' + index" />
            <InputError :message="form.errors[`modulos_amostrais.${index}.latitude_final`]" />
          </div>
          <div>
            <InputLabel value="LONGITUDE FINAL" for="longitude_final" />
            <input v-model="form.modulos_amostrais[index].longitude_final" type="number" step="any" class="form-control" :id="'longitude_final_' + index" />
            <InputError :message="form.errors[`modulos_amostrais.${index}.longitude_final`]" />
          </div>
          <div>
            <InputLabel value="SHAPEFILE" for="arquivo" />
            <input type="file" @change="form.modulos_amostrais[index].arquivo = $event.target.files[0] || null" accept=".shp,.zip" class="form-control" :id="'arquivo_' + index" />
            <InputError :message="form.errors[`modulos_amostrais.${index}.arquivo`]" />
          </div>
          <div class="col-span-full sm:col-span-2 lg:col-span-3">
            <InputLabel value="OBSERVAÇÕES" for="obs_modulo" />
            <textarea v-model="form.modulos_amostrais[index].obs" class="form-control" :id="'obs_modulo_' + index" rows="5"></textarea>
            <InputError :message="form.errors[`modulos_amostrais.${index}.obs`]" />
          </div>
          <div class="col-span-full">
            <button type="button" class="btn btn-danger btn-sm" @click="removeModulo(index)">Remover Módulo</button>
          </div>
        </div>
        <button type="button" class="btn btn-primary btn-sm mt-2" @click="addModulo">Adicionar Módulo</button>
      </div>
      <div v-else class="alert alert-info text-center">
        Nenhum módulo amostral disponível.
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
.text-muted {
  color: #6c757d !important;
}
.btn-sm {
  padding: 0.25rem 0.5rem;
  font-size: 0.875rem;
}
</style>