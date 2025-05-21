<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import { useForm } from '@inertiajs/vue3';
import { IconDots, IconTrash } from '@tabler/icons-vue';
import {usePage} from "@inertiajs/vue3";

const props = defineProps({
  licenca: { type: Object }
});

const form = useForm({
  licenca_id: props.licenca.id,
  documento: null,
  documentoTermo: null,
  shapefile: null
})

const salvarDocumento = () => {
  form.licenca_id = props.licenca.id;

  form.post(route('licenca.documento.store'));
}

const salvarDocumentoTermo = () => {
  form.licenca_id = props.licenca.id;

  form.post(route('licenca.documento.termo.store'));
}


const deleteDocumento = () => {
  form.delete(route('licenca.documento.delete', props.licenca.id));
}

const deleteDocumentoTermo = () => {
  form.delete(route('licenca.documento.termo.delete', props.licenca.id));
}

const salvarShapefile = () => {
  form.licenca_id = props.licenca.id;

  form.post(route('licenca.shapefile.store'));
}

const deleteShapefile = () => {
  form.delete(route('licenca.shapefile.delete', props.licenca.shapefile.id));
}

</script>
<template>
  <div class="row">
    <!-- Termo de Referência -->
    <div class="col">
      <div v-if="!licenca.arquivo_termo" class="mb-4">
        <InputLabel value="Termo de referência" for="documentoTermo" />
        <div class="row g-2">
          <div class="col">
            <input @input="form.documentoTermo = $event.target.files[0]" type="file" class="form-control">
          </div>
          <div class="col-auto">
            <a @click="salvarDocumentoTermo()" href="#" class="btn btn-success" aria-label="Button"
              :disabled="form.processing">
              Enviar
            </a>
          </div>
        </div>
        <!-- </form> -->
        <InputError :message="form.errors.documentoTermo" />
      </div>
      <div v-else>
        <InputLabel value="Termo de referência" for="documentoTermo" />
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
                  <td>{{ licenca.arquivo_termo.split('__')[1] }}</td>
                  <td>
                    <a :href="`${usePage().props.app_url}/storage/${licenca.arquivo_termo}`" target="_blank" class="btn btn-primary m-1">
                      Ver Termo
                    </a>
                    <a @click="deleteDocumentoTermo()" class="btn  btn-danger m-1" title="Excluir"
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

    <!-- Licença -->
    <div class="col">
      <div v-if="!licenca.arquivo_licenca" class="mb-4">
        <InputLabel value="Licença" for="documento" />
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
        <InputLabel value="Licença" for="documento" />
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
                  <td>{{ licenca.arquivo_licenca.split('__')[1] }}</td>
                  <td>
                    <a :href="`${usePage().props.app_url}/storage/${licenca.arquivo_licenca}`" target="_blank" class="btn btn-primary m-1">
                      Ver Licenca
                    </a>

                    <a @click="deleteDocumento()" class="btn  btn-danger m-1" title="Excluir"
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

    <!-- Shapefile -->
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
