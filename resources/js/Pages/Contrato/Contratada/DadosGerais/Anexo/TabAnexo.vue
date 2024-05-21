<script setup>
import InputError from '@/Components/InputError.vue';
import { router, useForm } from '@inertiajs/vue3';
import { IconDots } from '@tabler/icons-vue';

const props = defineProps({
  contrato: Object
})

const form = useForm({
  id: null,
  contrato_id: props.contrato.id,
  caminho: null,
  arquivo: null,
  versao: null,
  descricao: null
})

const salvarAnexo = () => {
  if (form.id) {
    form.post(route('contratos.contratada.update_anexo'), {
      onSuccess: () => {
        form.reset();
        document.getElementById('inputfile').value = null;
      }
    });
  } else {
    form.post(route('contratos.contratada.store_anexo'), {
      onSuccess: () => {
        form.reset();
        document.getElementById('inputfile').value = null;
      }
    });
  }
}

const excluirAnexo = (anexo_id) => {
  router.delete(route('contratos.contratada.destroy_anexo', anexo_id));
}

</script>
<template>
  <div class="row mb-4">
    <div class="card-header mb-4 mt-4">
      <h3 class="my-0">Anexos</h3>
    </div>
    <div class="col">
      <label class="form-label">Anexo:</label>
      <input @input="form.arquivo = $event.target.files[0]" id="inputfile" type="file" class="form-control">
    </div>
    <div class="col">
      <label class="form-label">Nome</label>
      <div class="row g-2">
        <div class="col">
          <input v-model="form.descricao" type="text" class="form-control">
          <InputError :message="form.errors.descricao" />
        </div>
        <div class="col-auto">
          <a @click="salvarAnexo()" href="javascript:void(0)" class="btn btn-success" aria-label="Button">
            Salvar
          </a>
        </div>
      </div>
    </div>
  </div>

  <div class="table-responsive mb-4">
    <table class="table table-hover non-hover">
      <thead>
        <tr>
          <th>Nome</th>
          <th>Versão</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="anexo in contrato.anexos" :key="anexo.id">
          <td>{{ anexo.descricao }}</td>
          <td>{{ anexo.versao }}</td>
          <td @click.stop>
            <span class="dropdown">
              <button class="btn dropdown-toggle align-text-top" data-bs-boundary="viewport" data-bs-toggle="dropdown"
                aria-expanded="false">
                <IconDots />
              </button>
              <div class="dropdown-menu dropdown-menu-end" style="">
                <a class="dropdown-item" target="_blank"
                  :href="route('contratos.contratada.visualizar_anexo', anexo.id)">
                  Visualizar
                </a>
                <a @click="Object.assign(form, anexo)" class="dropdown-item" href="javascript:void(0)">
                  Editar
                </a>
                <a @click="excluirAnexo(anexo.id)" class="dropdown-item" href="javascript:void(0)">
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