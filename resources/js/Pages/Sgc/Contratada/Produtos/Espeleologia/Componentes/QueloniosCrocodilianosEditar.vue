<script setup>
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import NavButton from '@/Components/NavButton.vue';
import Table from '@/Components/Table.vue';
import { ref } from 'vue';

const props = defineProps({
  form: {
    type: Object,
    required: true,
  },
  pontoRecords: {
    type: Array,
    default: () => [],
  },
  subStep: {
    type: Number,
    default: 4,
  },
  disabled: {
    type: Boolean,
    default: false,
  },
});

const emit = defineEmits(['next', 'prev', 'adicionar-ponto', 'excluir-ponto']);

// Estado para gerenciar o formulário de um único ponto
const formPontoAmostragem = ref({
  id: null,
  ponto_de_coleta: '',
  nome_curso_hidrico: '',
  // coordenadas: '',
  latitude: '',
  longitude: '',
  bacia: '',
  profundidade: null,
  largura: null,
  tipo_substrato: '',
  errors: {},
});

// Função para editar um ponto existente
const editPonto = (ponto) => {
  if (props.disabled) return;
  formPontoAmostragem.value = {
    ...ponto,
    errors: {},
  };
};

// Função para limpar o formulário após adicionar ou atualizar
const resetForm = () => {
  formPontoAmostragem.value = {
    id: null,
    ponto_de_coleta: '',
    nome_curso_hidrico: '',
    // coordenadas: '',
    latitude: '',
    longitude: '',
    bacia: '',
    profundidade: null,
    largura: null,
    tipo_substrato: '',
    errors: {},
  };
};

// Função para adicionar ou atualizar ponto
const handleAdicionarPonto = () => {
  if (props.disabled) return;
  emit('adicionar-ponto', formPontoAmostragem);
  resetForm();
};
</script>

