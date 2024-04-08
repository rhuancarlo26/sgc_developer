<script setup>
import Modal from "@/Components/Modal.vue";
import { ref } from "vue";

const detalhe = ref({});
const modalDetalhes = ref(null);

const abrirModal = (item) => {
  detalhe.value = item;

  modalDetalhes.value.getBsModal().show();
}

defineExpose({ abrirModal });
</script>

<template>
  <Modal ref="modalDetalhes" title="Visualizar serviço cadastrado" modal-dialog-class="modal-xl">
    <template #body>
      <div class="mb-4">
        <div class="card-header">
          <h3 class="my-0">Dados gerais</h3>
        </div>
        <div class="card-body">
          <div class="accordion" id="servico">
            <div class="accordion-item">
              <h2 class="accordion-header" id="heading-1">
                <button class="accordion-button " type="button" data-bs-toggle="collapse" data-bs-target="#introducao"
                  aria-expanded="true">
                  Introdução
                </button>
              </h2>
              <div id="introducao" class="accordion-collapse collapse show" data-bs-parent="#servico">
                <div class="accordion-body pt-0">
                  <p>{{ detalhe.introducao }}</p>
                </div>
              </div>
            </div>
            <div class="accordion-item">
              <h2 class="accordion-header" id="heading-2">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                  data-bs-target="#justificativa" aria-expanded="false">
                  Justificativa
                </button>
              </h2>
              <div id="justificativa" class="accordion-collapse collapse" data-bs-parent="#servico">
                <div class="accordion-body pt-0">
                  <p>{{ detalhe.justificativa }}</p>
                </div>
              </div>
            </div>
            <div class="accordion-item">
              <h2 class="accordion-header" id="heading-3">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                  data-bs-target="#objetivo" aria-expanded="false">
                  Objetivos
                </button>
              </h2>
              <div id="objetivo" class="accordion-collapse collapse" data-bs-parent="#servico">
                <div class="accordion-body pt-0">
                  <p>{{ detalhe.objetivo }}</p>
                </div>
              </div>
            </div>
            <div class="accordion-item">
              <h2 class="accordion-header" id="heading-4">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                  data-bs-target="#metodologia" aria-expanded="false">
                  Metodologia
                </button>
              </h2>
              <div id="metodologia" class="accordion-collapse collapse" data-bs-parent="#servico">
                <div class="accordion-body pt-0">
                  <p>{{ detalhe.metodologia }}</p>
                </div>
              </div>
            </div>
            <div class="accordion-item">
              <h2 class="accordion-header" id="heading-4">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                  data-bs-target="#publico_alvo" aria-expanded="false">
                  Público alvo
                </button>
              </h2>
              <div id="publico_alvo" class="accordion-collapse collapse" data-bs-parent="#servico">
                <div class="accordion-body pt-0">
                  <p>{{ detalhe.publico_alvo }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="mb-4">
        <div class="card-header">
          <h3 class="my-0">Recursos humanos</h3>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover non-hover">
              <thead>
                <tr>
                  <th>Nome do profissional</th>
                  <th>Profissão</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="rh in detalhe.rhs" :key="rh.id">
                  <td>{{ rh.nome }}</td>
                  <td>{{ rh.formacao }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <div class="mb-4">
        <div class="card-header">
          <h3 class="my-0">Veículos</h3>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover non-hover">
              <thead>
                <tr>
                  <th>Descriçao</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="veiculo in detalhe.veiculos" :key="veiculo.id">
                  <td>{{ `${veiculo.codigo?.codigo} - ${veiculo.codigo?.descricao}` }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <div class="mb-4">
        <div class="card-header">
          <h3 class="my-0">Equipamentos</h3>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover non-hover">
              <thead>
                <tr>
                  <th>Nome</th>
                  <th>Modelo</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="equipamento in detalhe.equipamentos" :key="equipamento.id">
                  <td>{{ equipamento.nome }}</td>
                  <td>{{ equipamento.descricao }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <div class="mb-4">
        <div class="card-header">
          <h3 class="my-0">Licenças</h3>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover non-hover">
              <thead>
                <tr>
                  <th>Licença</th>
                  <th>Condicionante</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="condicionante in detalhe.condicionantes" :key="condicionante.id">
                  <td>{{ condicionante.licenca?.numero_licenca }}</td>
                  <td>{{ condicionante.descricao }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </template>
  </Modal>
</template>