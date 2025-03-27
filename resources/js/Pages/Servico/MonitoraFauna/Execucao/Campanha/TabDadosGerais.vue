<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import NavButton from '@/Components/NavButton.vue';
import { useForm } from '@inertiajs/vue3';
import axios from 'axios';
import { useToast } from 'vue-toastification';

const props = defineProps({
  contrato: { type: Object },
  servico: { type: Object },
  modulos: { type: Array }
})

const toast = useToast();
const form = useForm({
  id: null,
  id_servico: props.servico.id,
  data_campanha_inicial: null,
  data_campanha_final: null,
  periodo: null,
  obs: null
});

const save = () => {
  axios.post(route('contratos.contratada.servicos.monitora_fauna.execucao.campanha.store', { contrato: props.contrato.id, servico: props.servico.id })).then((resp) => {
    if (resp.data.request.type === 'success') {
      toast.success(resp.data.request?.content);
    } else {
      toast.error(resp.data.request?.content);
    }

    form.id = resp.data.model?.id;
  })
}

</script>
<template>
  <div class="mt-4">

  </div>
</template>