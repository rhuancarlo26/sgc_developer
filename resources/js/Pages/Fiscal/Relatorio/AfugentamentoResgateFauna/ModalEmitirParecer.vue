<script setup>
import Modal from "@/Components/Modal.vue";
import { ref } from "vue";
import InputError from "@/Components/InputError.vue";
import { useForm } from "@inertiajs/vue3";
import NavButton from "@/Components/NavButton.vue";
import { dateTimeFormat } from "@/Utils/DateTimeUtils";

const props = defineProps({
  contrato: { type: Object }
})

const afugentamento = ref({});
const modalParecer = ref({});

const form = useForm({
  id: null,
  parecer_fiscal: null,
  fk_status: null,
});

const abrirModal = (item) => {
  form.parecer_fiscal = null;

  afugentamento.value = item;
  Object.assign(form, item);

  modalParecer.value.getBsModal().show();
}

const emiteParecer = (status) => {
  form.id = afugentamento.value.id;
  form.fk_status = status;

  form.post(route('fiscal.relatorio.afugentamento.store', { contrato: props.contrato.id }), {
    onSuccess: () => modalParecer.value.getBsModal().hide()
  });
}

defineExpose({ abrirModal });
</script>
<template>
  <Modal ref="modalParecer" title="Parecer" modal-dialog-class="modal-xl">
    <template #body>
      <div class="">
        <div class="card">
          <div class="card-body">
            <div class="mb-2">
              <IconCode class="icon me-2 text-secondary" />
              Programa: <strong>{{ afugentamento.servico?.tema?.nome_tema }}</strong>
            </div>
            <div class="mb-2">
              <IconBriefcase class="icon me-2 text-secondary" />
              Tipo: <strong>{{ afugentamento.servico?.tipo?.nome }}</strong>
            </div>
            <div class="mb-2">
              <IconHome class="icon me-2 text-secondary" />
              Status: <strong>
                <span v-if="afugentamento.fk_status === 1" class="badge bg-yellow-lt">
                  Em análise
                </span>
                <span v-else-if="afugentamento.fk_status === 3" class="badge bg-blue-lt">
                  Aprovado
                </span>
                <span v-else-if="afugentamento.fk_status === 2" class="badge bg-red-lt">
                  Pendente
                </span>
                <span v-else class="badge bg-red-lt">
                  Em confecção
                </span>
              </strong>
            </div>
            <div class="mb-2" v-if="afugentamento.fk_status === 1">
              <textarea name="parecer" id="parecer" class="form-control" v-model="form.parecer_fiscal"
                rows="5"></textarea>
              <InputError :message="form.errors.parecer_fiscal" />
            </div>
            <div class="mb-2" v-if="afugentamento.fk_status !== 1">
              <IconMessage class="icon me-2 text-secondary" />
              Parecer: <strong>{{ afugentamento.parecer_fiscal }}</strong>
            </div>
            <div class="mb-2" v-if="afugentamento.fk_status !== 1">
              <IconCalendar class="icon me-2 text-secondary" />
              Data do parece: <strong>{{ dateTimeFormat(afugentamento.updated_at) }}</strong>
            </div>
          </div>
        </div>
      </div>
    </template>
    <template #footer>
      <NavButton @click="emiteParecer(3)" v-if="afugentamento.fk_status === 1" type-button="success" :icon="IconCheck"
        title="Aprovar" />
      <NavButton @click="emiteParecer(2)" v-if="afugentamento.fk_status === 1" type-button="danger" :icon="IconCheck"
        title="Reprovar" />
    </template>
  </Modal>
</template>
