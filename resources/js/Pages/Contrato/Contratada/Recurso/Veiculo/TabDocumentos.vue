<script setup>
import Table from '@/Components/Table.vue';
import { Head, Link, router, useForm } from "@inertiajs/vue3";
import { IconDeviceFloppy, IconDoorExit, IconDots, IconEye, IconTrash } from "@tabler/icons-vue";

const props = defineProps({
  contrato: Object,
  veiculo: Object,
  codigos: Array
});

const form_documento = useForm({
  recurso_veiculo_id: props.veiculo.id,
  contrato_id: props.contrato.id,
  documentos: null
});

const salvarDocumentoVeiculo = () => {
  form_documento.recurso_veiculo_id = props.veiculo.id;

  form_documento.post(route('contratos.contratada.recurso.veiculo.store_documento'))
}

const destroyDocumentoVeiculo = (documento_id) => {
  router.delete(route('contratos.contratada.recurso.veiculo.destroy', { contrato: props.contrato.id, documento: documento_id }))
}
</script>
<template>
  <div class="row mb-4">
    <div class="col">
      <small>Devem ser inseridos os seguintes documentos: Fotos do veículo, foto da placa e
        documentos do
        veiculos</small>
      <div class="row g-2 mt-1">
        <div class="col">
          <input @input="form_documento.documentos = $event.target.files" type="file" id="anexo" name="anexo"
            class="form-control" multiple>
        </div>
        <div class="col-auto">
          <a @click="salvarDocumentoVeiculo()" href="javascript:void(0)" class="btn btn-success" aria-label="Button">
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
        <tr v-for="documento in veiculo.documentos" :key="documento.id">
          <td>{{ documento.nome }}</td>
          <td>
            <a target="_blank" class="btn btn-info me-2"
              :href="route('contratos.contratada.recurso.veiculo.visualizar', documento.id)">
              <IconEye />
            </a>
            <button @click="destroyDocumentoVeiculo(documento.id)" type="button" class="btn btn-danger">
              <IconTrash />
            </button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>