<script setup>
import Modal from "@/Components/Modal.vue";
import { useForm } from "@inertiajs/vue3";
import { ref } from "vue";

const props = defineProps({
  contrato: { type: Object }
});

const modalParecerRNC = ref({});
const ocorrencia = ref({});

const form = useForm({
  id: null,
  parecer_fiscal: null,
  aprovado_rnc: null,
  envio_fiscal: null
});

const abrirModal = (item) => {
  ocorrencia.value = item;
  Object.assign(form, ocorrencia.value);

  modalParecerRNC.value.getBsModal().show();
}

const enviarParecer = (parecer) => {
  form.aprovado_rnc = parecer;

  form.post(route('fiscal.rnc.parecer_fiscal', { contrato: props.contrato.id }), {
    onSuccess: () => modalParecerRNC.value.getBsModal().hide()
  });
}

defineExpose({ abrirModal });
</script>

<template>
  <Modal ref="modalParecerRNC" title="Parecer Fiscal" modal-dialog-class="modal-xl">
    <template #body>
      <div class="form-row">
        <div class="col-12 mb-2">
          <label for="tipo">Contratada:</label>
          <strong>{{ contrato.contratada }}</strong>
        </div>
        <div class="col-12 mb-2">
          <label for="nome_servico">Número do Contrato:</label>
          <strong>{{ contrato.numero_contrato }}</strong>
        </div>
        <div class="col-12 mb-4">
          <label for="parecer">Parecer:</label>
          <textarea class="form-control" placeholder="Parecer..." rows="5" v-model="form.parecer_fiscal"></textarea>
        </div>
        <div class="d-flex justify-content-end">
          <button @click="enviarParecer('Em análise')" class="btn btn-info me-1" type="button">
            Salvar parecer
          </button>
          <button @click="enviarParecer('Aprovado')" class="btn btn-success me-1" type="button">
            Aprovar
          </button>
          <button @click="enviarParecer('Reprovado')" class=" btn btn-danger me-1" type="button">
            Rejeitar
          </button>
        </div>
      </div>
    </template>
  </Modal>


</template>