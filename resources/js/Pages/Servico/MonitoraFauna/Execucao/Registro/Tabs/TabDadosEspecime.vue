<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
  contrato: { type: Object },
  servico: { type: Object }
});

const form = useForm({
  sexo: null,
  faixa_etaria: null,
  marcacao: null,
  num_marcacao: null,
  data_registro: null,
  hora_registro: null,
  coletado: null,
  num_tombamento: null,
  dds_bio_anotados: null,
  num_individuos: null
});
</script>
<template>
  <form>
    <div class="row mb-4">
      <div class="col">
        <InputLabel value="ID Registro" for="nome_id" />
        <input type="text" class="form-control" :value="form.nome_id" disabled>
        <InputError :message="form.errors.nome_id" />
      </div>
    </div>
  </form>
</template>