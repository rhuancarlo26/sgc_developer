<script setup>
import InputError from "@/Components/InputError.vue";
import Modal from "@/Components/Modal.vue";
import { router, useForm } from "@inertiajs/vue3";
import { IconDots } from "@tabler/icons-vue";
import { ref } from "vue";

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
              <td>{{ requerimento.nome }}</td>
              <td>
                <span class="dropdown">
                  <button class="btn dropdown-toggle align-text-top" data-bs-boundary="viewport" data-bs-toggle="dropdown"
                    aria-expanded="false">
                    <IconDots />
                  </button>
                  <div class="dropdown-menu dropdown-menu-end" style="">
                    <a class="dropdown-item" target="_blank"
                      :href="route('licenca.requerimento.visualizar', requerimento.id)">
                      Visualizar
                    </a>
                    <a @click="excluirRequerimento(requerimento.id, index)" class="dropdown-item"
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
    </template>
    <template #footer>
    </template>
  </Modal>
</template>
