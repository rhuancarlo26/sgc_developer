<script setup>

import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import ModelSearchForm from "@/Components/ModelSearchForm.vue";
import Table from '@/Components/Table.vue';
import Navbar from "../../Navbar.vue";
import { Head, Link, router, useForm } from "@inertiajs/vue3";
import { ref } from "vue";
import { IconDeviceFloppy, IconDoorExit, IconDots } from "@tabler/icons-vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";

const props = defineProps({
  contrato: Object,
  veiculo: Object,
  codigos: Array
})

const form = useForm({
  id: null,
  contrato_id: props.contrato.id,
  veiculo_codigo_id: null,
  codigo: null,
  descricao: null,
  alugado: null,
  placa_veiculo: null,
  ultima_revisao: null,
  observacao: null,
  ...props.veiculo
});

const form_documento = useForm({
  recurso_veiculo_id: props.veiculo.id,
  contrato_id: props.contrato.id,
  documentos: null
})

const salvarVaiculo = () => {
  form.veiculo_codigo_id = form.codigo?.id;
  form.descricao = form.codigo?.descricao;

  if (form.id) {
    form.patch(route('contratos.contratada.recurso.veiculo.update'));
  } else {
    form.post(route('contratos.contratada.recurso.veiculo.store'));
  }
}

const salvarDocumentoVeiculo = () => {
  form_documento.recurso_veiculo_id = props.veiculo.id;

  form_documento.post(route('contratos.contratada.recurso.veiculo.store_documento'))
}

const destroyDocumentoVeiculo = (documento_id) => {
  router.delete(route('contratos.contratada.recurso.veiculo.destroy', { contrato: props.contrato.id, documento: documento_id }))
}

</script>

<template>

  <Head :title="`${contrato.contratada.slice(0, 10)}...`" />

  <AuthenticatedLayout>

    <template #header>
      <div class="w-100 d-flex justify-content-between">
        <Breadcrumb class="align-self-center" :links="[
          { route: route('contratos.contratada.recurso.veiculo.index', contrato.id), label: `Veiculos` },
          { route: '#', label: contrato.contratada }
        ]
          " />
        <Link class="btn btn-info" :href="route('contratos.contratada.recurso.veiculo.index', contrato.id)">
        <IconDoorExit class="me-2" />
        Voltar
        </Link>
      </div>
    </template>

    <Navbar :contrato="contrato">

      <template #body>
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
            <div class="d-flex col">
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
              <input id="placa_veiculo" name="placa_veiculo" type="text" class="form-control"
                v-model="form.placa_veiculo">
              <InputError :message="form.errors.placa_veiculo" />
            </div>
            <div class="col">
              <InputLabel value="Data última revisão" for="ultima_revisao" />
              <input id="ultima_revisao" name="ultima_revisao" type="date" class="form-control"
                v-model="form.ultima_revisao">
              <InputError :message="form.errors.ultima_revisao" />
            </div>
          </div>
          <div class="row mb-4">
            <div class="col">
              <InputLabel value="Observação" for="observacao" />
              <textarea id="observacao" name="observacao" v-model="form.observacao" class="form-control"
                rows="5"></textarea>
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
        <div v-if="veiculo.id">
          <div class="row mb-4">
            <div class="col">
              <InputLabel value="Fotos do Veiculo" for="anexo" />
              <div class="row g-2">
                <div class="col">
                  <input @input="form_documento.documentos = $event.target.files" type="file" id="anexo" name="anexo"
                    class="form-control" multiple>
                </div>
                <div class="col-auto">
                  <a @click="salvarDocumentoVeiculo()" href="javascript:void(0)" class="btn btn-success"
                    aria-label="Button">
                    Salvar
                  </a>
                </div>
              </div>
            </div>
          </div>

          <div class="table-responsive">
            <table class="table table-hover non-hover">
              <thead>
                <tr>
                  <th>Nome</th>
                  <th>Açao</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="documento in veiculo.documentos " :key="documento.id">
                  <td>{{ documento.nome }}</td>
                  <td>
                    <span class="dropdown">
                      <button class="btn dropdown-toggle align-text-top" data-bs-boundary="viewport"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <IconDots />
                      </button>
                      <div class="dropdown-menu dropdown-menu-end">
                        <a class="dropdown-item" target="_blank"
                          :href="route('contratos.contratada.recurso.veiculo.visualizar', documento.id)">
                          Visualizar
                        </a>
                        <a @click="destroyDocumentoVeiculo(documento.id)" class="dropdown-item"
                          href="javascript:void(0)">
                          Excluir
                        </a>
                      </div>
                    </span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </template>
    </Navbar>

  </AuthenticatedLayout>
</template>
