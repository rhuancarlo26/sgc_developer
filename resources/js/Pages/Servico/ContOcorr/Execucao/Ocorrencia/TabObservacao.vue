<script setup>
import { useForm } from "@inertiajs/vue3";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import NavButton from "@/Components/NavButton.vue";
import { IconDeviceFloppy } from "@tabler/icons-vue";
import { watch } from "vue";
import { computed } from "vue";

const props = defineProps({
  contrato: { type: Object },
  servico: { type: Object },
  ocorrencia: { type: Object }
});

const form = useForm({
  form: 6,
  id: null,
  obs: null,
  ...props.ocorrencia
});

const salvarObservacao = () => {
  form.id = props.ocorrencia.id;

  form.post(route('contratos.contratada.servicos.cont_ocorrencia.execucao.ocorrencia.update', { contrato: props.contrato.id, servico: props.servico.id }));
}
</script>
<template>
  <div class="row mb-4">
    <div class="col">
      <InputLabel value="Observações" for="obs" />
      <textarea class="form-control" rows="5" v-model="form.obs"></textarea>
      <InputError :message="form.errors.obs" />
    </div>
  </div>
  <div class="row">
    <div class="col d-flex justify-content-end">
      <NavButton @click="salvarObservacao()" type-button="success" :icon="IconDeviceFloppy"
        :title="form.id ? 'Alterar' : 'Salvar'" />
    </div>
  </div>
</template>