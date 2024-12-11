<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
  contrato: { type: Object },
  servico: { type: Object }
});

const form = useForm({
  id: null,
  id_servico: null,
  classe: null,
  ordem: null,
  familia: null,
  genero: null,
  especie: null,
  nome_comum: null
});
</script>
<template>
  <form>
    <div class="row mb-4">
      <div class="col">
        <InputLabel value="Classe" for="classe" />
        <input type="text" class="form-control" v-model="form.classe">
        <InputError :message="form.errors.classe" />
      </div>
      <div class="col">
        <InputLabel value="Ordem" for="ordem" />
        <input type="text" class="form-control" v-model="form.ordem">
        <InputError :message="form.errors.ordem" />
      </div>
      <div class="col">
        <InputLabel value="Familia" for="familia" />
        <input type="text" class="form-control" v-model="form.familia">
        <InputError :message="form.errors.familia" />
      </div>
    </div>
    <div class="row mb-4">
      <div class="col">
        <InputLabel value="Gênero" for="genero" />
        <input type="text" class="form-control" v-model="form.genero">
        <InputError :message="form.errors.genero" />
      </div>
      <div class="col">
        <InputLabel value="Espécie" for="especie" />
        <input type="text" class="form-control" v-model="form.especie">
        <InputError :message="form.errors.especie" />
      </div>
      <div class="col">
        <InputLabel value="Nome comum" for="nome_comum" />
        <input type="text" class="form-control" v-model="form.nome_comum">
        <InputError :message="form.errors.nome_comum" />
      </div>
    </div>
  </form>
</template>