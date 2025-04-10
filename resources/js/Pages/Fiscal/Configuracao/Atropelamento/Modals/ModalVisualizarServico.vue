<script setup>
import Modal from "@/Components/Modal.vue";
import { ref } from "vue";
import Table from "@/Components/Table.vue";
import { dateTimeFormat } from "@/Utils/DateTimeUtils";
import NavButton from "@/Components/NavButton.vue";
import { IconMap, IconLineDashed } from "@tabler/icons-vue";

const modalVisualizacaoServicoRef = ref(null);
const servicoRef = ref({});

const abrirModal = (item) => {
  servicoRef.value = item;

  modalVisualizacaoServicoRef.value.getBsModal().show();
}

defineExpose({ abrirModal });
</script>

<template>
  <Modal ref="modalVisualizacaoServicoRef" title="Monitoramento de Passagem da Fauna" modal-dialog-class="modal-xl">
    <template #body>
      <div class="accordion" id="accordion-example">
        <div class="accordion-item">
          <h2 class="accordion-header" id="heading-1">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#abio"
              aria-expanded="false">
              ABIO
            </button>
          </h2>
          <div id="abio" class="accordion-collapse collapse" data-bs-parent="#accordion-example">
            <div class="accordion-body pt-0">
              <Table
                :columns="['Tipo', 'N° licença', 'Empreendimento', 'Emissor', 'Data de emissão', 'Vencimento', 'Responsável', 'Processo DNIT']"
                :records="{ data: servicoRef.atropelamento_abios, links: [] }" table-class="table-hover">
                <template #body="{ item }">
                  <tr>
                    <td>{{ item.licenca?.tipo_rel?.sigla }}</td>
                    <td>{{ item.licenca?.numero_licenca }}</td>
                    <td>{{ item.licenca?.empreendimento }}</td>
                    <td>{{ item.licenca?.emissor }}</td>
                    <td>{{ dateTimeFormat(item.licenca?.data_emissao) }}</td>
                    <td>{{ dateTimeFormat(item.licenca?.vencimento) }}</td>
                    <td>{{ item.licenca?.fiscal }}</td>
                    <td>{{ item.licenca?.processo_dnit }}</td>
                  </tr>
                </template>
              </Table>
            </div>
          </div>
        </div>
        <div class="accordion-item">
          <h2 class="accordion-header" id="heading-2">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
              data-bs-target="#malha_viaria" aria-expanded="false">
              Malha viária
            </button>
          </h2>
          <div id="malha_viaria" class="accordion-collapse collapse" data-bs-parent="#accordion-example">
            <div class="accordion-body pt-0">
              <Table
                :columns="['Tipo', 'N° licença', 'Empreendimento', 'Km inicial', 'Km final', 'Extensao', 'ShapeFile']"
                :records="{ data: servicoRef.licencas_condicionantes, links: [] }" table-class="table-hover">
                <template #body="{ item }">
                  <tr>
                    <td>{{ item.licenca?.tipo_rel?.sigla }}</td>
                    <td>{{ item.licenca?.numero_licenca }}</td>
                    <td>{{ item.licenca?.empreendimento }}</td>
                    <td>
                      <template v-if="item.licenca?.segmentos.length">
                        {{ Math.min(...item.licenca?.segmentos.map(segmento => segmento.km_inicio)) }}
                      </template>
                    </td>
                    <td>
                      <template v-if="item.licenca?.segmentos.length">
                        {{ Math.max(...item.licenca?.segmentos.map(segmento => segmento.km_fim)) }}
                      </template>
                    </td>
                    <td>{{ item.licenca?.extensao }}</td>
                    <td class="text-center">
                      <div v-if="item.licenca?.local_shape || item.licenca?.local_shape_licenca">
                        <NavButton type-button="info" class="btn-icon" :icon="IconMap" />
                      </div>
                      <IconLineDashed v-else color="red" :size="40" />
                    </td>
                  </tr>
                </template>
              </Table>
            </div>
          </div>
        </div>
      </div>
    </template>
  </Modal>
</template>
