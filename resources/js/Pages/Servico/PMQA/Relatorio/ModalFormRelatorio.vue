<script setup>
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import Modal from "@/Components/Modal.vue";
import NavButton from "@/Components/NavButton.vue";
import Table from "@/Components/Table.vue";
import { dateTimeFormat } from "@/Utils/DateTimeUtils";
import { useForm } from "@inertiajs/vue3";
import { IconDeviceFloppy } from "@tabler/icons-vue";
import { computed } from "vue";
import { ref } from "vue";

const props = defineProps({
  contrato: { type: Object },
  servico: { type: Object },
  resultados: { type: Array }
});

const modalCampanha = ref();

const form = useForm({
  id: null,
  servico_id: props.servico.id,
  nome: null,
  resultado_id: null,
  observacao: null
});

const reset = () => {
  form.id = null;
  form.servico_id = props.servico.id;
  form.nome = null;
  form.resultado_id = null;
  form.observacao = null;
}

const salvarRelatorio = () => {
  const url = form.id ? 'update' : 'store';

  form.post(route('contratos.contratada.servicos.pmqa.relatorio.' + url, { contrato: props.contrato.id, servico: props.servico.id }), {
    onSuccess: () => modalCampanha.value.getBsModal().hide()
  })
}

const abrirModal = (item) => {
  form.reset();

  if (item) {
    Object.assign(form, item);
  }

  modalCampanha.value.getBsModal().show();
}

defineExpose({ abrirModal });
</script>

<template>
  <Modal ref="modalCampanha" title="Nova campanha" modal-dialog-class="modal-xl">
    <template #body>
      <div class="card-body">
        <div class="row mb-4">
          <div class="col form-group">
            <InputLabel value="Nome do relatório" for="nome" />
            <input type="text" class="form-control" name="nome" id="nome" v-model="form.nome">
            <InputError :message="form.errors.nome" />
          </div>
        </div>
        <div class="row mb-4">
          <div class="col form-group">
            <InputLabel value="Selecionar resultado:" />
            <InputError :message="form.errors.resultado" />
            <div class="table-responsive">
              <table class="table card-table table-bordered">
                <thead>
                  <tr>
                    <th>Nome</th>
                    <th>Campanhas</th>
                    <th>Data</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="resultado in resultados" :key="resultado.id">
                    <td>
                      <label class="form-check">
                        <input class="form-check-input" type="radio" :name="'resultado-' + resultado.id"
                          :id="'resultado-' + resultado.id" :value="resultado.id" v-model="form.resultado_id">
                        <span class="form-check-label">{{ resultado.nome }}</span>
                      </label>
                    </td>
                    <td>
                      <span v-for="campanha in resultado.campanhas.map(campanha => campanha.nome)" :key="campanha"
                        class="badge bg-warning text-white m-1">
                        {{ campanha }}
                      </span>
                    </td>
                    <td>{{ dateTimeFormat(resultado.created_at) }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col form-group">
            <InputLabel value="Observações" for="observacao" />
            <textarea class="form-control" name="observacao" id="observacao" rows="6"
              v-model="form.observacao"></textarea>
            <InputError :message="form.errors.observacao" />
          </div>
        </div>
      </div>
    </template>
    <template #footer>
      <NavButton @click="salvarRelatorio()" type-button="success" :icon="IconDeviceFloppy"
        :title="form.id ? 'Editar' : 'Salvar'" />
    </template>
  </Modal>
</template>
