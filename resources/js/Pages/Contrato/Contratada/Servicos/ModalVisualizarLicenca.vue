<script setup>
import Modal from "@/Components/Modal.vue";
import { ref } from "vue";

const detalhe = ref({});
const modalDetalhes = ref(null);

const abrirModal = (item) => {
  detalhe.value = item;

  modalDetalhes.value.getBsModal().show();
}

defineExpose({ abrirModal });
</script>

<template>
  <Modal ref="modalDetalhes" :title="detalhe.tipo?.nome" modal-dialog-class="modal-xl">
    <template #body>
      <div class="mb-4">
        <div class="card-header">
          <h3 class="my-0">Licenças</h3>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover non-hover">
              <thead>
                <tr>
                  <th>Licença</th>
                  <th>Condicionante</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="condicionante in detalhe.condicionantes" :key="condicionante.id">
                  <td>{{ condicionante.licenca?.numero_licenca }}</td>
                  <td>{{ `${condicionante.numero_condicionante}. ${condicionante.descricao}` }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </template>
  </Modal>
</template>