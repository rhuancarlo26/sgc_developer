<script setup>
import InputError from "@/Components/InputError.vue";
import Modal from "@/Components/Modal.vue";
import { useForm } from "@inertiajs/vue3";
import { ref } from "vue";

const props = defineProps({
  licenca: { Object }
});

const modalNovaCondicionante = ref();

const abrirModal = () => {
  modalNovaCondicionante.value.getBsModal().show();
}

const editarCondicionante = (item) => {
  Object.assign(form, item);

  modalNovaCondicionante.value.getBsModal().show();
}

const form = useForm({
  id: null,
  licencas_id: props.licenca.id,
  numero: null,
  prazo: null,
  descricao: null
});

const salvarCondicionante = () => {
  form.transform((data) => Object.assign({}, data))

  if (form.id) {
    form.patch(route('licenca.condicionante.update', form.id), {
      onSuccess: () => {
        form.reset();

        modalNovaCondicionante.value.getBsModal().hide();
      }
    });

    return;
  }

  form.post(route('licenca.condicionante.store'), {
    onSuccess: () => {
      form.reset();

      modalNovaCondicionante.value.getBsModal().hide();
    }
  });
}

defineExpose({ abrirModal, editarCondicionante });
</script>

<template>
  <Modal ref="modalNovaCondicionante" title="Condicionante" modal-dialog-class="modal-xl">
    <template #body>
      <div class="row mb-4">
        <div class="col">
          <label class="form-label">N° da condicionante</label>
          <input type="text" class="form-control" name="numero" v-model="form.numero">
          <InputError :message="form.errors.numero" />
        </div>
        <div class="col">
          <label class="form-label">Prazo</label>
          <input type="number" class="form-control" name="prazo" v-model="form.prazo">
          <InputError :message="form.errors.prazo" />
        </div>
      </div>
      <div class="row mb-4">
        <div class="col">
          <label class="form-label">Descrição</label>
          <textarea class="form-control" name="descricao" rows="5" v-model="form.descricao"></textarea>
          <InputError :message="form.errors.descricao" />
        </div>
      </div>
    </template>
    <template #footer>
      <button @click="salvarCondicionante()" class="btn btn-success">Salvar</button>
    </template>
  </Modal>
</template>
