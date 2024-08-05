<script setup>
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import NavButton from "@/Components/NavButton.vue";
import { useForm } from "@inertiajs/vue3";
import { useToast } from "vue-toastification";
import { IconDeviceFloppy } from "@tabler/icons-vue";

const toast = useToast();
const props = defineProps({
  contrato: { type: Object },
  servico: { type: Object },
  lote: { type: Object }
});

const form = useForm({
  id: null,
  temContrato: props.lote.num_contrato_supervisora ? true : false,
  id_servico: props.servico.id,
  supervisora_obras: null,
  num_contrato_supervisora: null,
  obj_contrato_supervisora: null,
  fiscal_contrato_supervisora: null,
  situacao_contrato_supervisora: null,
  ...props.lote
});

const getDadosContrato = () => {
  const nr_contrato = form.num_contrato_supervisora?.replace(/\D/g, '');

  if (!nr_contrato) {
    toast.error('Número de contrato inválido!');
    return;
  }

  axios.get(route('contratos.get_contrato', nr_contrato))
    .then((response) => {
      toast.success('Contrato encontrado!');

      form.supervisora_obras = response.data.data[0].NO_EMPRESA;
      form.num_contrato_supervisora = response.data.data[0].NU_CON_FORMATADO;
      form.obj_contrato_supervisora = response.data.data[0].DS_OBJETO;
      form.fiscal_contrato_supervisora = response.data.data[0].NM_FISCAL;
      form.situacao_contrato_supervisora = response.data.data[0].DS_FAS_CONTRATO;
    })
    .catch((response) => {
      return console.log(response);
    });
}

const salvarObra = () => {
  form.id = props.lote.id;

  form.post(route('contratos.contratada.servicos.cont_ocorrencia.configuracao.lote_obra.update', { contrato: props.contrato.id, servico: props.servico.id }))
}
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
      <InputLabel value="Número do contrato" for="num_contrato_supervisora" />
      <div class="row g-2">
        <div class="col">
          <input type="text" class="form-control" v-model="form.num_contrato_supervisora">
        </div>
        <div class="col-auto">
          <NavButton @click="getDadosContrato()"
            route-name="contratos.contratada.servicos.pmqa.configuracao.ponto.importar"
            :param="{ contrato: props.contrato.id, servico: props.servico.id }" type-button="success" title="Buscar" />
        </div>
      </div>
    </div>
    <InputError :message="form.errors.num_contrato_supervisora" />
  </div>
  <div class="row mb-4">
    <div class="col">
      <InputLabel value="Supervisora de Obras" for="supervisora_obras" />
      <input type="text" class="form-control" v-model="form.supervisora_obras" :disabled="form.temContrato">
      <InputError :message="form.errors.supervisora_obras" />
    </div>
    <div class="col">
      <InputLabel value="N° do contrato" for="num_contrato_supervisora" />
      <input type="text" class="form-control" v-model="form.num_contrato_supervisora" :disabled="form.temContrato">
      <InputError :message="form.errors.num_contrato_supervisora" />
    </div>
  </div>
  <div class="row mb-4">
    <div class="col">
      <InputLabel value="Objeto do contrato" for="obj_contrato_supervisora" />
      <textarea class="form-control" rows="5" v-model="form.obj_contrato_supervisora"
        :disabled="form.temContrato"></textarea>
      <InputError :message="form.errors.obj_contrato_supervisora" />
    </div>
  </div>
  <div class="row mb-4">
    <div class="col">
      <InputLabel value="Fiscal contrato" for="fiscal_contrato_supervisora" />
      <input type="text" class="form-control" v-model="form.fiscal_contrato_supervisora" :disabled="form.temContrato">
      <InputError :message="form.errors.fiscal_contrato_supervisora" />
    </div>
    <div class="col">
      <InputLabel value="Situação do contrato" for="situacao_contrato_supervisora" />
      <v-select :options="['ATIVO', 'PARALISADO', 'CONCLUIDO', 'RESCINDIDO', 'SEM CONTRATO']"
        v-model="form.situacao_contrato_supervisora" :disabled="form.temContrato">
        <template #no-options="{}">
          Nenhum registro encontrado.
        </template>
      </v-select>
      <InputError :message="form.errors.situacao_contrato_supervisora" />
    </div>
  </div>
  <div class="row">
    <div class="col d-flex justify-content-end">
      <NavButton @click="salvarObra()" type-button="success" :icon="IconDeviceFloppy"
        :title="form.id ? 'Alterar' : 'Salvar'" />
    </div>
  </div>
</template>