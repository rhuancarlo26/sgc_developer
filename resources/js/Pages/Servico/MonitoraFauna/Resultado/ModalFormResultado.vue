<script setup>
import InputLabel from "@/Components/InputLabel.vue";
import Modal from "@/Components/Modal.vue";
import { Link, router, useForm } from "@inertiajs/vue3";
import { computed, ref } from "vue";
import { dateTimeFormat } from "@/Utils/DateTimeUtils.js";
import { IconTrash } from "@tabler/icons-vue";
import Table from "@/Components/Table.vue";
import LinkConfirmation from "@/Components/LinkConfirmation.vue";
import NavButton from "@/Components/NavButton.vue";
import InputError from "@/Components/InputError.vue";

const props = defineProps({
  contrato: { type: Object },
  servico: { type: Object },
  campanhas: { type: Array }
})

const modalRef = ref({});

const form = useForm({
  id: null,
  id_servico: props.servico.id,
  nome: null,
  data: null,
  campanha: null,
  campanhas: []
});

const abrirModal = (item) => {
  if (item) {
    Object.assign(form, item);
  }

  modalRef.value.getBsModal().show();
}

const adicionarCampanha = () => {
  if (form.campanhas.find(campanha => campanha.id === form.campanha.id)) {
    return false
  }

  form.campanhas.push(form.campanha);
}

const removerCampanha = (item) => {
  const index = form.campanhas.findIndex(campanha => campanha.id === item.id);
  if (index !== -1) {
    form.campanhas.splice(index, 1);
  }
};

const save = () => {
  const url = form.id ? 'update' : 'store';

  form.post(route('contratos.contratada.servicos.monitora_fauna.resultado.' + url, { contrato: props.contrato.id, servico: props.servico.id }), {
    onSuccess: () => modalRef.value.getBsModal().hide()
  })
}

defineExpose({ abrirModal });
</script>

<template>
  <Modal ref="modalRef" title="Resultado" modal-dialog-class="modal-xl">
    <template #body>
      <form @submit.prevent="save()">
        <div class="row mb-4">
          <div class="col">
            <InputLabel value="ID Resultado" />
            <input type="number" name="id" id="id" class="form-control" :value="form.id" disabled>
            <InputError />
          </div>
          <div class="col">
            <InputLabel value="Data" />
            <input type="date" name="date" id="date" class="form-control" :value="form.created_at" disabled>
            <InputError />
          </div>
        </div>
        <div class="row mb-4">
          <div class="col">
            <InputLabel value="Nome Resultado" />
            <input type="text" name="nome" id="nome" class="form-control" v-model="form.nome">
            <InputError :massage="form.errors.nome" />
          </div>
          <div class="col">
            <InputLabel value="Nome Resultado" />
            <div class="row g-2">
              <div class="col">
                <select name="campanha" id="campanha" class="form-select" v-model="form.campanha">
                  <option v-for="campanha in campanhas" :key="campanha.id" :value="campanha">{{ campanha.id }}
                  </option>
                </select>
              </div>
              <div class="col-auto">
                <NavButton @click="adicionarCampanha()" type-button="success" title="Adicionar" />
              </div>
            </div>
            <InputError :massage="form.errors.campanhas" />
          </div>
        </div>
        <div class="row mb-4">
          <div class="col">
            <Table :columns="['Campanha', 'Ação']" :records="{ data: form.campanhas, links: [] }"
              table-class="table-hover">
              <template #body="{ item }">
                <tr>
                  <td>{{ item.id }}</td>
                  <td class="text-center">
                    <NavButton @click="removerCampanha(item)" type-button="danger" :icon="IconTrash" />
                  </td>
                </tr>
              </template>
            </Table>
          </div>
        </div>
        <div class="row">
          <div class="col text-end">
            <NavButton @click="save()" type-button="success" title="Salvar" />
          </div>
        </div>
      </form>
    </template>
  </Modal>
</template>
