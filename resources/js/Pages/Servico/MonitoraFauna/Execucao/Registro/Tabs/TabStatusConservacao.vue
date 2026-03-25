<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
  status_conservacao: { type: Array },
  form: { type: Object }
});
</script>
<template>
  <form>
    <div class="row mb-4">
      <div class="col">
        <InputLabel value="Federal" for="status_conserv_federal" />
        <v-select :options="[...status_conservacao.filter((_, index) => [0, 6, 8, 9].includes(index)).map(c => {
          return { ...c, label: `${c.nome} - ${c.sigla}` }
        })]" :reduce="a => a.id" v-model="form.status_conserv_federal">
          <template #no-options="{}">
            Nenhum registro encontrado.
          </template>
        </v-select>
        <InputError :message="form.errors.status_conserv_federal" />
      </div>
      <div class="col">
        <InputLabel value="IUCN" for="status_conserv_iucn" />
        <v-select :options="[...status_conservacao.filter((_, index) => ![0].includes(index)).map(c => {
          return { ...c, label: `${c.nome} - ${c.sigla}` }
        })]" :reduce="a => a.id" v-model="form.status_conserv_iucn">
          <template #no-options="{}">
            Nenhum registro encontrado.
          </template>
        </v-select>
        <InputError :message="form.errors.status_conserv_iucn" />
      </div>
    </div>
  </form>
</template>