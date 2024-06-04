<script setup>
import Modal from "@/Components/Modal.vue";
import { dateTimeFormat } from "@/Utils/DateTimeUtils";
import { router, useForm } from "@inertiajs/vue3";
import { IconSearch } from "@tabler/icons-vue";
import axios from "axios";
import { ref } from "vue";
import { useToast } from "vue-toastification";

const rh = ref({
  nome: null,
  telefone: null,
  cpf: null,
  email: null,
  formacao: null,
  funcao: null,
  ctf: null,
  ctf_validade: null,
  conselho_classe: null,
  numero_registro: null,
  observacao: null,
});

const modalDetalhes = ref(null);

const abrirModal = (item) => {
  rh.value = item;

  modalDetalhes.value.getBsModal().show();
}

defineExpose({ abrirModal });
</script>

<template>
  <Modal ref="modalDetalhes" :title="'Nome do funcionário: ' + rh.nome" modal-dialog-class="modal-xl">
    <template #body>
      <div class="row mb-4">
        <div class="col">
          <span><strong>Nome: </strong>{{ rh.nome }}</span>
        </div>
        <div class="col">
          <span><strong>Telefone: </strong>{{ rh.telefone }}</span>
        </div>
      </div>
      <div class="row mb-4">
        <div class="col">
          <span><strong>CPF: </strong>{{ rh.cpf }}</span>
        </div>
        <div class="col">
          <span><strong>E-mail: </strong>{{ rh.email }}</span>
        </div>
        <div class="col">
          <span><strong>Formação: </strong>{{ rh.formacao }}</span>
        </div>
      </div>
      <div class="row mb-4">
        <div class="col">
          <span><strong>Função: </strong>{{ rh.funcao }}</span>
        </div>
        <div class="col">
          <span><strong>CTF: </strong>{{ rh.ctf }}</span>
        </div>
        <div class="col">
          <span><strong>Validade: </strong>{{ dateTimeFormat(rh.ctf_validade) }}</span>
        </div>
      </div>
      <div class="row mb-4">
        <div class="col">
          <!-- Opções ainda serão passadas -->
          <!-- <span><strong>Conselho de classe: </strong>{{ rh.conselho_classe ? 'Sim' : 'Não' }}</span> -->
        </div>
        <div class="col">
          <span><strong>Número de registro: </strong>{{ rh.numero_registro }}</span>
        </div>
      </div>
      <div class="row mb-4">
        <div class="col">
          <span><strong>Observação: </strong>{{ rh.observacao }}</span>
        </div>
      </div>
      <div class="row">
        <strong class="px-3">Documentos</strong>
        <div class="d-flex flex-wrap">
          <div v-for="documento in rh.documentos" :key="documento.id" class="col-xl-6 py-2">
            <img :src="route('home') + documento.caminho" :title="documento.nome" :alt="documento.nome" class="w-100" />
          </div>
        </div>
      </div>
    </template>
    <template #footer>

    </template>
  </Modal>
</template>
