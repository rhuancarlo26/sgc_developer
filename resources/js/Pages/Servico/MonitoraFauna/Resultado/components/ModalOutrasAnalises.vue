<script setup>
import Modal from "@/Components/Modal.vue";
import { useForm } from "@inertiajs/vue3";
import { ref } from "vue";

const props = defineProps({
  contrato: { type: Object },
  servico: { type: Object }
});

const modal = ref();

const form = useForm({});

const abrirModal = () => {
  modal.value.getBsModal().show();
}

defineExpose({ abrirModal });
</script>

<template>
  <Modal ref="modal" title="Modal" modal-dialog-class="modal-xl">
    <template #body>
      <div class="card-body">

      </div>
    </template>
  </Modal>
</template>
