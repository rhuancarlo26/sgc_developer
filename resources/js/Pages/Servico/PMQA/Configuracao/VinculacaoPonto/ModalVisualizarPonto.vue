<script setup>
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import Modal from "@/Components/Modal.vue";
import NavButton from "@/Components/NavButton.vue";
import Table from "@/Components/Table.vue";
import { useForm } from "@inertiajs/vue3";
import { IconDeviceFloppy } from "@tabler/icons-vue";
import { computed } from "vue";
import { ref } from "vue";

const modalVisualizarPonto = ref({});
const lista = ref({});

const abrirModal = (item) => {
  lista.value = item;

  modalVisualizarPonto.value.getBsModal().show();
}

defineExpose({ abrirModal });
</script>

<template>
  <Modal ref="modalVisualizarPonto" :title="'Pontos vinculados - ' + lista.nome" modal-dialog-class="modal-xl">
    <template #body>
      <div class="card-body">

        <div class="table-responsive">
          <table class="table card-table table-bordered">
            <thead>
              <tr>
                <th>Ponto</th>
                <th>Classe</th>
                <th>Tipo de ambiente</th>
                <th>UF</th>
                <th>Município</th>
                <th>Bacia hifrográfica</th>
                <th>Km rodovia</th>
                <th>Estaca</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="ponto in lista.pontos" :key="ponto.id">
                <td>{{ ponto.id }}</td>
                <td>{{ ponto.classe }}</td>
                <td>{{ ponto.tipoambiente }}</td>
                <td>{{ ponto.uf }}</td>
                <td>{{ ponto.municipio }}</td>
                <td>{{ ponto.baciahidrografica }}</td>
                <td>{{ ponto.km_rodovia }}</td>
                <td>{{ ponto.estaca }}</td>
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
