<script setup>
import { Head, Link, router, useForm } from "@inertiajs/vue3";
import { IconDeviceFloppy, IconDoorExit, IconDots } from "@tabler/icons-vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";

const props = defineProps({
  contrato: Object,
  veiculo: Object,
  codigos: Array
});

const form = useForm({
  id: null,
  contrato_id: props.contrato.id,
  veiculo_codigo_id: null,
  codigo: null,
  descricao: null,
  alugado: null,
  placa_veiculo: null,
  ultima_revisao: null,
  km_inicial: null,
  observacao: null,
  ...props.veiculo
});

const salvarVaiculo = () => {
  form.veiculo_codigo_id = form.codigo?.id;
  form.descricao = form.codigo?.descricao;

  if (form.id) {
    form.patch(route('contratos.contratada.recurso.veiculo.update'));
  } else {
    form.post(route('contratos.contratada.recurso.veiculo.store'));
  }
}
</script>
<template>
  <form @submit.prevent="salvarVaiculo()">
    <div class="row mb-4">
      <div class="col">
        <InputLabel value="Código" for="codigo" />
        <v-select :options="codigos" id="codigo" name="codigo" label="codigo" v-model="form.codigo">
          <template #no-options="{}">
            Nenhum registro encontrado.
          </template>
        </v-select>
        <InputError :message="form.errors.veiculo_codigo_id" />
      </div>
      <div class="col">
        <InputLabel value="Descrição" for="descricao" />
        <input id="descricao" name="descricao" type="text" class="form-control" :value="form.codigo?.descricao"
          disabled>
        <InputError :message="form.errors.descricao" />
      </div>
    </div>
    <div class="row mb-4">
      <div class="d-flex col-2">
        <div class="d-flex align-items-center">
          <label class="form-check">
            <input type="checkbox" class="form-check-input" v-model="form.alugado">
            <span class="form-check-label">Alugado</span>
          </label>
        </div>
        <InputError :message="form.errors.alugado" />
      </div>
      <div class="col">
        <InputLabel value="Placa do veiculo" for="placa_veiculo" />
        <input id="placa_veiculo" name="placa_veiculo" type="text" class="form-control" v-model="form.placa_veiculo">
        <InputError :message="form.errors.placa_veiculo" />
      </div>
      <div class="col">
        <InputLabel value="Data última revisão" for="ultima_revisao" />
        <input id="ultima_revisao" name="ultima_revisao" type="date" class="form-control" v-model="form.ultima_revisao">
        <InputError :message="form.errors.ultima_revisao" />
      </div>
      <div class="col">
        <InputLabel value="Km inicial" for="km_inicial" />
        <input id="km_inicial" name="km_inicial" type="number" step="any" class="form-control"
          v-model="form.km_inicial">
      </div>
    </div>
    <div class="row mb-4">
      <div class="col">
        <InputLabel value="Observação" for="observacao" />
        <textarea id="observacao" name="observacao" v-model="form.observacao" class="form-control" rows="5"></textarea>
        <InputError :message="form.errors.observacao" />
      </div>
    </div>
    <div class="mb-4 d-flex justify-content-end">
      <button type="submit" class="btn btn-success" :disabled="form.processing">
        <IconDeviceFloppy class="me-2" />
        {{ form.id ? 'Editar' : 'Salvar' }}
      </button>
    </div>
  </form>
</template>