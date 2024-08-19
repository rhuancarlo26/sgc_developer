<script setup>
import Modal from "@/Components/Modal.vue";
import { router, useForm } from "@inertiajs/vue3";
import { IconSearch } from "@tabler/icons-vue";
import axios from "axios";
import { ref } from "vue";
import { useToast } from "vue-toastification";

const equipamento = ref({
  nome: null,
  descricao: null,
  atividade: null,
  observacao: null
});
const modalDetalhes = ref(null);

const abrirModal = (item) => {
  equipamento.value = item;

  modalDetalhes.value.getBsModal().show();
}

defineExpose({ abrirModal });
</script>

<template>
  <Modal ref="modalDetalhes" :title="equipamento.nome" modal-dialog-class="modal-xl">
    <template #body>
      <div class="accordion" id="accordion-example">
        <div class="accordion-item">
          <h2 class="accordion-header" id="heading-1">
            <button class="accordion-button " type="button" data-bs-toggle="collapse" data-bs-target="#descricao"
              aria-expanded="true">
              Descrição equipamento
            </button>
          </h2>
          <div id="descricao" class="accordion-collapse collapse show" data-bs-parent="#accordion-example">
            <div class="accordion-body pt-0">
              {{ equipamento.modelo }}
            </div>
          </div>
        </div>
        <div class="accordion-item">
          <h2 class="accordion-header" id="heading-2">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
              data-bs-target="#atividade" aria-expanded="false">
              Atividades relacionadas
            </button>
          </h2>
          <div id="atividade" class="accordion-collapse collapse" data-bs-parent="#accordion-example">
            <div class="accordion-body pt-0">
              {{ equipamento.espec_tecnica }}
            </div>
          </div>
        </div>
        <div class="accordion-item">
          <h2 class="accordion-header" id="heading-3">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
              data-bs-target="#observacao" aria-expanded="false">
              Observação
            </button>
          </h2>
          <div id="observacao" class="accordion-collapse collapse" data-bs-parent="#accordion-example">
            <div class="accordion-body pt-0">
              {{ equipamento.obs }}
            </div>
          </div>
        </div>
        <div class="accordion-item">
          <h2 class="accordion-header" id="heading-3">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                  data-bs-target="#fotos_equipamento" aria-expanded="false">
                Foto(s) do Equipamento
            </button>
          </h2>
          <div id="fotos_equipamento" class="accordion-collapse collapse" data-bs-parent="#accordion-example">
            <div class="accordion-body pt-0">
                <div v-for="documento in equipamento.documentos" :key="documento.id" class="col-xl-6 py-2">
                    <img :src="route('home') + documento.caminho" :title="documento.nome" :alt="documento.nome" class="w-100" />
                </div>
            </div>
          </div>
        </div>
      </div>
    </template>
  </Modal>
</template>
