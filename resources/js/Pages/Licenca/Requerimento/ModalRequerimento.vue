<script setup>
import Modal from "@/Components/Modal.vue";
import { router } from "@inertiajs/vue3";
import { IconEdit, IconEye, IconTrash } from "@tabler/icons-vue";
import { ref } from "vue";
import NavButton from "@/Components/NavButton.vue";
import NavLink from "@/Components/NavLink.vue";

let licenca = ref({});
let arquivos = ref({});

const modalRequerimento = ref();

const abrirModal = (item) => {
  licenca.value = item;

  modalRequerimento.value.getBsModal().show();
}

const salvarRequerimento = () => {
  router.post(route('licenca.requerimento.store'), { arquivos: arquivos.value, licenca_id: licenca.value.id }, {
    preserveScroll: true,
    onSuccess: (r) => {
      licenca.value.requerimentos = r.props.flash.message.requerimentos;
    }
  })
}

const excluirRequerimento = (requerimento_id, index) => {
  router.delete(route('licenca.requerimento.destroy', requerimento_id), {
    preserveScroll: true,
    onSuccess: () => {
      licenca.value.requerimentos.splice(index, 1);
    }
  })
}

defineExpose({ abrirModal });
</script>

<template>
  <Modal ref="modalRequerimento" title="Requerimento" modal-dialog-class="modal-xl">
    <template #body>
      <div class="mb-4">
        <label class="form-label">Adicionar requerimento</label>
        <div class="row g-2">
          <div class="col">
            <input @input="arquivos = $event.target.files" type="file" accept=".pdf" class="form-control" multiple>
          </div>
          <div class="col-auto">
            <a @click="salvarRequerimento()" href="javascript:void(0)" class="btn btn-success" aria-label="Button">
              Salvar
            </a>
          </div>
        </div>
      </div>
      <div v-if="licenca?.requerimentos?.length" class="table-responsive">
        <table class="table table-hover non-hover">
          <thead>
            <tr>
              <th>Nome</th>
              <th>Ação</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="requerimento, index in licenca.requerimentos" :key="requerimento.id">
              <td>{{ requerimento.nome_arquivo }}</td>
              <td>
                <a class="btn btn-lg btn-info m-1" title="Visualizar" target="_blank"
                  :href="route('licenca.requerimento.visualizar', requerimento.id)">
                  <IconEye />
                </a>
                <a @click="excluirRequerimento(requerimento.id, index)" class="btn btn-lg btn-danger" title="Excluir"
                  href="javascript:void(0)">
                  <IconTrash />
                </a>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </template>
    <template #footer>
    </template>
  </Modal>
</template>
