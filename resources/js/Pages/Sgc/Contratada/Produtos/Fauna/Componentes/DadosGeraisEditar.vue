<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import NavButton from '@/Components/NavButton.vue';
import Table from '@/Components/Table.vue';
import vSelect from 'vue-select';
import 'vue-select/dist/vue-select.css';
import { ref, computed } from 'vue';

const props = defineProps({
  formAbio: Object,
  form: {
    type: Object,
    required: true,
  },
  abios: {
      type: Array,
      default: () => []
  },
  profissionais: {
    type: Array,
    default: () => [],
  },
  abioRecords: {
    type: Array,
    default: () => [],
  },
  profissionalRecords: {
    type: Array,
    default: () => [],
  },
   
});
console.log('Props.form:', props.form);
console.log('ABIO RECORDS:', props.abioRecords);

const emit = defineEmits(['vincular-abio', 'excluir-abio', 'salvar-novo-profissional', 'vincular-profissional', 'excluir-profissional', 'next', 'prev']);

const vincularProfissional = () => {
  if (props.form.profissional.id_profissional && props.form.profissional.grupo_faunistico) {
    const prof = props.profissionais.find(p => p.id === props.form.profissional.id_profissional);
    if (prof) {
      const profissionalData = {
        id: prof.id,
        profissional: {
          id: prof.id,
          profissional: prof.profissional || 'N/A',
          formacao: prof.formacao || 'N/A',
        },
        grupo_faunistico: props.form.profissional.grupo_faunistico,
      };
      console.log('Emitindo vincular-profissional:', profissionalData);
      emit('vincular-profissional', profissionalData);
      props.form.profissional.id_profissional = null;
      props.form.profissional.grupo_faunistico = null;
    } else {
      console.error('Profissional não encontrado:', props.form.profissional.id_profissional);
    }
  } else {
    console.error('Campos obrigatórios não preenchidos:', props.form.profissional);
  }
};

const vincularAbio = () => {
  if (props.form.abio.id_abio) {
    const abio = props.abios.find(a => a.id === props.form.abio.id_abio);
    if (abio) {
      const abioData = {
        id: abio.id,
        abio: {
          id: abio.id,
          numero_licenca: abio.numero_licenca || abio.licenca?.numero_licenca || 'N/A',
        },
      };
      console.log('Emitindo vincular-abio:', abioData);
      emit('vincular-abio', abioData);
      props.form.abio.id_abio = null;
    } else {
      console.error('ABIO não encontrado:', props.form.abio.id_abio);
    }
  } else {
    console.error('Campo id_abio não preenchido:', props.form.abio);
  }
};

const showModalProfissional = ref(false);
const formNovoProfissional = ref({
  profissional: '',
  telefone: '',
  cpf: '',
  email: '',
  formacao: '',
  curriculum_lattes: '',
  funcao: '',
  ctf: '',
  validade: '',
  conselho_de_classe: 'Não',
  numero_de_registro: '',
  status: 'Ativo',
  observacao: '',
});

const abioOptions = computed(() => {
  if (!props.abios) {
    console.warn('Props abios está indefinido ou vazio:', props.abios);
    return [];
  }
  return props.abios
    .filter(abio => abio?.licenca)
    .map(abio => ({
      ...abio,
      label: abio.licenca?.numero_licenca || 'Sem Licença',
    }));
});

const profissionalOptions = computed(() => {
  if (!props.profissionais) {
    console.warn('Props profissionais está indefinido ou vazio:', props.profissionais);
    return [];
  }
  return props.profissionais.map(p => ({
    value: p.id, // Usar ID em vez de nome
    label: `${p.profissional} (${p.formacao})`,
  }));
});

const grupoFaunisticoOptions = [
  { value: 'Avifauna', label: 'Avifauna' },
  { value: 'Herpetofauna', label: 'Herpetofauna' },
  { value: 'Mastofauna', label: 'Mastofauna' },
  { value: 'Ictiofauna', label: 'Ictiofauna' },
  { value: 'Bentos', label: 'Bentos' },
];

const conselhoDeClasseOptions = [
  { value: 'Sim', label: 'Sim' },
  { value: 'Não', label: 'Não' },
];

const statusOptions = [
  { value: 'Ativo', label: 'Ativo' },
  { value: 'Inativo', label: 'Inativo' },
];

</script>

