<script setup>
import InputLabel from "@/Components/InputLabel.vue";
import Modal from "@/Components/Modal.vue";
import { Link, router, useForm, usePage } from "@inertiajs/vue3";
import { computed, ref } from "vue";
import { dateTimeFormat } from "@/Utils/DateTimeUtils.js";
import { IconTrash } from "@tabler/icons-vue";
import Table from "@/Components/Table.vue";
import LinkConfirmation from "@/Components/LinkConfirmation.vue";
import NavButton from "@/Components/NavButton.vue";
import InputError from "@/Components/InputError.vue";

const props = defineProps({
  url: { type: String, default: '' }
})

const modalRef = ref({});

const abrirModal = () => {
  modalRef.value.getBsModal().show();
}

defineExpose({ abrirModal });
</script>

<template>
  <Modal ref="modalRef" title="Video" modal-dialog-class="modal-xl">
    <template #body>
      <div class="d-flex justify-content-center">
        <video controls width="600" :src="url" type="video/mp4"></video>
      </div>
    </template>
  </Modal>
</template>
