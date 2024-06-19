<script setup>
import InputError from '@/Components/InputError.vue';
import { useForm } from '@inertiajs/vue3';
import axios from 'axios';
import { useToast } from "vue-toastification";

const toast = useToast();

const props = defineProps({
  contrato: Object
})

const form = useForm({
  id: null,
  contrato_id: props.contrato.id,
  nome: null,
  ...props.contrato.introducao
});

const enviarIntroducao = () => {
  if (form.id) {
    form.patch(route('contratos.contratada.update_introducao.index', form.id));
  } else {
    form.post(route('contratos.contratada.store_introducao.index'));
  }
};

</script>

<template>
  <!-- <h4>Introdução</h4> -->
  <div class="form-group mt-4">
    <div class="card-header">
      <h3 class="my-0">Introdução</h3>
    </div>
    <textarea v-model="form.nome" class="form-control" rows="5"></textarea>
    <InputError :message="form.errors.nome" />
  </div>
  <div class="text-end">
    <button @click="enviarIntroducao()" type="button" class="btn btn-success">Salvar</button>
  </div>
</template>