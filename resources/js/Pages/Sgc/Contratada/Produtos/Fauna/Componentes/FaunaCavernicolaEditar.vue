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
    default: 5,
  },
  disabled: {
    type: Boolean,
    default: false,
  },
});

const emit = defineEmits(['next', 'prev', 'adicionar-ponto-cavernicola', 'excluir-ponto-cavernicola']);

// Estado para gerenciar o formulário de um único ponto
const formPontoCavernicola = ref({
  id: null,
  cavidade: '',
  latitude: null,
  longitude: null,
  distancia_eixo_rodovia: null,
  formacao_associada: '',
  temperatura_media_interna: null,
  temperatura_media_externa: null,
  umidade_relativa_interna: null,
  umidade_relativa_externa: null,
  errors: {},
});

// Função para editar um ponto existente
const editPonto = (ponto) => {
  if (props.disabled) return;
  formPontoCavernicola.value = {
    id: ponto.id || null,
    cavidade: ponto.cavidade || '',
    latitude: ponto.latitude || null,
    longitude: ponto.longitude || null,
    distancia_eixo_rodovia: ponto.distancia_eixo_rodovia || null,
    formacao_associada: ponto.formacao_associada || '',
    temperatura_media_interna: ponto.temperatura_media_interna || null,
    temperatura_media_externa: ponto.temperatura_media_externa || null,
    umidade_relativa_interna: ponto.umidade_relativa_interna || null,
    umidade_relativa_externa: ponto.umidade_relativa_externa || null,
    errors: {},
  };
};

// Função para limpar o formulário após adicionar ou atualizar
const resetForm = () => {
  formPontoCavernicola.value = {
    id: null,
    cavidade: '',
    latitude: null,
    longitude: null,
    distancia_eixo_rodovia: null,
    formacao_associada: '',
    temperatura_media_interna: null,
    temperatura_media_externa: null,
    umidade_relativa_interna: null,
    umidade_relativa_externa: null,
    errors: {},
  };
};

// Função para validar o formulário antes de adicionar ou atualizar
const validatePonto = () => {
  const errors = {};
  if (!formPontoCavernicola.value.cavidade) {
    errors.cavidade = 'O campo Cavidade é obrigatório.';
  }
  formPontoCavernicola.value.errors = errors;
  return Object.keys(errors).length === 0;
};

// Função para adicionar ou atualizar ponto
const handleAdicionarPonto = () => {
  if (props.disabled) return;
  if (!validatePonto()) {
    return;
  }
  emit('adicionar-ponto-cavernicola', { ...formPontoCavernicola.value });
  resetForm();
};
</script>

