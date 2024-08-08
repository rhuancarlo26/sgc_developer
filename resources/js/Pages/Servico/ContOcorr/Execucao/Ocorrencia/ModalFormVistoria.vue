<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import Navbar from "../../Navbar.vue";
import { Head, Link, router, useForm } from "@inertiajs/vue3";
import ModelSearchFormAllColumns from "@/Components/ModelSearchFormAllColumns.vue";
import Table from "@/Components/Table.vue";
import NavButton from "@/Components/NavButton.vue";
import LinkConfirmation from "@/Components/LinkConfirmation.vue";
import { ref } from "vue";
import { IconEye } from "@tabler/icons-vue";
import { IconPlus } from "@tabler/icons-vue";
import { IconPencil } from "@tabler/icons-vue";
import { IconTrash } from "@tabler/icons-vue";
import NavLink from "@/Components/NavLink.vue";
import { IconMap } from "@tabler/icons-vue";
import { dateTimeFormat } from "@/Utils/DateTimeUtils";
import Modal from "@/Components/Modal.vue";
import { usePage } from "@inertiajs/vue3";
import { IconDeviceFloppy } from "@tabler/icons-vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";

const modalFormVistoria = ref();
const ocorrencia = ref({});

const props = defineProps({
  contrato: { type: Object },
  servico: { type: Object },
  ocorrencias: { type: Object }
});

const form = useForm({
  ocorrencias: []
});


const abrirModal = (item) => {
  ocorrencia.value = {};

  if (item) {
    ocorrencia.value = item;
  }
  modalFormVistoria.value.getBsModal().show();
}

const enviarOcorrencias = () => {
  form.post(route('contratos.contratada.servicos.cont_ocorrencia.execucao.ocorrencia.enviar_ocorrencia', { contrato: props.contrato.id, servico: props.servico.id }), {
    onSuccess: () => modalFormVistoria.value.getBsModal().hide()
  });
}

defineExpose({ abrirModal });
</script>

<template>
  <Modal ref="modalFormVistoria" title="Cadastro de vistorias" modal-dialog-class="modal-xl">
    <template #body>
      <div class="row mb-4">
        <div class="col">
          <InputLabel value="ID ocorrência" for="nome_id" />
          <input type="text" class="form-control" :value="ocorrencia.nome_id" disabled>
          <InputError :message="form.errors.nome_id" />
        </div>
        <div class="col">
          <InputLabel value="Data da ocorrência" for="data_ocorrencia" />
          <input type="date" class="form-control" :value="ocorrencia.data_ocorrencia" disabled>
          <InputError :message="form.errors.data_ocorrencia" />
        </div>
      </div>
      <div class="row mb-4">
        <div class="col">
          <InputLabel value="ID vistoria" for="nome_id" />
          <input type="text" class="form-control" v-model="form.nome_id">
          <InputError :message="form.errors.nome_id" />
        </div>
        <div class="col">
          <InputLabel value="Data da vistoria" for="data_vistoria" />
          <input type="date" class="form-control" v-model="form.data_vistoria">
          <InputError :message="form.errors.data_vistoria" />
        </div>
      </div>
      <div class="row mb-4">
        <div class="col align-content-center">
          <InputLabel value="Ocorrência corrigida" for="corrigido" />
          <div>
            <label class="form-check form-check-inline">
              <input class="form-check-input" type="radio" :value="true" v-model="form.corrigido">
              <span class="form-check-label">Sim</span>
            </label>
            <label class="form-check form-check-inline">
              <input class="form-check-input" type="radio" :value="false" v-model="form.corrigido">
              <span class="form-check-label">Não</span>
            </label>
          </div>
          <InputError :message="form.errors.corrigido" />
        </div>
        <div v-if="form.corrigido" class="col">
          <InputLabel value="Data fim" for="data_fim" />
          <input type="date" class="form-control" v-model="form.data_fim">
          <InputError :message="form.errors.data_fim" />
        </div>
      </div>
      <div class="row mb-4">
        <div class="col">
          <InputLabel value="Intensidade de Ocorrência" for="intensidade_vistoria" />
          <select class="form-control form-select" v-model="form.intensidade_vistoria">
            <option value="Leve">Leve</option>
            <option value="Moderada">Moderada</option>
            <option value="Grave">Grave</option>
          </select>
          <InputError :message="form.errors.intensidade_vistoria" />
        </div>
        <div class="col">
          <InputLabel value="Tipo de Ocorrência" for="tipo_vistoria" />
          <input type="text" class="form-control" v-model="form.tipo_vistoria">
          <InputError :message="form.errors.tipo_vistoria" />
        </div>
      </div>
      <div v-if="!form.corrigido" class="row mb-4">
        <div class="col align-content-center">
          <InputLabel value="Realizado Acordo de Prazo" for="acordo_prazo" />
          <div>
            <label class="form-check form-check-inline">
              <input class="form-check-input" type="radio" :value="true" v-model="form.acordo_prazo">
              <span class="form-check-label">Sim</span>
            </label>
            <label class="form-check form-check-inline">
              <input class="form-check-input" type="radio" :value="false" v-model="form.acordo_prazo">
              <span class="form-check-label">Não</span>
            </label>
          </div>
          <InputError :message="form.errors.acordo_prazo" />
        </div>
        <div class="col">
          <InputLabel value="Prazo para Correção (dias)" for="prazo_vistoria" />
          <input type="text" class="form-control" v-model="form.prazo_vistoria">
          <InputError :message="form.errors.prazo_vistoria" />
        </div>
      </div>
      <div class="row mb-4">
        <div class="col">
          <InputLabel value="Observações da Vistoria" for="obs_vistoria" />
          <textarea rows="6" class="form-control" v-model="form.obs_vistoria"></textarea>
          <InputError :message="form.errors.obs_vistoria" />
        </div>
      </div>
      <div class="row mb-4">
        <div class="col d-flex justify-content-end">
          <NavButton type-button="success" :icon="IconDeviceFloppy" :title="form.id ? 'Alterar' : 'Salvar'" />
        </div>
      </div>
      <div class="row col mb-4">
        <div class="card-body">
          <div class="table-responsive">
            <table class="table card-table table-bordered table-hover">
              <thead>
                <tr>
                  <th>ID vistoria</th>
                  <th>Data da vistoria</th>
                  <th>Adicionar arquivos</th>
                  <th>Ação</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </template>
    <template #footer>
    </template>
  </Modal>
</template>