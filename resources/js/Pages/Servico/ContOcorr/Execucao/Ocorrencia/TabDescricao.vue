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
  form: 2,
  id: null,
  local: null,
  classificacao: null,
  descricao: null,
  area_total: null,
  prazo: null,
  ...props.ocorrencia
});

const salvarDescricao = () => {
  form.id = props.ocorrencia.id;

  form.post(route('contratos.contratada.servicos.cont_ocorrencia.execucao.ocorrencia.update', { contrato: props.contrato.id, servico: props.servico.id }));
}
</script>
<template>
  <div class="row mb-4">
    <div class="col">
      <InputLabel value="Local da Ocorrência" for="local" />
      <select class="form-control form-select" v-model="form.local">
        <option value="APP">APP</option>
        <option value="Faixa de Domínio">Faixa de Domínio</option>
        <option value="Canteiro">Canteiro</option>
        <option value="Bota-fora">Bota-fora</option>
        <option value="Área Fonte">Área Fonte</option>
        <option value="Área de Apoio">Área de Apoio</option>
        <option value="Outros">Outros</option>
      </select>
      <InputError :message="form.errors.local" />
    </div>
    <div class="col">
      <InputLabel value="Classificação da Ocorrência" for="classificacao" />
      <select class="form-control form-select" v-model="form.classificacao">
        <option value="Erosão">Erosão</option>
        <option value="Escorregamento">Escorregamento</option>
        <option value="Queda de blocos">Queda de blocos</option>
        <option value="Rolamento de Blocos">Rolamento de Blocos</option>
        <option value="Acessos Irregulares">Acessos Irregulares</option>
        <option value="Ocupação da Faixa de domínio">Ocupação da Faixa de domínio</option>
        <option value="Assoreamento">Assoreamento</option>
        <option value="Alagamento">Alagamento</option>
        <option value="Contaminação/poluição">Contaminação/poluição</option>
        <option value="Licenças/outorgas">Licenças/outorgas</option>
        <option value="Outros">Outros</option>
      </select>
      <InputError :message="form.errors.classificacao" />
    </div>
  </div>
  <div class="row mb-4">
    <div class="col">
      <InputLabel value="Data da Ocorrência" for="descricao" />
      <textarea class="form-control" rows="5" v-model="form.descricao"></textarea>
      <InputError :message="form.errors.descricao" />
    </div>
  </div>
  <div class="row mb-4">
    <div class="col">
      <InputLabel value="Área total da Ocorrência (m²)" for="area_total" />
      <input type="text" class="form-control" v-model="form.area_total">
      <InputError :message="form.errors.area_total" />
    </div>
    <div class="col">
      <InputLabel value="Prazo para Correção (dias)" for="prazo" />
      <input type="text" class="form-control" :value="ocorrencia.prazo" disabled>
      <InputError :message="form.errors.prazo" />
    </div>
  </div>
  <div class="row">
    <div class="col d-flex justify-content-end">
      <NavButton @click="salvarDescricao()" type-button="success" :icon="IconDeviceFloppy"
        :title="form.id ? 'Alterar' : 'Salvar'" />
    </div>
  </div>
</template>