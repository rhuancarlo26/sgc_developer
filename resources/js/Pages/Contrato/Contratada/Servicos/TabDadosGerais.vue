<script setup>
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import { useForm } from "@inertiajs/vue3";
import { IconDeviceFloppy } from "@tabler/icons-vue";
import { ref, watch } from "vue";

const props = defineProps({
  contrato: Object,
  servico: Object,
  tipos: Array,
  temas: Array
})

let tipoFiltrado = ref();

const form = useForm({
  id: null,
  contrato_id: props.contrato?.id,
  tema: null,
  tipo: null,
  especificacao: null,
  introducao: null,
  justificativa: null,
  objetivo: null,
  metodologia: null,
  publico_alvo: null,
  ...props.servico
});

watch(
  () => form.tema,
  () => {
    // Filtra rodovias de acordo com UF selecionada;
    if (form.tema) {
      tipoFiltrado = props.tipos.filter((tipo) => {
        return tipo.servico_tema_id === form.tema.id;
      });
    } else {
      tipoFiltrado = [];
    }
  }
);

const salvarServico = () => {
  if (form.id) {
    form.patch(route('contratos.contratada.servicos.update'));
  } else {
    form.post(route('contratos.contratada.servicos.store'));
  }
}
</script>
<template>
  <form @submit.prevent="salvarServico()">
    <div class="row mb-4">
      <div class="col form-group">
        <InputLabel value="Tema" for="tema" />
        <v-select :options="temas" label="nome" v-model="form.tema">
          <template #no-options="{ }">
            Nenhum registro encontrado.
          </template>
        </v-select>
        <InputError :message="form.errors.tema" />
      </div>
      <div class="col form-group">
        <InputLabel value="Serviço" for="servico" />
        <v-select :options="tipoFiltrado" label="nome" v-model="form.tipo">
          <template #no-options="{ }">
            Nenhum registro encontrado.
          </template>
        </v-select>
        <InputError :message="form.errors.tipo" />
      </div>
    </div>
    <div class="row mb-4">
      <div class="col form-group">
        <InputLabel value="Especificação" for="especificacao" />
        <textarea name="especificacao" id="especificacao" class="form-control" v-model="form.especificacao"
          rows="5"></textarea>
        <InputError :message="form.errors.especificacao" />
      </div>
    </div>
    <div class="row mb-4">
      <div class="col form-group">
        <InputLabel value="Introdução" for="introducao" />
        <textarea name="introducao" id="introducao" class="form-control" v-model="form.introducao" rows="5"></textarea>
        <InputError :message="form.errors.introducao" />
      </div>
    </div>
    <div class="row mb-4">
      <div class="col form-group">
        <InputLabel value="Justificativa" for="justificativa" />
        <textarea name="justificativa" id="justificativa" class="form-control" v-model="form.justificativa"
          rows="5"></textarea>
        <InputError :message="form.errors.justificativa" />
      </div>
    </div>
    <div class="row mb-4">
      <div class="col form-group">
        <InputLabel value="Objetivos" for="objetivo" />
        <textarea name="objetivo" id="objetivo" class="form-control" v-model="form.objetivo" rows="5"></textarea>
        <InputError :message="form.errors.objetivo" />
      </div>
    </div>
    <div class="row mb-4">
      <div class="col form-group">
        <InputLabel value="Metodologia" for="metodologia" />
        <textarea name="metodologia" id="metodologia" class="form-control" v-model="form.metodologia"
          rows="5"></textarea>
        <InputError :message="form.errors.metodologia" />
      </div>
    </div>
    <div class="row mb-4">
      <div class="col form-group">
        <InputLabel value="Público alvo" for="publico_alvo" />
        <textarea name="publico_alvo" id="publico_alvo" class="form-control" v-model="form.publico_alvo"
          rows="5"></textarea>
        <InputError :message="form.errors.publico_alvo" />
      </div>
    </div>
    <div class="mb-4 d-flex justify-content-end">
      <button type="submit" class="btn btn-success" :disabled="form.processing">
        <IconDeviceFloppy class="me-2" />
        Salvar
      </button>
    </div>
  </form>
</template>