<script setup>
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import Modal from "@/Components/Modal.vue";
import NavButton from "@/Components/NavButton.vue";
import { useForm } from "@inertiajs/vue3";
import { IconDeviceFloppy } from "@tabler/icons-vue";
import { ref } from "vue";

const props = defineProps({
  contrato: { type: Object },
  servico: { type: Object },
  campanha: { type: Object }
});

const ponto = ref({});
const modal = ref({});

const form = useForm({
  id: null,
  campanha_ponto_id: null,
  data_coleta: null,
  sem_coleta: false,
  numero_amostra: null,
  preservacao_amostra: null,
  acondicionamento_amostra: null,
  transporte_amostra: null,
  justificativa: null
});

const abrirModal = (item) => {
  form.reset();
  ponto.value = item;

  if (item.coleta) {
    form.id = item.coleta.id;
    form.campanha_ponto_id = item.coleta.campanha_ponto_id;
    form.data_coleta = item.coleta.data_coleta;
    form.sem_coleta = item.coleta.sem_coleta;
    form.numero_amostra = item.coleta.numero_amostra;
    form.preservacao_amostra = item.coleta.preservacao_amostra;
    form.acondicionamento_amostra = item.coleta.acondicionamento_amostra;
    form.transporte_amostra = item.coleta.transporte_amostra;
    form.justificativa = item.coleta.justificativa;
  }

  modal.value.getBsModal().show();
}

const saveColetaPonto = () => {
  form.campanha_ponto_id = ponto.value.id;

  if (form.id) {
    form.patch(route('contratos.contratada.servicos.pmqa.execucao.coleta.update', { contrato: props.contrato.id, servico: props.servico.id, campanha: props.campanha.id }), {
      onSuccess: () => modal.value.getBsModal().hide()
    })
  } else {
    form.post(route('contratos.contratada.servicos.pmqa.execucao.coleta.store', { contrato: props.contrato.id, servico: props.servico.id, campanha: props.campanha.id }), {
      onSuccess: () => modal.value.getBsModal().hide()
    })
  }
}

defineExpose({ abrirModal });
</script>

<template>
  <Modal ref="modal" :title="`Visualizar ${ponto.ponto?.nomepontocoleta} - ${campanha.nome}`"
    modal-dialog-class="modal-xl">
    <template #body>
      <div class="card-body">
        <div class="row mb-4">
          <div class="col form-group">
            <InputLabel value="Data da coleta" for="data_coleta" />
            <input type="date" class="form-control" name="data_coleta" id="data_coleta" v-model="form.data_coleta">
            <InputError :message="form.errors.data_coleta" />
          </div>
          <div class="col d-flex align-self-end justify-content-center form-group ">
            <label class="form-check">
              <input class="form-check-input" type="checkbox" v-model="form.sem_coleta">
              <span class="form-check-label">Não foi possível realizar a coleta</span>
            </label>
            <InputError :message="form.errors.data_coleta" />
          </div>
        </div>
        <div v-if="form.sem_coleta === false">
          <div class="row mb-4">
            <div class="col form-group">
              <InputLabel value="Número da amostra" for="numero_amostra" />
              <input type="text" class="form-control" name="numero_amostra" id="numero_amostra"
                v-model="form.numero_amostra">
              <InputError :message="form.errors.numero_amostra" />
            </div>
            <div class="col form-group">
              <InputLabel value="Preservação da amostra" for="preservacao_amostra" />
              <input type="text" class="form-control" name="preservacao_amostra" id="preservacao_amostra"
                v-model="form.preservacao_amostra">
              <InputError :message="form.errors.preservacao_amostra" />
            </div>
          </div>
          <div class="row">
            <div class="col form-group">
              <InputLabel value="Acondicionamento da amostra" for="acondicionamento_amostra" />
              <input type="text" class="form-control" name="acondicionamento_amostra" id="acondicionamento_amostra"
                v-model="form.acondicionamento_amostra">
              <InputError :message="form.errors.acondicionamento_amostra" />
            </div>
            <div class="col form-group">
              <InputLabel value="Transporte da amostra" for="transporte_amostra" />
              <input type="text" class="form-control" name="transporte_amostra" id="transporte_amostra"
                v-model="form.transporte_amostra">
              <InputError :message="form.errors.transporte_amostra" />
            </div>
          </div>
        </div>
        <div v-else>
          <div class="row">
            <div class="col">
              <InputLabel value="Justificativa" for="transporte_amostra" />
              <textarea class="form-control" name="justificativa" id="justificativa" rows="5"
                v-model="form.justificativa"></textarea>
              <InputError :message="form.errors.justificativa" />
            </div>
          </div>
        </div>
      </div>
    </template>
    <template #footer>
      <NavButton @click="saveColetaPonto()" type-button="success" :icon="IconDeviceFloppy"
        :title="form.id ? 'Alterar' : 'Salvar'" />
    </template>
  </Modal>
</template>
