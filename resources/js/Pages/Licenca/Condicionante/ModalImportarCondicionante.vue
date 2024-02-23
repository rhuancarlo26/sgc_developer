<script setup>
import Modal from "@/Components/Modal.vue";
import { router, useForm } from "@inertiajs/vue3";
import { IconSearch } from "@tabler/icons-vue";
import axios from "axios";
import { ref } from "vue";
import { useToast } from "vue-toastification";

const toast = useToast();

const props = defineProps({
  licenca: { Object }
});

const modalImportarCondicionante = ref(null);
var condicionantes = ref([]);

const form = useForm({
  numero_licenca: null
});

const abrirModal = () => {
  form.reset();
  condicionantes.value = [];

  modalImportarCondicionante.value.getBsModal().show();
}

const buscarLicenca = () => {
  axios.post(route('licenca.condicionante.buscar_licenca'), form).then(r => {
    if (!r.data.condicionantes.length) {
      toast.error('Nenhuma condicionante encontrada!');
      return
    }

    condicionantes.value = r.data.condicionantes;
  });
}

const salvarImportacao = () => {
  router.post(route('licenca.condicionante.store_importacao'), { condicionantes: condicionantes.value, licenca: props.licenca }, {
    onSuccess: () => {
      modalImportarCondicionante.value.getBsModal().hide();
    }
  })
}

defineExpose({ abrirModal });
</script>

<template>
  <Modal ref="modalImportarCondicionante" title="Condicionante" modal-dialog-class="modal-xl">
    <template #body>
      <div class="mb-4">
        <label class="form-label">Número licença</label>
        <div class="row g-2">
          <div class="col">
            <input type="text" class="form-control" name="numero_licenca" id="numero_licenca"
              v-model="form.numero_licenca">
          </div>
          <div class="col-auto">
            <button @click="buscarLicenca()" type="button" class="btn btn-icon">
              <IconSearch />
            </button>
          </div>
        </div>
      </div>
      <div v-if="condicionantes.length" class="table-responsive mb-4">
        <table width="100%" class="table table-hover non-hover">
          <thead>
            <tr>
              <th>Número</th>
              <th>Descrição</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="condicionante in condicionantes" :key="condicionante.id">
              <td class="text-center">{{ condicionante.numero_condicionante }}</td>
              <td class="text-left">{{ condicionante.descricao }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </template>
    <template #footer v-if="condicionantes.length">
      <button @click="salvarImportacao()" :disabled="!condicionantes" class="btn btn-success">Salvar</button>
    </template>
  </Modal>
</template>