<template>
  <form @submit.prevent="$emit('next')">
    <h4 class="mb-3" style="text-align: center;">DADOS GERAIS</h4>
    <div class="mb-4">
      <div class="row mb-3">
        <div class="col-12 col-md-6">
          <InputLabel value="Data Inicial" for="data_campanha_inicial" />
          <input type="date" id="data_campanha_inicial" class="form-control" v-model="form.data_campanha_inicial" />
          <InputError :message="form.errors.data_campanha_inicial" />
        </div>
        <div class="col-12 col-md-6">
          <InputLabel value="Data Final" for="data_campanha_final" />
          <input type="date" id="data_campanha_final" class="form-control" v-model="form.data_campanha_final" />
          <InputError :message="form.errors.data_campanha_final" />
        </div>
      </div>
      <div class="row mb-3">
        <div class="col-12">
          <InputLabel value="Período" for="periodo" />
          <div class="d-flex align-items-center">
            <label class="form-check form-check-inline me-3">
              <input class="form-check-input" type="radio" name="periodo" value="Seca" v-model="form.periodo" />
              <span class="form-check-label">Seca</span>
            </label>
            <label class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="periodo" value="Chuva" v-model="form.periodo" />
              <span class="form-check-label">Chuva</span>
            </label>
          </div>
          <InputError :message="form.errors.periodo" />
        </div>
      </div>
      <div class="row mb-3">
        <div class="col-12">
          <InputLabel value="Observações" for="obs" />
          <textarea id="obs" class="form-control" v-model="form.obs" rows="5"></textarea>
          <InputError :message="form.errors.obs" />
        </div>
      </div>
    </div>

    <!-- Vincular ABIO -->
    <h4 class="mb-3">VINCULAR ABIO</h4>
    <div class="mb-4">
      <div class="row mb-3">
        <div class="col-12 col-md-10">
          <InputLabel value="N° ABIO Vigente" for="id_abio" />
          <v-select
            :options="abioOptions"
            :reduce="a => a.id"
            v-model="form.abio.id_abio"
            placeholder="Selecione um ABIO"
            class="v-select-custom"
          >
            <template #no-options>
              Nenhum registro encontrado.
            </template>
          </v-select>
          <InputError :message="form.errors['abio.id_abio']" />
        </div>
        <div class="col-12 col-md-2 d-flex align-items-end">
          <NavButton type="button" type-button="success" title="Vincular Abio" class="w-100" @click="vincularAbio" />
        </div>
      </div>
      <div class="table-responsive">
      <Table :columns="['ABIOs Vigentes', 'Ação']" :records="{ data: abioRecords, links: [] }">
        <template #body="{ item }">
          <tr>
            <td>{{ item.abio?.numero_licenca || 'N/A' }}</td>
            <td class="text-center" style="min-width: 100px;">
              <NavButton @click="$emit('excluir-abio', item.id)" type-button="danger" title="Excluir">
                <i class="bi bi-trash"></i>
              </NavButton>
            </td>
          </tr>
        </template>
      </Table>
    </div>
    </div>

    <!-- Vincular Profissionais -->
    <h4 class="mb-3">VINCULAR PROFISSIONAIS</h4>
    <div class="mb-4">
      <div class="row mb-3">
        <div class="col-12 col-md-5">
          <InputLabel value="Selecionar Profissional" for="profissional" />
        <v-select
            v-model="form.profissional.id_profissional"
            :options="profissionalOptions"
            :reduce="p => p.value"
            placeholder="Selecione um profissional"
            class="v-select-custom"
        />
        <InputError :message="form.errors['profissional.id_profissional']" />
        </div>
        <div class="col-12 col-md-5">
          <InputLabel value="Grupo Responsável" for="grupo_faunistico" />
          <v-select
            v-model="form.profissional.grupo_faunistico"
            :options="grupoFaunisticoOptions"
            :reduce="g => g.value"
            placeholder="Selecione um grupo"
            class="v-select-custom"
          />
          <InputError :message="form.errors['profissional.grupo_faunistico']" />
        </div>
        <!-- <div class="col-12 col-md-2 d-flex align-items-end">
          <NavButton type="button" type-button="success" title="Vincular" class="w-100" @click="$emit('vincular-profissional')" />
        </div> -->
        <div class="col-12 col-md-2 d-flex align-items-end">
          <NavButton type="button" type-button="success" title="Vincular" class="w-100" @click="vincularProfissional" />
        </div>
      </div>
      <div class="row mb-3">
        <div class="col-12">
          <NavButton type="button" type-button="primary" title="Cadastrar Profissional" @click="showModalProfissional = true" />
        </div>
      </div>
      <div class="table-responsive">
        <Table :columns="['Equipe Técnica', 'Formação', 'Grupo Responsável', 'Ação']" :records="{ data: profissionalRecords, links: [] }">
          <template #body="{ item }">
            <tr>
                <td>{{ item.profissional.profissional || 'N/A' }}</td>
                <td>{{ item.profissional.formacao || 'N/A' }}</td>
                <td>{{ item.grupo_faunistico || 'N/A' }}</td>
                <td class="text-center" style="min-width: 100px;">
                    <NavButton @click="$emit('excluir-profissional', item.id)" type-button="danger" title="Excluir">
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

    <!-- Modal para Cadastrar Profissional -->
    <div v-if="showModalProfissional" class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Cadastrar Profissional</h5>
            <button type="button" class="btn-close" @click="showModalProfissional = false"></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="$emit('salvar-novo-profissional', formNovoProfissional)">
              <div class="row mb-3">
                <div class="col-12 col-md-6">
                  <InputLabel value="Nome do Profissional" for="profissional" />
                  <input v-model="formNovoProfissional.profissional" type="text" class="form-control" id="profissional" required />
                  <InputError :message="formNovoProfissional.errors?.profissional" />
                </div>
                <div class="col-12 col-md-6">
                  <InputLabel value="Telefone" for="telefone" />
                  <input v-model="formNovoProfissional.telefone" type="text" class="form-control" id="telefone" />
                  <InputError :message="formNovoProfissional.errors?.telefone" />
                </div>
              </div>
              <div class="row mb-3">
                <div class="col-12 col-md-6">
                  <InputLabel value="CPF" for="cpf" />
                  <input v-model="formNovoProfissional.cpf" type="text" class="form-control" id="cpf" />
                  <InputError :message="formNovoProfissional.errors?.cpf" />
                </div>
                <div class="col-12 col-md-6">
                  <InputLabel value="E-mail" for="email" />
                  <input v-model="formNovoProfissional.email" type="email" class="form-control" id="email" />
                  <InputError :message="formNovoProfissional.errors?.email" />
                </div>
              </div>
              <div class="row mb-3">
                <div class="col-12 col-md-6">
                  <InputLabel value="Formação" for="formacao" />
                  <input v-model="formNovoProfissional.formacao" type="text" class="form-control" id="formacao" required />
                  <InputError :message="formNovoProfissional.errors?.formacao" />
                </div>
                <div class="col-12 col-md-6">
                  <InputLabel value="Curriculum Lattes" for="curriculum_lattes" />
                  <input v-model="formNovoProfissional.curriculum_lattes" type="text" class="form-control" id="curriculum_lattes" />
                  <InputError :message="formNovoProfissional.errors?.curriculum_lattes" />
                </div>
              </div>
              <div class="row mb-3">
                <div class="col-12 col-md-6">
                  <InputLabel value="Função" for="funcao" />
                  <input v-model="formNovoProfissional.funcao" type="text" class="form-control" id="funcao" />
                  <InputError :message="formNovoProfissional.errors?.funcao" />
                </div>
                <div class="col-12 col-md-6">
                  <InputLabel value="CTF" for="ctf" />
                  <input v-model="formNovoProfissional.ctf" type="text" class="form-control" id="ctf" />
                  <InputError :message="formNovoProfissional.errors?.ctf" />
                </div>
              </div>
              <div class="row mb-3">
                <div class="col-12 col-md-6">
                  <InputLabel value="Validade" for="validade" />
                  <input v-model="formNovoProfissional.validade" type="date" class="form-control" id="validade" />
                  <InputError :message="formNovoProfissional.errors?.validade" />
                </div>
                <div class="col-12 col-md-6">
                  <InputLabel value="Conselho de Classe" for="conselho_de_classe" />
                  <div class="d-flex align-items-center">
                    <label class="form-check form-check-inline me-3">
                      <input
                        class="form-check-input"
                        type="radio"
                        name="conselho_de_classe"
                        value="Sim"
                        v-model="formNovoProfissional.conselho_de_classe"
                      />
                      <span class="form-check-label">Sim</span>
                    </label>
                    <label class="form-check form-check-inline">
                      <input
                        class="form-check-input"
                        type="radio"
                        name="conselho_de_classe"
                        value="Não"
                        v-model="formNovoProfissional.conselho_de_classe"
                      />
                      <span class="form-check-label">Não</span>
                    </label>
                  </div>
                  <InputError :message="formNovoProfissional.errors?.conselho_de_classe" />
                </div>
              </div>
              <div class="row mb-3" v-if="formNovoProfissional.conselho_de_classe === 'Sim'">
                <div class="col-12">
                  <InputLabel value="Número de Registro" for="numero_de_registro" />
                  <input v-model="formNovoProfissional.numero_de_registro" type="number" class="form-control" id="numero_de_registro" />
                  <InputError :message="formNovoProfissional.errors?.numero_de_registro" />
                </div>
              </div>
              <div class="row mb-3">
                <div class="col-12 col-md-6">
                  <InputLabel value="Status" for="status" />
                  <select v-model="formNovoProfissional.status" class="form-select" id="status">
                    <option v-for="option in statusOptions" :value="option.value" :key="option.value">{{ option.label }}</option>
                  </select>
                  <InputError :message="formNovoProfissional.errors?.status" />
                </div>
                <div class="col-12 col-md-6">
                  <InputLabel value="Observação" for="observacao" />
                  <textarea v-model="formNovoProfissional.observacao" class="form-control" id="observacao" rows="3"></textarea>
                  <InputError :message="formNovoProfissional.errors?.observacao" />
                </div>
              </div>
              <div class="d-flex justify-content-end">
                <NavButton type="button" type-button="secondary" title="Cancelar" @click="showModalProfissional = false" class="me-2" />
                <NavButton type="submit" type-button="primary" title="Salvar" />
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </form>
</template>

<style scoped>
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
.table-responsive {
  margin-bottom: 1rem;
}
.modal-dialog.modal-lg {
  max-width: 1200px;
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
</style>