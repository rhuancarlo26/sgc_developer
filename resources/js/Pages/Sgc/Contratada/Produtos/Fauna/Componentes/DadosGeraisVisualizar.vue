<script setup>
import { defineProps, defineEmits } from 'vue';
import InputLabel from '@/Components/InputLabel.vue';
import NavButton from '@/Components/NavButton.vue';
import { IconScanEye, IconDeviceFloppy, IconPlus, IconDots, IconMap } from "@tabler/icons-vue";

defineProps({
  campanha: {
    type: Object,
    default: () => ({}),
  },
  abioRecords: {
    type: Array,
    default: () => [],
  },
  profissionalRecords: {
    type: Array,
    default: () => [],
  },
  subStep: {
    type: Number,
    default: 2,
  },
});

defineEmits(['next', 'prev']);

import { ref } from 'vue'

const showAbioModal = ref(false)
const selectedAbio = ref(null)

function openAbioModal(abio) {
  selectedAbio.value = abio
  showAbioModal.value = true
}

function closeAbioModal() {
  showAbioModal.value = false
  selectedAbio.value = null
}


</script>

<template>
  <div class="card">
    <div class="card-body">
      <h4 class="mb-3" style="text-align: center;">DADOS GERAIS DA CAMPANHA</h4>
      
      <!-- Dados da Campanha -->
      <div v-if="campanha && Object.keys(campanha).length" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div>
          <InputLabel value="DATA INICIAL" for="data_campanha_inicial" />
          <input
            type="text"
            class="form-control"
            id="data_campanha_inicial"
            :value="campanha.data_campanha_inicial || 'Não informado'"
            disabled
          />
        </div>
        <div>
          <InputLabel value="DATA FINAL" for="data_campanha_final" />
          <input
            type="text"
            class="form-control"
            id="data_campanha_final"
            :value="campanha.data_campanha_final || 'Não informado'"
            disabled
          />
        </div>
        <div>
          <InputLabel value="PERÍODO" for="periodo" />
          <input
            type="text"
            class="form-control"
            id="periodo"
            :value="campanha.periodo || 'Não informado'"
            disabled
          />
        </div>
        <div>
          <InputLabel value="EMPREENDIMENTO" for="cod_emp" />
          <input
            type="text"
            class="form-control"
            id="cod_emp"
            :value="campanha.cod_emp || 'Não informado'"
            disabled
          />
        </div>
        <div>
          <InputLabel value="SUBPRODUTO" for="familia" />
          <input
            type="text"
            class="form-control"
            id="familia"
            :value="campanha.familia || 'Não informado'"
            disabled
          />
        </div>
        <div>
          <InputLabel value="SEI DNIT" for="sei_dnit" />
          <input
            type="text"
            class="form-control"
            id="sei_dnit"
            :value="campanha.sei_dnit || 'Não informado'"
            disabled
          />
        </div>
        <div>
          <InputLabel value="OBSERVAÇÕES" for="observacoes" />
          <input
            type="text"
            class="form-control"
            id="observacoes"
            :value="campanha.observacoes || 'Não informado'"
            disabled
          ></input>
        </div>
      </div>

      <!-- Licenças (Abios) -->
      <h5 class="section-title">ABIO'S VINCULADAS</h5>

      <div v-if="abioRecords && abioRecords.length" class="table-responsive">
        <table class="table table-bordered align-middle">
          <thead>
            <tr>
              <th>Número da Licença</th>
              <th width="80" class="text-center">Visualizar</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="abio in abioRecords" :key="abio.id">
              <td>
                {{ abio.abio?.numero_licenca || 'Não informado' }}
              </td>
              <td class="text-center">
                <button
                  class="btn btn-sm btn-outline-primary"
                  @click="openAbioModal(abio.abio)"
                  :disabled="!abio.abio"
                >
                  <IconScanEye />
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div v-else class="alert alert-info text-center">
        Nenhuma licença vinculada.
      </div>

      <!-- Modal ABIO -->
      <div
        class="modal fade show"
        tabindex="-1"
        style="display: block;"
        v-if="showAbioModal"
      >
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
          <div class="modal-content">

            <!-- Header -->
            <div class="modal-header justify-content-center position-relative">
              <h5 class="modal-title text-center w-100">
                ABIO - DETALHES DA LICENÇA
              </h5>
              <button
                type="button"
                class="btn-close position-absolute end-0 me-3"
                @click="closeAbioModal"
              ></button>
            </div>

            <!-- Body -->
            <div class="modal-body" v-if="selectedAbio">
              <div class="abio-list">

                <div class="row abio-row">
                  <div class="col-md-4 abio-label">NÚMERO DA LICENÇA</div>
                  <div class="col-md-8 abio-value">{{ selectedAbio.numero_licenca }}</div>
                </div>

                <div class="row abio-row">
                  <div class="col-md-4 abio-label">DATA DE EMISSÃO</div>
                  <div class="col-md-8 abio-value">{{ selectedAbio.data_emissao }}</div>
                </div>

                <div class="row abio-row">
                  <div class="col-md-4 abio-label">VENCIMENTO</div>
                  <div class="col-md-8 abio-value">{{ selectedAbio.vencimento }}</div>
                </div>

                <div class="row abio-row">
                  <div class="col-md-4 abio-label">Nº SEI</div>
                  <div class="col-md-8 abio-value">{{ selectedAbio.numero_sei }}</div>
                </div>

                <div class="row abio-row">
                  <div class="col-md-4 abio-label">PROCESSO DNIT</div>
                  <div class="col-md-8 abio-value">{{ selectedAbio.processo_dnit }}</div>
                </div>

                <div class="row abio-row">
                  <div class="col-md-4 abio-label">EMPREENDIMENTO</div>
                  <div class="col-md-8 abio-value">{{ selectedAbio.empreendimento }}</div>
                </div>

                <div class="row abio-row">
                  <div class="col-md-4 abio-label">INÍCIO SUBTRECHO</div>
                  <div class="col-md-8 abio-value">{{ selectedAbio.inicio_subtrecho }}</div>
                </div>

                <div class="row abio-row">
                  <div class="col-md-4 abio-label">FIM SUBTRECHO</div>
                  <div class="col-md-8 abio-value">{{ selectedAbio.fim_subtrecho }}</div>
                </div>

                <div class="row abio-row">
                  <div class="col-md-4 abio-label">EXTENSÃO</div>
                  <div class="col-md-8 abio-value">{{ selectedAbio.extensao }}</div>
                </div>

                <div class="row abio-row">
                  <div class="col-md-4 abio-label">EMISSOR</div>
                  <div class="col-md-8 abio-value">{{ selectedAbio.emissor }}</div>
                </div>

                <div class="row abio-row">
                  <div class="col-md-4 abio-label">OBSERVAÇÕES</div>
                  <div class="col-md-8 abio-value">
                    {{ selectedAbio.obs_renovacao || '—' }}
                  </div>
                </div>

              </div>
            </div>



            <!-- Footer -->
            <div class="modal-footer">
              <button class="btn btn-secondary" @click="closeAbioModal">
                Fechar
              </button>
            </div>

          </div>
        </div>
      </div>

      <!-- Backdrop -->
      <div class="modal-backdrop fade show" v-if="showAbioModal"></div>


      <!-- Profissionais -->
      <h5 class="section-title">PROFISSIONAIS VINCULADOS</h5>
      <div v-if="profissionalRecords && profissionalRecords.length" class="table-responsive">
        <table class="table table-bordered">
          <thead>
            <tr>
              <th>PROFISSIONAL</th>
              <th>GRUPO FAUNÍSTICO</th>
              <th>FORMAÇÃO</th>
              <th>FUNÇÃO</th>
              <th>CTF</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="profissional in profissionalRecords" :key="profissional.id">
              <td>{{ profissional.profissional?.profissional || 'Não informado' }}</td>
              <td>{{ profissional.grupo_faunistico || 'Não informado' }}</td>
              <td>{{ profissional.profissional?.formacao || 'Não informado' }}</td>
              <td>{{ profissional.profissional?.funcao || 'Não informado' }}</td>
              <td>{{ profissional.profissional?.ctf || 'Não informado' }}</td>
            </tr>
          </tbody>
        </table>
      </div>
      <div v-else class="alert alert-info text-center">
        Nenhum profissional vinculado.
      </div>

      <!-- Navegação -->
      <div class="d-flex justify-content-between mt-4">
        <NavButton type="button" type-button="secondary" title="Voltar" @click="$emit('prev')" />
        <NavButton type="button" type-button="primary" title="Avançar" @click="$emit('next')" />
      </div>
      <h4 class="text-center mt-4 font-weight-bold text-muted">2/5</h4>
    </div>
  </div>
