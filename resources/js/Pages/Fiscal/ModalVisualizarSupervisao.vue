<script setup>
import Modal from "@/Components/Modal.vue";
import { ref } from "vue";
import Table from "@/Components/Table.vue";
import { IconMap } from "@tabler/icons-vue";

const modalVisualizacao = ref(null);
const supervisao = ref({});

const abrirModal = (item) => {
  supervisao.value = item;

  modalVisualizacao.value.getBsModal().show();
}

defineExpose({ abrirModal });
</script>

<template>
  <Modal ref="modalVisualizacao" title="Programa de monitoramento de qualidade da água" modal-dialog-class="modal-xl">
    <template #body>
      <div class="accordion" id="accordion-example">
        <div class="accordion-item">
          <h2 class="accordion-header" id="heading-1">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
              data-bs-target="#empreendimentos" aria-expanded="false">
              Empreendimento
            </button>
          </h2>
          <div id="empreendimentos" class="accordion-collapse collapse" data-bs-parent="#accordion-example">
            <div class="accordion-body pt-0">
              <Table
                :columns="['UF', 'BR', 'Empreendimento', 'Km inicial', 'Km final', 'Extensão', 'N° licença', 'ShapeFile']"
                :records="{ data: supervisao.licencas_condicionantes, links: [] }" table-class="table-hover">
                <template #body="{ item }">
                  <tr>
                    <td class="w-8">
                      <template v-if="item.licenca?.iniciais">
                        <span v-for="uf in item.licenca?.iniciais.split(',')" :key="uf"
                          class="badge bg-warning text-white m-1">
                          {{ uf }}
                        </span>
                      </template>
                    </td>
                    <td class="w-8">
                      <template v-if="item.licenca?.finais">
                        <span v-for="uf in item.licenca?.finais.split(',')" :key="uf"
                          class="badge bg-warning text-white m-1">
                          {{ uf }}
                        </span>
                      </template>
                    </td>
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
                    <td>{{ item.licenca?.numero_licenca }}</td>
                    <td>
                      <button v-if="item.licenca?.geo_json" @click="abrirShapefile(item)"
                        class="btn btn-icon btn-primary">
                        <IconMap />
                      </button>
                      <span v-else>-</span>
                    </td>
                  </tr>
                </template>
              </Table>
            </div>
          </div>
        </div>
        <div class="accordion-item">
          <h2 class="accordion-header" id="heading-2">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
              data-bs-target="#lote-obra" aria-expanded="false">
              Lote de obra
            </button>
          </h2>
          <div id="lote-obra" class="accordion-collapse collapse" data-bs-parent="#accordion-example">
            <div class="accordion-body pt-0">
              <Table
                :columns="['ID', 'Nome', 'BR', 'UF', 'Km inicial', 'Km final', 'Contrutora', 'N° contrato', 'Situação', 'Supervisora']"
                :records="{ data: supervisao.supervisao_lotes, links: [] }" table-class="table-hover">
                <template #body="{ item }">
                  <tr>
                    <td>{{ item.nome_id }}</td>
                    <td>{{ item.nome }}</td>
                    <td>{{ item.rodovia?.rodovia }}</td>
                    <td>{{ item.uf?.uf }}</td>
                    <td>{{ item.km_inicial }}</td>
                    <td>{{ item.km_final }}</td>
                    <td>{{ item.empresa }}</td>
                    <td>{{ item.num_contrato }}</td>
                    <td>{{ item.situacao_contrato }}</td>
                    <td>{{ item.supervisora_obras }}</td>
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
