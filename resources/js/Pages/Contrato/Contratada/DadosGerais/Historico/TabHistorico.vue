<script setup>
import InputError from '@/Components/InputError.vue';
import { router, useForm } from '@inertiajs/vue3';
import { IconDots } from '@tabler/icons-vue';

const props = defineProps({
  contrato: Object
});

const form = useForm({
  id: null,
  observacao: null,
  contrato_id: props.contrato.id
});

const enviarObservacao = () => {
  if (form.id) {
    form.patch(route('contratos.contratada.update_historico'), {
      onSuccess: () => form.reset()
    });
  } else {
    form.post(route('contratos.contratada.store_historico'), {
      onSuccess: () => form.reset()
    });
  }
}

const excluirObservacao = (observacao_id) => {
  router.delete(route('contratos.contratada.destroy_historico', observacao_id));
}

</script>

<template>
  <div class="mb-4">
    <!-- <h4>Histórico</h4> -->
    <div class="card-header mb-4 mt-4" v-if="can('contratos.contratada.store_historico')">
      <h3 class="my-0">Histórico</h3>
    </div>
    <div class="form-group mb-4" v-if="can('contratos.contratada.store_historico')">
      <textarea v-model="form.observacao" class="form-control" rows="5"></textarea>
      <InputError :message="form.errors.observacao" />
    </div>
    <div class="text-end" v-if="can('contratos.contratada.store_historico')">
      <button @click="enviarObservacao()" type="button" class="btn btn-success">Salvar</button>
    </div>
  </div>
  <div class="table-responsive mb-4">
    <table class="table table-hover non-hover">
      <thead>
        <tr>
          <th>Histórico</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="observacao in contrato.historico" :key="observacao.id">
          <td>{{ observacao.observacao }}</td>
          <td @click.stop>
            <span class="dropdown" v-if="can('contratos.contratada.dados_gerais.index')">
              <button class="btn dropdown-toggle align-text-top" data-bs-boundary="viewport" data-bs-toggle="dropdown"
                aria-expanded="false">
                <IconDots />
              </button>
              <div class="dropdown-menu dropdown-menu-end" style="">
                <a @click="Object.assign(form, observacao)" class="dropdown-item" href="javascript:void(0)" v-if="can('contratos.contratada.update_historico')">
                  Editar
                </a>
                <a @click="excluirObservacao(observacao.id)" class="dropdown-item" href="javascript:void(0)" v-if="can('contratos.contratada.destroy_historico')">
                  Excluir
                </a>
              </div>
            </span>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>