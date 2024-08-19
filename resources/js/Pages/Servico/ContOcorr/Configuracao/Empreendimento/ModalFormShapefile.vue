<script setup>
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import Modal from "@/Components/Modal.vue";
import Map from "@/Components/Map.vue";
import NavButton from "@/Components/NavButton.vue";
import Table from "@/Components/Table.vue";
import { useForm } from "@inertiajs/vue3";
import { IconDeviceFloppy } from "@tabler/icons-vue";
import { computed } from "vue";
import { ref } from "vue";

const props = defineProps({
  contrato: { type: Object },
  servico: { type: Object }
});

const modalFormShapefile = ref({});
const empreendimento = ref({});

const form = useForm({
  licenca_id: null,
  shapefile: null
});

const abrirModal = (item) => {
  empreendimento.value = {};

  if (item) {
    empreendimento.value = item
  }

  modalFormShapefile.value.getBsModal().show();
};

const salvarShapefile = () => {
  form.licenca_id = empreendimento.value.licenca?.id;

  form.post(route('contratos.contratada.servicos.cont_ocorrencia.configuracao.empreendimento.store_licenca_shapefile', { contrato: props.contrato.id, servico: props.servico.id }), {
    onSuccess: () => modalFormShapefile.value.getBsModal().hide()
  });
}

defineExpose({ abrirModal });
</script>

<template>
  <Modal ref="modalFormShapefile" title="Visualizar empreendimento" modal-dialog-class="modal-xl">
    <template #body>
      <div class="card-body">
        <InputLabel value="Shapefile" for="shapefile" />
        <div class="row g-2">
          <div class="col">
            <input @input="form.shapefile = $event.target.files[0]" name="shapefile" id="shapefile" type="file"
              class="form-control">
          </div>
          <div class="col-auto">
            <a @click="salvarShapefile()" href="#" class="btn btn-success" aria-label="Button"
              :disabled="form.processing">
              Enviar
            </a>
          </div>
        </div>
        <!-- </form> -->
        <InputError :message="form.errors.shapefile" />
      </div>
    </template>
    <template #footer>
    </template>
  </Modal>
</template>