<template>
  <form @submit.prevent="$emit('next')">
    <div class="card">
      <div class="card-body">
        <h4 class="mb-3" style="text-align: center;">FAUNA CAVERNÍCOLA</h4>

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
                <InputLabel value="Cavidade" for="cavidade" />
                <input
                  type="text"
                  id="cavidade"
                  class="form-control"
                  v-model="formPontoCavernicola.cavidade"
                  :disabled="disabled || form.nao_se_aplica"
                />
                <InputError :message="formPontoCavernicola.errors.cavidade" />
              </div>
              <div class="col-12 col-md-6">
                <InputLabel value="Latitude" for="latitude" />
                <input
                  type="number"
                  step="any"
                  id="latitude"
                  class="form-control"
                  v-model="formPontoCavernicola.latitude"
                  :disabled="disabled || form.nao_se_aplica"
                />
                <InputError :message="formPontoCavernicola.errors.latitude" />
              </div>
            </div>
            <div class="row mb-3">
              <div class="col-12 col-md-6">
                <InputLabel value="Longitude" for="longitude" />
                <input
                  type="number"
                  step="any"
                  id="longitude"
                  class="form-control"
                  v-model="formPontoCavernicola.longitude"
                  :disabled="disabled || form.nao_se_aplica"
                />
                <InputError :message="formPontoCavernicola.errors.longitude" />
              </div>
              <div class="col-12 col-md-6">
                <InputLabel value="Distância do Eixo da Rodovia (m)" for="distancia_eixo_rodovia" />
                <input
                  type="number"
                  step="any"
                  id="distancia_eixo_rodovia"
                  class="form-control"
                  v-model="formPontoCavernicola.distancia_eixo_rodovia"
                  :disabled="disabled || form.nao_se_aplica"
                />
                <InputError :message="formPontoCavernicola.errors.distancia_eixo_rodovia" />
              </div>
            </div>
            <div class="row mb-3">
              <div class="col-12">
                <InputLabel value="Formação Associada" for="formacao_associada" />
                <input
                  type="text"
                  id="formacao_associada"
                  class="form-control"
                  v-model="formPontoCavernicola.formacao_associada"
                  :disabled="disabled || form.nao_se_aplica"
                />
                <InputError :message="formPontoCavernicola.errors.formacao_associada" />
              </div>
            </div>
            <div class="row mb-3">
              <div class="col-12 col-md-6">
                <InputLabel value="Temperatura Média Interna (°C)" for="temperatura_media_interna" />
                <input
                  type="number"
                  step="any"
                  id="temperatura_media_interna"
                  class="form-control"
                  v-model="formPontoCavernicola.temperatura_media_interna"
                  :disabled="disabled || form.nao_se_aplica"
                />
                <InputError :message="formPontoCavernicola.errors.temperatura_media_interna" />
              </div>
              <div class="col-12 col-md-6">
                <InputLabel value="Temperatura Média Externa (°C)" for="temperatura_media_externa" />
                <input
                  type="number"
                  step="any"
                  id="temperatura_media_externa"
                  class="form-control"
                  v-model="formPontoCavernicola.temperatura_media_externa"
                  :disabled="disabled || form.nao_se_aplica"
                />
                <InputError :message="formPontoCavernicola.errors.temperatura_media_externa" />
              </div>
            </div>
            <div class="row mb-3">
              <div class="col-12 col-md-6">
                <InputLabel value="Umidade Relativa Interna (%)" for="umidade_relativa_interna" />
                <input
                  type="number"
                  step="any"
                  id="umidade_relativa_interna"
                  class="form-control"
                  v-model="formPontoCavernicola.umidade_relativa_interna"
                  :disabled="disabled || form.nao_se_aplica"
                />
                <InputError :message="formPontoCavernicola.errors.umidade_relativa_interna" />
              </div>
              <div class="col-12 col-md-6">
                <InputLabel value="Umidade Relativa Externa (%)" for="umidade_relativa_externa" />
                <input
                  type="number"
                  step="any"
                  id="umidade_relativa_externa"
                  class="form-control"
                  v-model="formPontoCavernicola.umidade_relativa_externa"
                  :disabled="disabled || form.nao_se_aplica"
                />
                <InputError :message="formPontoCavernicola.errors.umidade_relativa_externa" />
              </div>
            </div>
            <div class="row mb-4">
              <div class="col d-flex justify-content-end">
                <NavButton
                  type="button"
                  type-button="success"
                  :title="formPontoCavernicola.id ? 'Atualizar Ponto' : 'Adicionar Ponto'"
                  @click="handleAdicionarPonto"
                  :disabled="disabled || form.nao_se_aplica"
                />
              </div>
            </div>
            <div class="table-responsive">
              <Table
                :columns="['Cavidade', 'Latitude', 'Longitude', 'Distância (m)', 'Formação', 'Ação']"
                :records="{ data: pontoRecords, links: [] }"
              >
                <template #body="{ item }">
                  <tr>
                    <td>{{ item.cavidade || 'N/A' }}</td>
                    <td>{{ item.latitude || '' }}</td>
                    <td>{{ item.longitude || '' }}</td>
                    <td>{{ item.distancia_eixo_rodovia || '' }}</td>
                    <td>{{ item.formacao_associada || '' }}</td>
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
                        @click="$emit('excluir-ponto-cavernicola', item.id)"
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
            Nenhum ponto de fauna cavernícola disponível, pois a opção "Não se aplica" está marcada.
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
button:disabled,
.form-check-input:disabled {
  background-color: #f5f5f5;
  color: #999;
  cursor: not-allowed;
}
</style>