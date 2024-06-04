<script setup>

import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import Table from '@/Components/Table.vue';
import Navbar from "../../Navbar.vue";
import { Head, Link, router, useForm } from "@inertiajs/vue3";
import { IconDeviceFloppy, IconDoorExit, IconDots } from "@tabler/icons-vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";

const props = defineProps({
  contrato: Object,
  equipamento: Object
})

const form = useForm({
  id: null,
  contrato_id: props.contrato.id,
  alugado: null,
  numero_serie: null,
  ultima_calibracao: null,
  nome: null,
  descricao: null,
  atividade: null,
  observacao: null,
  ...props.equipamento
});

const form_documento = useForm({
  equipamento_id: props.equipamento.id,
  contrato_id: props.contrato.id,
  documentos: null
});

const salvarEquipamento = () => {
  if (form.id) {
    form.patch(route('contratos.contratada.recurso.equipamento.update'))
  } else {
    form.post(route('contratos.contratada.recurso.equipamento.store'))
  }
}

const salvarDocumentoEquipamento = () => {
  form_documento.equipamento_id = props.equipamento.id;

  form_documento.post(route('contratos.contratada.recurso.equipamento.store_documento'))
}

const destroyDocumentoEquipamento = (documento_id) => {
  router.delete(route('contratos.contratada.recurso.equipamento.destroy', { contrato: props.contrato.id, documento: documento_id }))
}

</script>

<template>

  <Head :title="`${contrato.contratada.slice(0, 10)}...`" />

  <AuthenticatedLayout>

    <template #header>
      <div class="w-100 d-flex justify-content-between">
        <Breadcrumb class="align-self-center" :links="[
          { route: route('contratos.contratada.recurso.equipamento.index', contrato.id), label: `Equipamentos` },
          { route: '#', label: contrato.contratada }
        ]
          " />
        <Link class="btn btn-info" :href="route('contratos.contratada.recurso.equipamento.index', contrato.id)">
        <IconDoorExit class="me-2" />
        Voltar
        </Link>
      </div>
    </template>

    <Navbar :contrato="contrato">

      <template #body>
        <form @submit.prevent="salvarEquipamento()">
          <div class="row mb-4">
            <div class="col">
              <InputLabel value="Nome" for="nome" />
              <input id="nome" name="nome" v-model="form.nome" class="form-control" />
              <InputError :message="form.errors.nome" />
            </div>
            <div class="col">
              <InputLabel value="Descrição" for="descricao" />
              <input id="descricao" name="descricao" v-model="form.descricao" class="form-control" />
              <InputError :message="form.errors.descricao" />
            </div>
          </div>
          <div class="row mb-4">
            <div class="col-6">
              <InputLabel value="Número de série" for="numero_serie" />
              <input type="text" id="numero_serie" name="numero_serie" v-model="form.numero_serie"
                class="form-control" />
              <InputError :message="form.errors.numero_serie" />
            </div>
            <div class="col-2">
              <InputLabel value="Data da última calibração" for="ultima_calibracao" />
              <input type="date" id="ultima_calibracao" name="ultima_calibracao" v-model="form.ultima_calibracao"
                class="form-control" />
              <InputError :message="form.errors.ultima_calibracao" />
            </div>
              <div class="col">
                <InputLabel value="Alugado"/>
                  <div class="d-flex align-items-center">
                    <input type="checkbox" class="form-check-input" v-model="form.alugado">
                  </div>
                <InputError :message="form.errors.alugado" />
              </div>
          </div>
          <div class="row mb-4">
            <div class="col">
              <InputLabel value="Atividade relacional" for="atividade" />
              <textarea id="atividade" name="atividade" v-model="form.atividade" class="form-control"
                rows="5"></textarea>
              <InputError :message="form.errors.atividade" />
            </div>
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
        <div v-if="equipamento.id">
          <div class="row mb-4">
            <div class="col">
              <InputLabel value="Fotos do equipamento" for="anexo" />
              <div class="row g-2">
                <div class="col">
                  <input @input="form_documento.documentos = $event.target.files" type="file" id="anexo" name="anexo"
                    class="form-control" multiple>
                </div>
                <div class="col-auto">
                  <a @click="salvarDocumentoEquipamento()" href="javascript:void(0)" class="btn btn-success"
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
                <tr v-for="documento in equipamento.documentos " :key="documento.id">
                  <td>{{ documento.nome }}</td>
                  <td>
                    <span class="dropdown">
                      <button class="btn dropdown-toggle align-text-top" data-bs-boundary="viewport"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <IconDots />
                      </button>
                      <div class="dropdown-menu dropdown-menu-end">
                        <a class="dropdown-item" target="_blank"
                          :href="route('contratos.contratada.recurso.equipamento.visualizar', documento.id)">
                          Visualizar
                        </a>
                        <a @click="destroyDocumentoEquipamento(documento.id)" class="dropdown-item"
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
