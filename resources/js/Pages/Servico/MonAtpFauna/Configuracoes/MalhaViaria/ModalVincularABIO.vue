<script setup>
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import Modal from "@/Components/Modal.vue";
import NavButton from "@/Components/NavButton.vue";
import { router, useForm } from "@inertiajs/vue3";
import { ref } from "vue";
import axios from "axios";
import { dateTimeFormat } from "@/Utils/DateTimeUtils";
import { IconEye } from "@tabler/icons-vue";

const props = defineProps({
  contrato: { type: Object },
  servico: { type: Object },
  licencas: { type: Array }
})

const modalVincularABIO = ref({});
const numero_licenca = ref({});
const licenca = ref({});

const abrirModal = () => {
  numero_licenca.value = {};
  licenca.value = {};

  modalVincularABIO.value.getBsModal().show();
}

const getLicenca = () => {
  axios.get(route('contratos.contratada.servicos.mon_atp_fauna.configuracoes.malha_viaria.get_licenca', { contrato: props.contrato.id, servico: props.servico.id, licenca: numero_licenca.value.id })).then(r => {
    licenca.value = r.data;
  });
}

const vincularABIO = () => {
  router.post(route('contratos.contratada.servicos.mon_atp_fauna.configuracoes.malha_viaria.store', { contrato: props.contrato.id, servico: props.servico.id }), { licenca_id: licenca.value.id }, {
    onSuccess: () => modalVincularABIO.value.getBsModal().hide()
  });
}

defineExpose({ abrirModal });
</script>

<template>
  <Modal ref="modalVincularABIO" title="Vincular ABIO" modal-dialog-class="modal-xl">
    <template #body>
      <div class="row">
        <InputLabel value="N° da licença" />
        <div class="row">
          <div class="col">
            <v-select :options="licencas" label="numero_licenca" v-model="numero_licenca">
              <template #no-options="{}">
                Nenhum registro encontrado.
              </template>
            </v-select>
          </div>
          <div class="col-auto">
            <NavButton @click="getLicenca()" type-button="success" title="Buscar" />
          </div>
        </div>
      </div>
      <div v-if="licenca.id" class="mt-4">
        <div class="row">
          <div class="col">
            <div class="table-responsive">
              <table class="table card-table table-bordered">
                <tr>
                  <th>N° licença</th>
                  <td>{{ licenca.numero_licenca }}</td>
                </tr>
                <tr>
                  <th>Nome licença</th>
                  <td>-</td>
                </tr>
                <tr>
                  <th>Data de emissão</th>
                  <td>{{ dateTimeFormat(licenca.data_emissao) }}</td>
                </tr>
                <tr>
                  <th>Vencimento</th>
                  <td>{{ dateTimeFormat(licenca.vencimento) }}</td>
                </tr>
                <tr>
                  <th>SEI</th>
                  <td>{{ licenca.numero_sei }}</td>
                </tr>
                <tr>
                  <th>Processo DNIT</th>
                  <td>{{ licenca.processo_dnit }}</td>
                </tr>
                <tr>
                  <th>Emissor</th>
                  <td>{{ licenca.emissor }}</td>
                </tr>
                <tr>
                  <th>Empreendimento</th>
                  <td>{{ licenca.empreendimento }}</td>
                </tr>
              </table>
            </div>
          </div>
          <div class="col">
            <div class="table-responsive">
              <table class="table card-table table-bordered">
                <tr>
                  <th>BR</th>
                  <td>-</td>
                </tr>
                <tr>
                  <th>UF/KM inicial</th>
                  <td>-</td>
                </tr>
                <tr>
                  <th>UF/KM final</th>
                  <td>-</td>
                </tr>
                <tr>
                  <th>Extensão</th>
                  <td>{{ licenca.extensao }}</td>
                </tr>
                <tr>
                  <th>Inicio do sub-trecho (PNV)</th>
                  <td>{{ licenca.inicio_subtrecho }}</td>
                </tr>
                <tr>
                  <th>Fim do sub-trecho (PNV)</th>
                  <td>{{ licenca.fim_subtrecho }}</td>
                </tr>
                <tr>
                  <th>Visualizar PDF</th>
                  <td>
                    <IconEye />
                  </td>
                </tr>
              </table>
            </div>
          </div>
        </div>
        <div class="d-flex justify-content-end mt-4">
          <NavButton @click="vincularABIO()" type-button="success" title="Vincular ABIO" />
        </div>
      </div>
    </template>
    <template #footer>
    </template>
  </Modal>
</template>
