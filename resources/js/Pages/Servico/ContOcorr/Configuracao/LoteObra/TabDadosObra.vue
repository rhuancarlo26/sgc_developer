<script setup>
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import NavButton from "@/Components/NavButton.vue";
import { useForm } from "@inertiajs/vue3";

const props = defineProps({
  contrato: { type: Object },
  servico: { type: Object }
});

const form = useForm({
  temContrato: null,
  num_contrato: null,
  supervisora_obras: null,
  num_contrato_supervisora: null,
  obj_contrato_supervisora: null,
  fiscal_contrato_supervisora: null,
  situacao_contrato_supervisora: null
});
</script>
<template>
  <div class="row mb-4">
    <div class="col">
      <InputLabel value="Tem contrato de supervisão de obra?" for="temContrato" />
      <div>
        <label class="form-check form-check-inline">
          <input class="form-check-input" type="radio" :value="true" v-model="form.temContrato">
          <span class="form-check-label">Sim</span>
        </label>
        <label class="form-check form-check-inline">
          <input class="form-check-input" type="radio" :value="false" v-model="form.temContrato">
          <span class="form-check-label">Não</span>
        </label>
      </div>
      <InputError :message="form.errors.temContrato" />
    </div>
    <div class="col" v-if="form.temContrato === true">
      <InputLabel value="Número do contrato" for="num_contrato" />
      <div class="row g-2">
        <div class="col">
          <input type="text" class="form-control" v-model="num_contrato">
        </div>
        <div class="col-auto">
          <NavButton route-name="contratos.contratada.servicos.pmqa.configuracao.ponto.importar"
            :param="{ contrato: props.contrato.id, servico: props.servico.id }" type-button="success" title="Buscar" />
        </div>
      </div>
    </div>
    <InputError :message="form.errors.num_contrato" />
  </div>
  <div class="row mb-4">
    <div class="col">
      <InputLabel value="Supervisora de Obras" for="supervisora_obras" />
      <input type="text" class="form-control" v-model="form.supervisora_obras">
      <InputError :message="form.errors.supervisora_obras" />
    </div>
    <div class="col">
      <InputLabel value="N° do contrato" for="num_contrato_supervisora" />
      <input type="text" class="form-control" v-model="form.num_contrato_supervisora">
      <InputError :message="form.errors.num_contrato_supervisora" />
    </div>
  </div>
  <div class="row mb-4">
    <div class="col">
      <InputLabel value="Objeto do contrato" for="obj_contrato_supervisora" />
      <textarea class="form-control" rows="5" v-model="form.obj_contrato_supervisora"></textarea>
      <InputError :message="form.errors.obj_contrato_supervisora" />
    </div>
  </div>
  <div class="row mb-4">
    <div class="col">
      <InputLabel value="Fiscal contrato" for="fiscal_contrato_supervisora" />
      <input type="text" class="form-control" v-model="form.fiscal_contrato_supervisora">
      <InputError :message="form.errors.fiscal_contrato_supervisora" />
    </div>
    <div class="col">
      <InputLabel value="Situação do contrato" for="situacao_contrato_supervisora" />
      <v-select :options="[]" label="nome" v-model="form.situacao_contrato_supervisora">
        <template #no-options="{}">
          Nenhum registro encontrado.
        </template>
      </v-select>
      <InputError :message="form.errors.situacao_contrato_supervisora" />
    </div>
  </div>
</template>