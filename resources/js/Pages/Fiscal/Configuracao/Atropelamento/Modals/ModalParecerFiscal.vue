<script setup>
import Modal from "@/Components/Modal.vue";
import InputError from "@/Components/InputError.vue";
import { useForm } from "@inertiajs/vue3";
import NavButton from "@/Components/NavButton.vue";
import { ref } from "vue";

const form = useForm({
  id: null,
  parecer: null,
  fk_status: null,
  id_contrato: null
});

const modalParecerRef = ref({});
const servico = ref({});

const abrirModal = (item) => {
  servico.value = item;
  Object.assign(form, item.parecer_atropelamento);

  modalParecerRef.value.getBsModal().show();
}

const emiteParecer = (status) => {
  form.fk_status = status;
  form.id_contrato = servico.value.id_contrato;

  form.post(route('fiscal.emite_parecer_config_atropelamento_fauna'), {
    onSuccess: () => modalParecerRef.value.getBsModal().hide()
  });
}

defineExpose({ abrirModal });
</script>
<template>
  <Modal ref="modalParecerRef" title="Parecer" modal-dialog-class="modal-xl">
    <template #body>
      <div class="">
        <div class="card">
          <div class="card-body">
            <div class="mb-2">
              <IconCode class="icon me-2 text-secondary" />
              Programa: <strong>{{ servico?.tema?.nome_tema }}</strong>
            </div>
            <div class="mb-2">
              <IconBriefcase class="icon me-2 text-secondary" />
              Tipo: <strong>{{ servico?.tipo?.nome }}</strong>
            </div>
            <div class="mb-2">
              <IconHome class="icon me-2 text-secondary" />
              Status: <strong>
                <span v-if="servico.parecer_atropelamento?.fk_status === 1" class="badge bg-yellow-lt">
                  Em análise
                </span>
                <span v-else-if="servico.parecer_atropelamento?.fk_status === 3" class="badge bg-blue-lt">
                  Aprovado
                </span>
                <span v-else-if="servico.parecer_atropelamento?.fk_status === 2" class="badge bg-red-lt">
                  Pendente
                </span>
                <span v-else class="badge bg-red-lt">
                  Em confecção
                </span>
              </strong>
            </div>
            <div class="mb-2" v-if="servico.parecer_atropelamento?.fk_status === 1">
              <textarea name="parecer" id="parecer" class="form-control" v-model="form.parecer" rows="5"></textarea>
              <InputError :message="form.errors.parecer" />
            </div>
            <div class="mb-2" v-if="servico.parecer_atropelamento?.fk_status !== 1">
              <IconMessage class="icon me-2 text-secondary" />
              Parecer: <strong>{{ servico.parecer_atropelamento?.parecer }}</strong>
            </div>
            <div class="mb-2" v-if="servico.parecer_atropelamento?.fk_status !== 1">
              <IconCalendar class="icon me-2 text-secondary" />
              Data do parece: <strong>{{ servico.parecer_atropelamento?.updated_at }}</strong>
            </div>
          </div>
        </div>
      </div>
    </template>
    <template #footer>
      <NavButton @click="emiteParecer(3)" v-if="servico.parecer_atropelamento?.fk_status === 1" type-button="success"
        :icon="IconCheck" title="Aprovar" />
      <NavButton @click="emiteParecer(2)" v-if="servico.parecer_atropelamento?.fk_status === 1" type-button="danger"
        :icon="IconCheck" title="Reprovar" />
    </template>
  </Modal>
</template>