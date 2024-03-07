<script setup>
import InputError from '@/Components/InputError.vue';
import { useForm } from '@inertiajs/vue3';

const form = useForm({
  introducao: null
});

const enviarIntroducao = () => {
  axios.post(route())
    .then(response => {

    })
    .catch(response => {
      return console.log(response);
    });
};

</script>

<template>
  <h4>Introdução</h4>
  <form>
    <div class="form-group mb-4">
      <textarea v-model="form.introducao" class="form-control" rows="5"></textarea>
      <InputError :message="form.errors.introducao" />
    </div>
    <div class="text-end">
      <button class="btn btn-success">Salvar</button>
    </div>
  </form>
</template>