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
  contrato_id: null,
  nome: null,
  ...props.contrato.introducao
});

const enviarIntroducao = () => {
  if (form.id) {
    axios.patch(route('contratos.contratada.update_introducao.index', form.id), form)
      .then(response => {
        if (response.data.type === 'success') {
          Object.assign(form, response.data.introducao);
          toast.success('Introdução alterada com sucesso!')
        } else {
          toast.error('Falha ao alterar a introdução')
        }
      }).catch(response => {
        console.log('teste', response);
        toast.error('Falha ao cadastrar a introdução')
      });
  } else {
    axios.post(route('contratos.contratada.store_introducao.index'), { contrato_id: props.contrato.id, form: form })
      .then(response => {
        if (response.data.type === 'success') {
          Object.assign(form, response.data.introducao);
          toast.success('Introdução cadastrado com sucesso!')
        } else {
          toast.error('Falha ao cadastrar a introdução')
        }
      }).catch(response => {
        console.log('teste', response);
        toast.error('Falha ao cadastrar a introdução')
      });
  }
};

</script>

<template>
  <h4>Introdução</h4>
  <div class="form-group mb-4">
    <textarea v-model="form.nome" class="form-control" rows="5"></textarea>
    <InputError :message="form.errors.nome" />
  </div>
  <div class="text-end">
    <button @click="enviarIntroducao()" type="button" class="btn btn-success">Salvar</button>
  </div>
</template>