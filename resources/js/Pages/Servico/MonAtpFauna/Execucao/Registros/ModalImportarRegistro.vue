<script setup>
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import Modal from "@/Components/Modal.vue";
import NavButton from "@/Components/NavButton.vue";
import { useForm } from "@inertiajs/vue3";
import axios from "axios";
import { ref } from "vue";

const modal = ref();
const props = defineProps({
  servico: { type: Object },
  campanhas: { type: Array }
});

const form = useForm({
  servico_id: null,
  campanha_id: null,
  arquivo: null
});

const abrirModal = () => {

  modal.value.getBsModal().show();
}

const importar = () => {
  form.servico_id = props.servico.id;

  form.post(route('contratos.contratada.servicos.mon_atp_fauna.execucao.registros.store_importar'));
}

defineExpose({ abrirModal });
</script>

<template>
  <Modal ref="modal" title="Importar Tabela de Registros" modal-dialog-class="modal-xl">
    <template #body>
      <div class="card-body">
        <div class="row">
          <div class="col">
            <div class="d-flex justify-content-end">
              <a class="btn btn-info" target="_blank"
                :href="route('contratos.contratada.servicos.mon_atp_fauna.execucao.registros.modelo_importar')">Modelo</a>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col">
            <InputLabel value="Selecionar Campanha" for="campanha_id" />
            <select name="campanha_id" id="campanha_id" class="form-select" v-model="form.campanha_id">
              <option v-for="campanha in campanhas" :key="campanha.id" :value="campanha.id">{{ campanha.id }}</option>
            </select>
            <InputError :message="form.errors.campanha_id" />
          </div>
          <div class="col">
            <InputLabel value="Selecionar Arquivo" for="arquivo" />

            <div class="row g-2">
              <div class="col">
                <input type="file" name="arquivo" id="arquivo" class="form-control"
                  @input="form.arquivo = $event.target.files[0]">
              </div>
              <div class="col-auto">
                <NavButton @click="importar()" title="Salvar" type-button="success" />
              </div>
            </div>
            <InputError :message="form.errors.arquivo" />
          </div>
        </div>
      </div>
    </template>
    <template #footer>
    </template>
  </Modal>
</template>
