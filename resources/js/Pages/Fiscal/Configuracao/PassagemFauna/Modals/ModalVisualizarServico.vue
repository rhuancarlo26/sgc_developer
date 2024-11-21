<script setup>
import Modal from "@/Components/Modal.vue";
import { ref } from "vue";
import Table from "@/Components/Table.vue";
import { dateTimeFormat } from "@/Utils/DateTimeUtils";

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
                :columns="['Tipo', 'N° licença', 'Empreendimento', 'Emissor', 'Data da emissão', 'Vencimento', 'Responsável', 'Processo DNIT']"
                :records="{ data: servicoRef.passagem_fauna_abios, links: [] }" table-class="table-hover">
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
              data-bs-target="#passagem_fauna" aria-expanded="false">
              Passagem fauna
            </button>
          </h2>
          <div id="passagem_fauna" class="accordion-collapse collapse" data-bs-parent="#accordion-example">
            <div class="accordion-body pt-0">
              <Table
                :columns="['ID', 'Rodovia', 'Km', 'UF', 'Tipo', 'Classificação', 'Dimensões', 'Nome', 'Observações', 'Data cadastro']"
                :records="{ data: servicoRef.passagem_fauna_passagens, links: [] }" table-class="table-hover">
                <template #body="{ item }">
                  <tr>
                    <td>{{ item.nome_id }}</td>
                    <td>{{ item.rodovia }}</td>
                    <td>{{ item.km }}</td>
                    <td>{{ item.uf }}</td>
                    <td>{{ item.tipo_de_estrutura }}</td>
                    <td>{{ item.classificacao }}</td>
                    <td>{{ item.dimensoes }}</td>
                    <td>{{ item.nome }}</td>
                    <td>{{ item.observacao }}</td>
                    <td>{{ dateTimeFormat(item.created_at) }}</td>
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
