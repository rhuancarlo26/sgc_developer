<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import LinkConfirmation from '@/Components/LinkConfirmation.vue';
import { dateTimeFormat } from '@/Utils/DateTimeUtils';
import { Link, useForm } from '@inertiajs/vue3';
import { IconPencil, IconTrash, IconX } from '@tabler/icons-vue';

const props = defineProps({
  tipo: { type: Object },
  contrato: { type: Object }
})

const form = useForm({
  id: null,
  tipo_id: props.tipo.id,
  contrato_id: props.contrato.id,
  aditivo: null,
  valor: null,
  publicacao: null,
  data_inicio_vigencia: null,
  numero_sei: null
});

const salvarAditivo = () => {
  if (form.id) {
    form.patch(route('contratos.gestao.aditivo.update'), {
      onSuccess: () => form.reset()
    })
  } else {
    form.post(route('contratos.gestao.aditivo.store'), {
      onSuccess: () => form.reset()
    })
  }
};

const editarAditivo = (aditivo) => {
  Object.assign(form, aditivo);
}

</script>
<template>
  <div class="card-header">
    <h3 class="my-0">Aditivo</h3>
  </div>
  <div class="card-body">
    <form method="post" @submit.prevent="salvarAditivo()" :disabled="form.processing">
      <div class="row">
        <div class="col">
          <InputLabel value="Aditivo" for="aditivo" />
          <input type="text" id="aditivo" name="aditivo" class="form-control" v-model="form.aditivo">
          <InputError :message="form.errors.aditivo" />
        </div>
        <div class="col">
          <InputLabel value="Valor" for="valor" />
          <input type="number" step="any" id="valor" name="valor" class="form-control" v-model="form.valor">
          <InputError :message="form.errors.valor" />
        </div>
        <div class="col">
          <InputLabel value="Publicação" for="publicacao" />
          <input type="text" id="publicacao" name="publicacao" class="form-control" v-model="form.publicacao">
          <InputError :message="form.errors.publicacao" />
        </div>
        <div class="col">
          <InputLabel value="Inicio da Vigencia" for="data_inicio_vigencia" />
          <input type="date" id="data_inicio_vigencia" name="data_inicio_vigencia" class="form-control"
            v-model="form.data_inicio_vigencia">
          <InputError :message="form.errors.data_inicio_vigencia" />
        </div>
        <div class="col">
          <InputLabel value="Numero SEI" for="numero_sei" />
          <div class="row g-2">
            <div class="col">
              <input type="text" id="numero_sei" name="numero_sei" class="form-control" v-model="form.numero_sei">
            </div>
            <div class="col-auto">
              <button v-if="form.id" @click="form.reset()" type="button" class="btn btn-icon btn-danger me-2">
                <IconX />
              </button>
              <button type="submit" class="btn btn-success">
                {{ form.id ? 'Editar' : 'Salvar' }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>

  <div class="card-body">
    <div class="table-responsive mb-4">
      <table class="table card-table table-bordered table-hover">
        <thead>
          <tr>
            <th>Aditivo</th>
            <th>Valor</th>
            <th>Publicação</th>
            <th>Inicio da vigencia</th>
            <th>Numero SEI</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="aditivo in contrato.aditivos" :key="aditivo.id" class="cursor-pointer">
            <td>{{ aditivo.aditivo }}</td>
            <td>{{ aditivo.valor }}</td>
            <td>{{ aditivo.publicacao }}</td>
            <td>{{ dateTimeFormat(aditivo.data_inicio_vigencia) }}</td>
            <td>{{ aditivo.numero_sei }}</td>
            <td>
              <button @click="editarAditivo(aditivo)" type="button" class="btn btn-icon btn-info me-2"
                :disabled="form.processing">
                <IconPencil />
              </button>
              <LinkConfirmation v-slot="confirmation"
                :options="{ text: 'Aditivos removidos não podem ser restaurados' }">
                <Link :onBefore="confirmation.show"
                  :href="route('contratos.gestao.aditivo.destroy', { tipo: tipo.id, aditivo: aditivo.id })"
                  method="DELETE" as="button" type="button" class="btn btn-icon btn-danger">
                <IconTrash />
                </Link>
              </LinkConfirmation>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>