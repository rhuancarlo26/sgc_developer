<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import NavButton from '@/Components/NavButton.vue';
import Table from '@/Components/Table.vue';
import vSelect from 'vue-select';
import 'vue-select/dist/vue-select.css';
import { ref, watch } from 'vue';
import axios from 'axios';

const props = defineProps({
  form: Object,
  moduloRecords: Array,
  ufs: Array,
  biomas: Array,
  subStep: Number,
  disabled: {
    type: Boolean,
    default: false,
  },
});

const emit = defineEmits(['next', 'prev', 'adicionar-modulo', 'excluir-modulo', 'get-localizacao']);

// Estado para gerenciar o formulário de um único módulo
const formModuloAmostral = ref({
  id: null,
  data_cadastro: null,
  tamanho_modulo: null,
  uf: null,
  municipio: null,
  bioma: null,
  fitofisionomia: null,
  latitude_inicial: null,
  longitude_inicial: null,
  latitude_final: null,
  longitude_final: null,
  arquivo: null,
  arquivo_existente: null,
  obs: null,
  errors: {},
});

// Estado para gerenciar municípios
const municipiosOptions = ref([]);
const loadingMunicipios = ref(false);

// Função para buscar municípios com base na UF selecionada
const fetchMunicipios = async (uf) => {
  if (props.disabled || !uf) {
    municipiosOptions.value = [];
    formModuloAmostral.value.municipio = null;
    return;
  }

  loadingMunicipios.value = true;
  try {
    const response = await axios.get(`https://servicodados.ibge.gov.br/api/v1/localidades/estados/${uf}/municipios`);
    municipiosOptions.value = response.data.map(mun => ({
      value: mun.nome,
      label: mun.nome,
    }));
    formModuloAmostral.value.municipio = null;
  } catch (error) {
    console.error('Erro ao buscar municípios:', error);
    municipiosOptions.value = [];
    alert('Erro ao carregar os municípios. Tente novamente.');
  } finally {
    loadingMunicipios.value = false;
  }
};

// Observar mudanças na UF para buscar municípios
watch(() => formModuloAmostral.value.uf, (newUf) => {
  if (!props.disabled) {
    fetchMunicipios(newUf);
  }
});

// Função para editar um módulo existente
const editModulo = (modulo) => {
  if (props.disabled) return;
  formModuloAmostral.value = {
    ...modulo,
    errors: {},
    arquivo: null, // Não carrega o arquivo existente no input file
  };
};

// Função para limpar o formulário após adicionar ou atualizar
const resetForm = () => {
  formModuloAmostral.value = {
    id: null,
    data_cadastro: null,
    tamanho_modulo: null,
    uf: null,
    municipio: null,
    bioma: null,
    fitofisionomia: null,
    latitude_inicial: null,
    longitude_inicial: null,
    latitude_final: null,
    longitude_final: null,
    arquivo: null,
    arquivo_existente: null,
    obs: null,
    errors: {},
  };
  municipiosOptions.value = [];
};

// Função para adicionar ou atualizar módulo
const handleAdicionarModulo = () => {
  if (props.disabled) return;
  emit('adicionar-modulo', formModuloAmostral);
  resetForm();
};
</script>

