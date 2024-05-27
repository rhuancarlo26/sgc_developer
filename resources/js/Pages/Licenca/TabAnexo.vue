<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import { useForm } from '@inertiajs/vue3';
import { IconDots, IconTrash } from '@tabler/icons-vue';

const props = defineProps({
  licenca: { type: Object }
});

const form = useForm({
  licenca_id: props.licenca.id,
  documento: null,
  shapefile: null
})

const salvarDocumento = () => {
  form.post(route('licenca.documento.store'));
}

const deleteDocumento = () => {
  form.delete(route('licenca.documento.delete', props.licenca.documento.id));
}

const salvarShapefile = () => {
  form.post(route('licenca.shapefile.store'));
}

const deleteShapefile = () => {
  form.delete(route('licenca.shapefile.delete', props.licenca.shapefile.id));
}

</script>
<template>
  <div class="row">
    <div class="col">
      <div v-if="!licenca.documento?.id" class="mb-4">
        <InputLabel value="Arquivo" for="documento" />
        <div class="row g-2">
          <div class="col">
            <input @input="form.documento = $event.target.files[0]" type="file" class="form-control">
          </div>
          <div class="col-auto">
            <a @click="salvarDocumento()" href="#" class="btn btn-success" aria-label="Button"
              :disabled="form.processing">
              Enviar
            </a>
          </div>
        </div>
        <!-- </form> -->
        <InputError :message="form.errors.documento" />
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
                    <a @click="deleteDocumento()" class="btn align-text-top btn-danger m-1" title="Excluir"
                      href="javascript:void(0)">
                      <IconTrash />
                    </a>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
    <div class="col">
      <div v-if="!licenca.shapefile?.id" class="mb-4">
        <InputLabel value="Shapefile" for="shapefile" />
        <div class="row g-2">
          <div class="col">
            <input @input="form.shapefile = $event.target.files[0]" name="shapefile" id="shapefile" type="file"
              class="form-control">
          </div>
          <div class="col-auto">
            <a @click="salvarShapefile()" href="#" class="btn btn-success" aria-label="Button"
              :disabled="form.processing">
              Enviar
            </a>
          </div>
        </div>
        <!-- </form> -->
        <InputError :message="form.errors.shapefile" />
      </div>
      <div v-else>
        <div class="card">
          <div class="table-responsive">
            <table class="table card-table table-bordered">
              <thead>
                <tr>
                  <th>Nome</th>
                  <th>Ação</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>{{ licenca.shapefile?.nome_arquivo }}</td>
                  <td>
                    <a @click="deleteShapefile()" class="btn align-text-top btn-danger m-1" title="Excluir"
                      href="javascript:void(0)">
                      <IconTrash />
                    </a>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
