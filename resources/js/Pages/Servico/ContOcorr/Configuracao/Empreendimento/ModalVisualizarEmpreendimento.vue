<script setup>
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import Modal from "@/Components/Modal.vue";
import Map from "@/Components/Map.vue";
import NavButton from "@/Components/NavButton.vue";
import Table from "@/Components/Table.vue";
import { useForm } from "@inertiajs/vue3";
import { IconDeviceFloppy } from "@tabler/icons-vue";
import { computed } from "vue";
import { ref } from "vue";

const modalvisualizarEmpreendimento = ref({});
const empreendimento = ref({});

const abrirModal = (item) => {
  if (item) {
    empreendimento.value = item
  }

  modalvisualizarEmpreendimento.value.getBsModal().show();
};

defineExpose({ abrirModal });
</script>

<template>
  <Modal ref="modalvisualizarEmpreendimento" title="Visualizar empreendimento" modal-dialog-class="modal-xl">
    <template #body>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table card-table table-bordered">
            <tbody>
              <tr>
                <th>Nome</th>
                <td>{{ empreendimento.licenca?.tipo?.nome }}</td>
              </tr>
              <tr>
                <th>Empreendimento</th>
                <td>{{ empreendimento.licenca?.empreendimento }}</td>
              </tr>
              <tr>
                <th>UF</th>
                <td>
                  <template v-if="empreendimento.licenca?.iniciais">
                    <span v-for="uf in empreendimento.licenca?.iniciais?.split(',')" :key="uf"
                      class="badge bg-warning text-white m-1">
                      {{ uf }}
                    </span>
                  </template>
                </td>
              </tr>
              <tr>
                <th>BR</th>
                <td>
                  <template v-if="empreendimento.licenca?.brs">
                    <span v-for="br in empreendimento.licenca?.brs.split(',')" :key="br"
                      class="badge bg-warning text-white m-1">
                      {{ br }}
                    </span>
                  </template>
                </td>
              </tr>
              <tr>
                <th>KM inicial</th>
                <td>
                  <template v-if="empreendimento.licenca?.segmentos.length">
                    {{ Math.min(...empreendimento.licenca?.segmentos.map(segmento => segmento.km_inicial)) }}
                  </template>
                </td>
              </tr>
              <tr>
                <th>KM final</th>
                <td>
                  <template v-if="empreendimento.licenca?.segmentos.length">
                    {{ Math.max(...empreendimento.licenca?.segmentos.map(segmento => segmento.km_final)) }}
                  </template>
                </td>
              </tr>
              <tr>
                <th>Extensão</th>
                <td>{{ empreendimento.licenca?.extensao }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </template>
    <template #footer>
    </template>
  </Modal>
</template>