<template>
  <form @submit.prevent="$emit('next')">
    <div class="card">
      <div class="card-body">
        <h4 class="mb-3" style="text-align: center;">MÓDULOS AMOSTRAIS</h4>
        <div class="mb-4">
          <div class="row mb-4">
            <div class="col-12 col-md-6">
              <InputLabel value="ID Módulo Amostral" for="id_modulo" />
              <input disabled type="text" class="form-control" :value="formModuloAmostral.id || 'Novo'" />
              <InputError :message="formModuloAmostral.errors.id" />
            </div>
            <div class="col-12 col-md-6">
              <InputLabel value="Data" for="data_cadastro" />
              <input
                type="date"
                class="form-control"
                v-model="formModuloAmostral.data_cadastro"
                :disabled="disabled"
              />
              <InputError :message="formModuloAmostral.errors.data_cadastro" />
            </div>
          </div>
          <div class="row mb-4">
            <div class="col-12 col-md-6">
              <InputLabel value="Selecionar o tamanho do Módulo Amostral" for="tamanho_modulo" />
              <div>
                <label class="form-check form-check-inline me-3">
                  <input
                    class="form-check-input"
                    type="radio"
                    name="tamanho_modulo"
                    value="5"
                    v-model="formModuloAmostral.tamanho_modulo"
                    :disabled="disabled"
                  />
                  <span class="form-check-label">5km</span>
                </label>
                <label class="form-check form-check-inline me-3">
                  <input
                    class="form-check-input"
                    type="radio"
                    name="tamanho_modulo"
                    value="4"
                    v-model="formModuloAmostral.tamanho_modulo"
                    :disabled="disabled"
                  />
                  <span class="form-check-label">4km</span>
                </label>
                <label class="form-check form-check-inline me-3">
                  <input
                    class="form-check-input"
                    type="radio"
                    name="tamanho_modulo"
                    value="3"
                    v-model="formModuloAmostral.tamanho_modulo"
                    :disabled="disabled"
                  />
                  <span class="form-check-label">3km</span>
                </label>
                <label class="form-check form-check-inline me-3">
                  <input
                    class="form-check-input"
                    type="radio"
                    name="tamanho_modulo"
                    value="2"
                    v-model="formModuloAmostral.tamanho_modulo"
                    :disabled="disabled"
                  />
                  <span class="form-check-label">2km</span>
                </label>
                <label class="form-check form-check-inline">
                  <input
                    class="form-check-input"
                    type="radio"
                    name="tamanho_modulo"
                    value="1"
                    v-model="formModuloAmostral.tamanho_modulo"
                    :disabled="disabled"
                  />
                  <span class="form-check-label">1km</span>
                </label>
              </div>
              <InputError :message="formModuloAmostral.errors.tamanho_modulo" />
            </div>
            <div class="col-12 col-md-6 d-flex align-items-end">
              <InputLabel :value="`Nº parcelas: ${formModuloAmostral.tamanho_modulo || 0} parcela(s)`" />
            </div>
          </div>
          <div class="row mb-4">
            <div class="col-12 col-md-4">
              <InputLabel value="UF" for="uf" />
              <v-select
                @option:selected="disabled ? null : $emit('get-localizacao')"
                :options="ufs"
                label="uf"
                v-model="formModuloAmostral.uf"
                :reduce="uf => uf.uf"
                placeholder="Selecione uma UF"
                :disabled="disabled"
              >
                <template #no-options>
                  Nenhum registro encontrado.
                </template>
              </v-select>
              <InputError :message="formModuloAmostral.errors.uf" />
            </div>
            <div class="col-12 col-md-4">
              <InputLabel value="Municípios" for="municipio" />
              <v-select
                v-model="formModuloAmostral.municipio"
                :options="municipiosOptions"
                :reduce="mun => mun.value"
                placeholder="Selecione um município"
                :disabled="disabled || loadingMunicipios || !formModuloAmostral.uf"
              >
                <template #no-options>
                  {{ loadingMunicipios ? 'Carregando...' : 'Selecione uma UF primeiro.' }}
                </template>
              </v-select>
              <InputError :message="formModuloAmostral.errors.municipio" />
            </div>
            <div class="col-12 col-md-4">
              <InputLabel value="Bioma" for="bioma" />
              <v-select
                :options="biomas"
                v-model="formModuloAmostral.bioma"
                placeholder="Selecione um bioma"
                :disabled="disabled"
              >
                <template #no-options>
                  Nenhum registro encontrado.
                </template>
              </v-select>
              <InputError :message="formModuloAmostral.errors.bioma" />
            </div>
          </div>
          <div class="row mb-4">
            <div class="col-12">
              <InputLabel value="Fitofisionomia" for="fitofisionomia" />
              <textarea
                class="form-control"
                id="fitofisionomia"
                rows="5"
                v-model="formModuloAmostral.fitofisionomia"
                :disabled="disabled"
              ></textarea>
              <InputError :message="formModuloAmostral.errors.fitofisionomia" />
            </div>
          </div>
          <div class="row mb-4">
            <div class="col-12 col-md-6">
              <InputLabel value="Latitude inicial" for="latitude_inicial" />
              <input
                type="number"
                step="any"
                class="form-control"
                id="latitude_inicial"
                v-model="formModuloAmostral.latitude_inicial"
                :disabled="disabled"
              />
              <InputError :message="formModuloAmostral.errors.latitude_inicial" />
            </div>
            <div class="col-12 col-md-6">
              <InputLabel value="Longitude inicial" for="longitude_inicial" />
              <input
                type="number"
                step="any"
                class="form-control"
                id="longitude_inicial"
                v-model="formModuloAmostral.longitude_inicial"
                :disabled="disabled"
              />
              <InputError :message="formModuloAmostral.errors.longitude_inicial" />
            </div>
          </div>
          <div class="row mb-4">
            <div class="col-12 col-md-6">
              <InputLabel value="Latitude final" for="latitude_final" />
              <input
                type="number"
                step="any"
                class="form-control"
                id="latitude_final"
                v-model="formModuloAmostral.latitude_final"
                :disabled="disabled"
              />
              <InputError :message="formModuloAmostral.errors.latitude_final" />
            </div>
            <div class="col-12 col-md-6">
              <InputLabel value="Longitude final" for="longitude_final" />
              <input
                type="number"
                step="any"
                class="form-control"
                id="longitude_final"
                v-model="formModuloAmostral.longitude_final"
                :disabled="disabled"
              />
              <InputError :message="formModuloAmostral.errors.longitude_final" />
            </div>
          </div>
          <div class="row mb-4">
            <div class="col-12">
              <InputLabel value="Shapefile" for="arquivo" />
              <input
                @input="formModuloAmostral.arquivo = $event.target.files[0]"
                type="file"
                class="form-control"
                id="arquivo"
                accept=".shp,.zip"
                :disabled="disabled"
              />
              <InputError :message="formModuloAmostral.errors.arquivo" />
              <div v-if="formModuloAmostral.arquivo_existente" class="mt-2 text-sm text-gray-600">
                Arquivo existente: {{ formModuloAmostral.arquivo_existente }}
              </div>
            </div>
          </div>
          <div class="row mb-4">
            <div class="col-12">
              <InputLabel value="Observações" for="obs_modulo" />
              <textarea
                id="obs_modulo"
                rows="5"
                class="form-control"
                v-model="formModuloAmostral.obs"
                :disabled="disabled"
              ></textarea>
              <InputError :message="formModuloAmostral.errors.obs" />
            </div>
          </div>
          <div class="row mb-4">
            <div class="col d-flex justify-content-end">
              <NavButton
                type="button"
                type-button="success"
                title="Adicionar Módulo"
                @click="handleAdicionarModulo"
                :disabled="disabled"
              />
            </div>
          </div>
          <div class="table-responsive">
            <Table :columns="['Data', 'Tamanho', 'UF', 'Município', 'Bioma', 'Arquivo', 'Ação']" :records="{ data: moduloRecords, links: [] }">
              <template #body="{ item }">
                <tr>
                  <td>{{ item.data_cadastro || 'N/A' }}</td>
                  <td>{{ item.tamanho_modulo ? `${item.tamanho_modulo}km` : 'N/A' }}</td>
                  <td>{{ item.uf || 'N/A' }}</td>
                  <td>{{ item.municipio || 'N/A' }}</td>
                  <td>{{ item.bioma || 'N/A' }}</td>
                  <td>{{ item.arquivo_existente || 'Nenhum' }}</td>
                  <td class="text-center" style="min-width: 150px;">
                    <NavButton
                      @click="editModulo(item)"
                      type-button="primary"
                      title="Editar"
                      class="me-2"
                      :disabled="disabled"
                    >
                      <i class="bi bi-pencil"></i>
                    </NavButton>
                    <NavButton
                      @click="$emit('excluir-modulo', item.id)"
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
        <div class="d-flex justify-content-between">
          <NavButton type="button" type-button="secondary" title="Voltar" @click="$emit('prev')" />
          <NavButton type="submit" type-button="primary" title="Avançar" />
        </div>
        <h4 class="text-center mt-4 font-weight-bold text-muted">{{ subStep }}/5</h4>
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
.v-select-custom {
  width: 100%;
}
.v-select-custom :deep(.vs__dropdown-toggle) {
  border: 1px solid #ced4da;
  border-radius: 0.25rem;
  padding: 0.375rem 0.75rem;
}
.v-select-custom :deep(.vs__selected) {
  margin: 2px;
}
.v-select-custom:disabled :deep(.vs__dropdown-toggle) {
  background-color: #f5f5f5;
  color: #999;
  cursor: not-allowed;
}
input:disabled,
textarea:disabled,
button:disabled,
.form-check-input:disabled {
  background-color: #f5f5f5;
  color: #999;
  cursor: not-allowed;
}
.table-responsive {
  margin-bottom: 1rem;
}
</style>
