<script setup>
import Modal from "@/Components/Modal.vue";
import { ref } from "vue";

const modalVisualizarLote = ref();

let lote = ref({});

const abrirModal = (item) => {
  lote.value = {};

  if (item) {
    lote.value = item;
  }

  modalVisualizarLote.value.getBsModal().show();
}

defineExpose({ abrirModal });
</script>

<template>
  <Modal ref="modalVisualizarLote" :title="'Lote ' + lote.nome_id" modal-dialog-class="modal-xl">
    <template #body>
      <div class=" mb-4">
        <h3>Dados lote</h3>
        <div class="row">
          <div class="col">
            <div class="card-body">
              <div class="table-responsive">
                <table class="table card-table table-bordered table-hover">
                  <tbody>
                    <tr class="cursor-pointer">
                      <th>ID lote:</th>
                      <td>{{ lote.nome_id }}</td>
                    </tr>
                    <tr class="cursor-pointer">
                      <th>Nome lote:</th>
                      <td>{{ lote.nome }}</td>
                    </tr>
                    <tr class="cursor-pointer">
                      <th>BR:</th>
                      <td>{{ lote.rodovia?.rodovia }}</td>
                    </tr>
                    <tr class="cursor-pointer">
                      <th>UF:</th>
                      <td>{{ lote.uf?.uf }}</td>
                    </tr>
                    <tr class="cursor-pointer">
                      <th>km inicial:</th>
                      <td>{{ lote.km_inicial }}</td>
                    </tr>
                    <tr class="cursor-pointer">
                      <th>Fiscal contrato:</th>
                      <td>{{ lote.fiscal_contrato }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
          <div class="col">
            <div class="card-body">
              <div class="table-responsive">
                <table class="table card-table table-bordered table-hover">
                  <tbody>
                    <tr class="cursor-pointer">
                      <th>Km final:</th>
                      <td>{{ lote.km_final }}</td>
                    </tr>
                    <tr class="cursor-pointer">
                      <th>Estaca inicial:</th>
                      <td>{{ lote.estaca_inicial }}</td>
                    </tr>
                    <tr class="cursor-pointer">
                      <th>Estaca final:</th>
                      <td>{{ lote.estaca_final }}</td>
                    </tr>
                    <tr class="cursor-pointer">
                      <th>Empresa/Consórcio:</th>
                      <td>{{ lote.empresa }}</td>
                    </tr>
                    <tr class="cursor-pointer">
                      <th>N° do contrato:</th>
                      <td>{{ lote.num_contrato }}</td>
                    </tr>
                    <tr class="cursor-pointer">
                      <th>Situação do contrato:</th>
                      <td>{{ lote.situacao_contrato }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col">
            <div class="card-body">
              <div class="table-responsive">
                <table class="table card-table table-bordered table-hover">
                  <tbody>
                    <tr class="cursor-pointer">
                      <th>Objeto do contrato:</th>
                      <td>{{ lote.obj_contrato }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="mb-4">
        <h3>Dados supervisora da obra</h3>
        <div class="row">
          <div class="col">
            <div class="card-body">
              <div class="table-responsive mb-4">
                <table class="table card-table table-bordered table-hover">
                  <tbody>
                    <tr class="cursor-pointer">
                      <th>Supervisora de obras:</th>
                      <td>{{ lote.fiscal_contrato_supervisora }}</td>
                    </tr>
                    <tr class="cursor-pointer">
                      <th>N° do contrato:</th>
                      <td>{{ lote.num_contrato_supervisora }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
          <div class="col">
            <div class="card-body">
              <div class="table-responsive mb-4">
                <table class="table card-table table-bordered table-hover">
                  <tbody>
                    <tr class="cursor-pointer">
                      <th>Fiscal contrato:</th>
                      <td>{{ lote.fiscal_contrato }}</td>
                    </tr>
                    <tr class="cursor-pointer">
                      <th>Situação do contrato:</th>
                      <td>{{ lote.situacao_contrato_supervisora }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col">
            <div class="card-body">
              <div class="table-responsive">
                <table class="table card-table table-bordered table-hover">
                  <tbody>
                    <tr class="cursor-pointer">
                      <th>Observações:</th>
                      <td>{{ lote.obs }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </template>
    <template #footer>
    </template>
  </Modal>
</template>