<template>
  <form @submit.prevent="$emit('next')">
    <div class="card">
      <div class="card-body">
        <h4 class="mb-3" style="text-align: center;">QUELÔNIOS E CROCODILIANOS</h4>

        <div class="mb-4">
          <div class="form-check mb-3">
            <input
              type="checkbox"
              class="form-check-input"
              id="nao_se_aplica"
              v-model="form.nao_se_aplica"
              :disabled="disabled"
            />
            <label class="form-check-label" for="nao_se_aplica">Não se aplica</label>
            <InputError :message="form.errors.nao_se_aplica" />
          </div>

          <div v-if="!form.nao_se_aplica" class="mb-6">
            <div class="row mb-3">
              <div class="col-12 col-md-6">
                <InputLabel value="Ponto de Coleta" for="ponto_de_coleta" />
                <input
                  type="text"
                  id="ponto_de_coleta"
                  class="form-control"
                  v-model="formPontoAmostragem.ponto_de_coleta"
                  :disabled="disabled || form.nao_se_aplica"
                />
                <InputError :message="formPontoAmostragem.errors.ponto_de_coleta" />
              </div>
              <div class="col-12 col-md-6">
                <InputLabel value="Nome do Curso Hídrico" for="nome_curso_hidrico" />
                <input
                  type="text"
                  id="nome_curso_hidrico"
                  class="form-control"
                  v-model="formPontoAmostragem.nome_curso_hidrico"
                  :disabled="disabled || form.nao_se_aplica"
                />
                <InputError :message="formPontoAmostragem.errors.nome_curso_hidrico" />
              </div>
            </div>
            <div class="row mb-3">
              <div class="col-12 col-md-6">
                <InputLabel value="Latitude" for="latitude" />
                <input
                  type="text"
                  id="latitude"
                  class="form-control"
                  v-model="formPontoAmostragem.latitude"
                  placeholder="Ex: -23.123, -46.456"
                  :disabled="disabled || form.nao_se_aplica"
                />
                <InputError :message="formPontoAmostragem.errors.latitude" />
              </div>
              <div class="col-12 col-md-6">
                <InputLabel value="Longitude" for="longitude" />
                <input
                  type="text"
                  id="longitude"
                  class="form-control"
                  v-model="formPontoAmostragem.longitude"
                  placeholder="Ex: -23.123, -46.456"
                  :disabled="disabled || form.nao_se_aplica"
                />
                <InputError :message="formPontoAmostragem.errors.longitude" />
              </div>
              <div class="col-12 col-md-6">
                <InputLabel value="Bacia Hidrográfica" for="bacia" />
                <input
                  type="text"
                  id="bacia"
                  class="form-control"
                  v-model="formPontoAmostragem.bacia"
                  :disabled="disabled || form.nao_se_aplica"
                />
                <InputError :message="formPontoAmostragem.errors.bacia" />
              </div>
            </div>
            <div class="row mb-3">
              <div class="col-12 col-md-6">
                <InputLabel value="Profundidade (m)" for="profundidade" />
                <input
                  type="number"
                  step="any"
                  id="profundidade"
                  class="form-control"
                  v-model="formPontoAmostragem.profundidade"
                  :disabled="disabled || form.nao_se_aplica"
                />
                <InputError :message="formPontoAmostragem.errors.profundidade" />
              </div>
              <div class="col-12 col-md-6">
                <InputLabel value="Largura (m)" for="largura" />
                <input
                  type="number"
                  step="any"
                  id="largura"
                  class="form-control"
                  v-model="formPontoAmostragem.largura"
                  :disabled="disabled || form.nao_se_aplica"
                />
                <InputError :message="formPontoAmostragem.errors.largura" />
              </div>
            </div>
            <div class="row mb-3">
              <div class="col-12">
                <InputLabel value="Tipo de Substrato" for="tipo_substrato" />
                <textarea
                  id="tipo_substrato"
                  class="form-control"
                  v-model="formPontoAmostragem.tipo_substrato"
                  rows="5"
                  :disabled="disabled || form.nao_se_aplica"
                ></textarea>
                <InputError :message="formPontoAmostragem.errors.tipo_substrato" />
              </div>
            </div>
            <div class="row mb-4">
              <div class="col d-flex justify-content-end">
                <NavButton
                  type="button"
                  type-button="success"
                  title="Adicionar Ponto"
                  @click="handleAdicionarPonto"
                  :disabled="disabled || form.nao_se_aplica"
                />
              </div>
            </div>
            <div class="table-responsive">
              <Table
                :columns="['Ponto de Coleta', 'Curso Hídrico', 'Bacia', 'Largura (m)', 'Ação']"
                :records="{ data: pontoRecords, links: [] }"
              >
                <template #body="{ item }">
                  <tr>
                    <td>{{ item.ponto_de_coleta || 'N/A' }}</td>
                    <td>{{ item.nome_curso_hidrico || 'N/A' }}</td>
                    <td>{{ item.bacia || 'N/A' }}</td>
                    <td>{{ item.largura || 'N/A' }}</td>
                    <td class="text-center" style="min-width: 150px;">
                      <NavButton
                        @click="editPonto(item)"
                        type-button="primary"
                        title="Editar"
                        class="me-2"
                        :disabled="disabled"
                      >
                        <i class="bi bi-pencil"></i>
                      </NavButton>
                      <NavButton
                        @click="$emit('excluir-ponto', item.id)"
                        type-button="danger"
                        title="Excluir"
                        :disabled="disabled"
                      >
                        <i class="bi bi-trash"></i>
                      </NavButton>
                    </td>
                  </tr>
                </template>
              </Table>
            </div>
          </div>
          <div v-else class="alert alert-info text-center">
            Nenhum ponto de amostragem disponível, pois a opção "Não se aplica" está marcada.
          </div>
        </div>

        <div class="d-flex justify-content-between mt-4">
          <NavButton type="button" type-button="secondary" title="Voltar" @click="$emit('prev')" />
          <NavButton type="submit" type-button="primary" title="Avançar" />
        </div>
        <slot name="footer">
          <h4 class="text-center mt-4 font-weight-bold text-muted">{{ subStep }}/5</h4>
        </slot>
      </div>
    </div>
  </form>
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
.form-control {
  border: 1px solid #ced4da;
  border-radius: 4px;
  padding: 0.375rem 0.75rem;
  width: 100%;
}
textarea.form-control {
  resize: vertical;
  min-height: 100px;
}
.alert-info {
  font-size: 1rem;
  padding: 1rem;
  border-radius: 6px;
  background-color: #e7f1ff;
  color: #084298;
}
.text-muted {
  color: #6c757d !important;
}
.btn-sm {
  padding: 0.25rem 0.5rem;
  font-size: 0.875rem;
}
.table-responsive {
  margin-bottom: 1rem;
}
input:disabled,
textarea:disabled,
button:disabled,
.form-check-input:disabled {
  background-color: #f5f5f5;
  color: #999;
  cursor: not-allowed;
}
</style>