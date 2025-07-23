<script setup>
import { ref } from 'vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import NavButton from '@/Components/NavButton.vue';
import Table from '@/Components/Table.vue';
import vSelect from 'vue-select';
import 'vue-select/dist/vue-select.css';

const props = defineProps({
  form: {
    type: Object,
    required: true,
  },
  metodologiaRecords: {
    type: Array,
    default: () => [],
  },
});

const emit = defineEmits(['next', 'prev', 'adicionar-metodologia', 'excluir-metodologia']);

// Opções predefinidas para o grupo faunístico
const grupoFaunisticoOptions = [
  { value: 'Avifauna', label: 'Avifauna' },
  { value: 'Herpetofauna', label: 'Herpetofauna' },
  { value: 'Mastofauna', label: 'Mastofauna' },
  { value: 'Ictiofauna', label: 'Ictiofauna' },
  { value: 'Bentos', label: 'Bentos' },
];

// Estado para gerenciar o formulário de uma metodologia
const formMetodologia = ref({
  id: null,
  grupo_faunistico: '',
  metodologia: '',
  errors: {},
});

// Função para editar uma metodologia existente
const editMetodologia = (metodologia) => {
  formMetodologia.value = {
    id: metodologia.id || null,
    grupo_faunistico: metodologia.grupo_faunistico || '',
    metodologia: metodologia.metodologia || '',
    errors: {},
  };
};

// Função para limpar o formulário após adicionar ou atualizar
const resetForm = () => {
  formMetodologia.value = {
    id: null,
    grupo_faunistico: '',
    metodologia: '',
    errors: {},
  };
};

// Função para validar o formulário
const validateMetodologia = () => {
  const errors = {};
  if (!formMetodologia.value.grupo_faunistico) {
    errors.grupo_faunistico = 'O campo Grupo Faunístico é obrigatório.';
  }
  formMetodologia.value.errors = errors;
  return Object.keys(errors).length === 0;
};

// Função para adicionar ou atualizar metodologia
const handleAdicionarMetodologia = () => {
  if (!validateMetodologia()) {
    return;
  }
  emit('adicionar-metodologia', { ...formMetodologia.value });
  resetForm();
};
</script>

<template>
  <form @submit.prevent="$emit('next')">
    <div class="card">
      <div class="card-body">
        <h4 class="mb-3" style="text-align: center;">METODOLOGIA</h4>

        <div class="mb-4">
          <div class="row mb-3">
            <div class="col-12">
              <InputLabel value="Grupo Faunístico" for="grupo_faunistico" />
              <vSelect
                v-model="formMetodologia.grupo_faunistico"
                :options="grupoFaunisticoOptions"
                :reduce="g => g.value"
                placeholder="Selecione um grupo"
                class="form-control"
                id="grupo_faunistico"
              />
              <InputError :message="formMetodologia.errors.grupo_faunistico" />
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-12">
              <InputLabel value="Metodologia" for="metodologia" />
              <textarea
                id="metodologia"
                class="form-control"
                v-model="formMetodologia.metodologia"
                rows="4"
              ></textarea>
              <InputError :message="formMetodologia.errors.metodologia" />
            </div>
          </div>
          <div class="row mb-4">
            <div class="col d-flex justify-content-end">
              <NavButton
                type="button"
                type-button="success"
                :title="formMetodologia.id ? 'Atualizar Metodologia' : 'Adicionar Metodologia'"
                @click="handleAdicionarMetodologia"
              />
            </div>
          </div>

          <div v-if="metodologiaRecords.length" class="table-responsive">
            <Table
              :columns="['Grupo Faunístico', 'Metodologia', 'Ação']"
              :records="{ data: metodologiaRecords, links: [] }"
            >
              <template #body="{ item }">
                <tr>
                  <td>{{ item.grupo_faunistico || 'N/A' }}</td>
                  <td>{{ item.metodologia || 'N/A' }}</td>
                  <td class="text-center" style="min-width: 150px;">
                    <NavButton
                      @click="editMetodologia(item)"
                      type-button="primary"
                      title="Editar"
                      class="me-2"
                    >
                      <i class="bi bi-pencil"></i>
                    </NavButton>
                    <NavButton
                      @click="$emit('excluir-metodologia', item.id)"
                      type-button="danger"
                      title="Excluir"
                    >
                      <i class="bi bi-trash"></i>
                    </NavButton>
                  </td>
                </tr>
              </template>
            </Table>
          </div>
          <div v-else class="alert alert-info text-center">
            Nenhuma metodologia disponível. Preencha os campos acima e clique em "Adicionar Metodologia" para começar.
          </div>
        </div>

        <div class="d-flex justify-content-between mt-4">
          <NavButton type="button" type-button="secondary" title="Voltar" @click="$emit('prev')" />
          <NavButton type="submit" type-button="primary" title="Avançar" />
        </div>
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
.table-responsive {
  margin-bottom: 1rem;
}
</style>