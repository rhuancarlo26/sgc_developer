<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import { useForm } from '@inertiajs/vue3';
import { IconDots } from '@tabler/icons-vue';

const props = defineProps({
  licenca: { type: Object }
});

const form = useForm({
  licenca_id: props.licenca.id,
  documento: null
})

const salvarDocumento = () => {
  form.post(route('licenca.documento.store'));
}

const deleteDocumento = () => {
  form.delete(route('licenca.documento.delete', props.licenca.documento.id));
}

</script>
<template>
  <div v-if="!licenca.documento?.id" class="mb-4">
    <InputLabel value="Arquivo" for="documento" />
    <div class="row g-2">
      <div class="col">
        <input @input="form.documento = $event.target.files[0]" type="file" class="form-control">
      </div>
      <div class="col-auto">
        <a @click="salvarDocumento()" href="#" class="btn btn-success" aria-label="Button" :disabled="form.processing">
          Enviar
        </a>
      </div>
    </div>
    <!-- </form> -->
    <InputError :message="form.errors.extensao" />
  </div>
  <div v-else>
    <div class="card">
      <div class="table-responsive">
        <table class="table card-table table-bordered" :class="tableClass">
          <thead>
            <tr>
              <th>Nome</th>
              <th>Ação</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>{{ licenca.documento?.nome }}</td>
              <td>
                <button type="button" class="btn btn-icon btn-info dropdown-toggle p-2" data-bs-boundary="viewport"
                  data-bs-toggle="dropdown" aria-expanded="false">
                  <IconDots />
                </button>
                <div class="dropdown-menu dropdown-menu-end">
                  <a @click="deleteDocumento()" class="dropdown-item" href="javascript:void(0)">
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