</template>

<style scoped>
.card {
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  background-color: #fff;
  margin: 1.5rem;
}
.card-body {
  padding: 2rem;
}
.card-title {
  font-size: 1.75rem;
  font-weight: 700;
  color: #343a40;
  margin-bottom: 1.5rem;
  text-align: center;
}
.section-title {
  font-size: 1.25rem;
  font-weight: 600;
  color: #495057;
  margin-top: 2rem;
  margin-bottom: 1rem;
  text-transform: uppercase;
}
.status-container {
  text-align: center;
  margin-bottom: 4rem;
}
.badge {
  font-size: 0.80rem;
  padding: 0.30rem 1.5rem;
  border-radius: 1.5rem;
  font-weight: 600;
}
.bg-success {
  background-color: #28a745;
  color: white;
}
.bg-warning {
  background-color: #ffc107;
  color: #fbfdff;
}
.bg-danger {
  background-color: #dc3545;
  color: white;
}
.form-control:disabled {
  background-color: #f8f9fa;
  color: #495057;
  border: 1px solid #ced4da;
  opacity: 1;
  width: 100%;
  min-width: 200px; /* Aumenta a largura mínima para evitar corte */
  max-width: 100%; /* Garante que não ultrapasse a coluna */
  overflow-wrap: break-word;
  white-space: normal;
  padding: 0.5rem 1rem; /* Aumenta o padding para mais espaço interno */
}
textarea.form-control:disabled {
  resize: none;
  min-height: 120px;
  max-height: 400px;
  overflow-y: auto;
  width: 100%;
  min-width: 300px; /* Aumenta a largura mínima para consistência */
  max-width: 100%;
  padding: 0.5rem 1rem;
}
.alert-info {
  font-size: 1rem;
  padding: 1rem;
  border-radius: 6px;
  background-color: #e7f1ff;
  color: #084298;
}
.table-responsive {
  overflow-x: auto;
  margin-bottom: 1.5rem;
}
.table th, .table td {
  padding: 0.75rem;
  vertical-align: top;
  border: 1px solid #dee2e6;
}
.table thead th {
  background-color: #f8f9fa;
}
.text-muted {
  color: #6c757d !important;
}

.abio-list {
  font-size: 0.95rem;
}

.abio-row {
  padding: 6px 0;
  border-bottom: 1px solid #eee;
}

.abio-row:last-child {
  border-bottom: none;
}

.abio-label {
  font-weight: 600;
  text-transform: uppercase;
  font-size: 0.75rem;
}

.abio-value {
  line-height: 1.4;
}


</style>
