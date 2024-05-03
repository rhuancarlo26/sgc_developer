<script setup>
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import { router, useForm } from '@inertiajs/vue3';
import { IconDots, IconDeviceFloppy } from '@tabler/icons-vue';

const props = defineProps({
  servico: Object,
  licencasLi: Array
});

const form = useForm({
  contrato_id: props.servico.contrato_id,
  servico_id: props.servico.id,
  licenca: null,
  condicionante: null
});

const salvarLicencaCondicionante = () => {
  form.post(route('contratos.contratada.servicos.condicionante.store'));
}

const excluirLicencaCondicionante = (condicionante_id, licenca_id) => {
  router.post(route('contratos.contratada.servicos.condicionante.delete'), {
    contrato_id: props.servico.contrato_id,
    servico_id: props.servico.id,
    licenca_id: licenca_id,
    condicionante_id: condicionante_id
  })
}

</script>
<template>
  <form @submit.prevent="salvarLicencaCondicionante()">
    <div class="row mb-4">
      <div class="col form-group">
        <InputLabel value="Licença" for="licenca" />
        <v-select :options="licencasLi" label="numero_licenca" v-model="form.licenca">
          <template #no-options="{}">
            Nenhum registro encontrado.
          </template>
        </v-select>
        <InputError :message="form.errors.licenca_id" />
      </div>
      <div class="col form-group">
        <InputLabel value="Condicionante" for="condicionante" />
        <select name="condicionante" id="condicionante" class="form-select form-control" v-model="form.condicionante">
          <option v-for="condicionante in form.licenca?.condicionantes" :key="condicionante.id" :value="condicionante">
            {{
              condicionante.numero_condicionante }}</option>
        </select>
        <InputError :message="form.errors.licenca_id" />
      </div>
    </div>
    <div class="row">
      <div class="mb-4 d-flex justify-content-end">
        <button type="submit" class="btn btn-success" :disabled="form.processing">
          <IconDeviceFloppy class="me-2" />
          Salvar
        </button>
      </div>
    </div>
  </form>

  <div>
    <div class="card-header">
      <h3 class="my-0">Licenças</h3>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-hover non-hover">
          <thead>
            <tr>
              <th>Licença</th>
              <th>Condicionante</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="condicionante in servico.condicionantes" :key="condicionante.id">
              <td>{{ condicionante.licenca?.numero_licenca }}</td>
              <td>{{ condicionante.descricao }}</td>
              <td>
                <button type="button" class="btn btn-icon btn-info dropdown-toggle p-2" data-bs-boundary="viewport"
                  data-bs-toggle="dropdown" aria-expanded="false">
                  <IconDots />
                </button>
                <div class="dropdown-menu dropdown-menu-end">
                  <a @click="excluirLicencaCondicionante(condicionante.id, condicionante.licenca?.id)"
                    class="dropdown-item" href="javascript:void(0)">
                    Excluir
                  </a>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>