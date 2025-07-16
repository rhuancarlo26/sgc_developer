<script setup>
import { defineProps, defineEmits } from 'vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import NavButton from '@/Components/NavButton.vue';

defineProps({
  form: {
    type: Object,
    required: true,
  },
  resultadosRecords: {
    type: Array,
    default: () => [],
  },
});

defineEmits(['next', 'prev']);

const addResultado = () => {
  form.resultados.push({
    modulo: '',
    parcela: '',
    id_armadilha: '',
    grupo_amostrado: '',
    data_registro: '',
    hora_registro: '',
    categoria: '',
    classe: '',
    ordem: '',
    familia: '',
    genero: '',
    especie: '',
    nome_comum: '',
    sexo: '',
    faixa_etaria: '',
    qnt_individuos: null,
    num_marcacao: '',
    coletado: '',
    num_tombamento: '',
    dados_biometricos: '',
    comp_total: null,
    cabeca: null,
    cauda: null,
    femur: null,
    orelha: null,
    peso: null,
    status_conservacao_federal: '',
    status_conservacao_iucn: '',
  });
};

const removeResultado = (index) => {
  form.resultados.splice(index, 1);
};
</script>

<template>
  <div class="card">
    <div class="card-body">
      <h4 class="mb-3" style="text-align: center;">RESULTADOS</h4>

      <div v-if="resultadosRecords.length" class="overflow-x-auto mb-6">
        <table class="min-w-full bg-white border border-gray-300">
          <thead>
            <tr class="bg-gray-100">
              <th class="py-2 px-4 border-b text-left">Módulo</th>
              <th class="py-2 px-4 border-b text-left">Parcela</th>
              <th class="py-2 px-4 border-b text-left">ID Armadilha</th>
              <th class="py-2 px-4 border-b text-left">Grupo Amostrado</th>
              <th class="py-2 px-4 border-b text-left">Data Registro</th>
              <th class="py-2 px-4 border-b text-left">Hora Registro</th>
              <th class="py-2 px-4 border-b text-left">Categoria</th>
              <th class="py-2 px-4 border-b text-left">Classe</th>
              <th class="py-2 px-4 border-b text-left">Ordem</th>
              <th class="py-2 px-4 border-b text-left">Família</th>
              <th class="py-2 px-4 border-b text-left">Gênero</th>
              <th class="py-2 px-4 border-b text-left">Espécie</th>
              <th class="py-2 px-4 border-b text-left">Nome Comum</th>
              <th class="py-2 px-4 border-b text-left">Sexo</th>
              <th class="py-2 px-4 border-b text-left">Faixa Etária</th>
              <th class="py-2 px-4 border-b text-left">Quantidade</th>
              <th class="py-2 px-4 border-b text-left">Nº Marcação</th>
              <th class="py-2 px-4 border-b text-left">Coletado</th>
              <th class="py-2 px-4 border-b text-left">Nº Tombamento</th>
              <th class="py-2 px-4 border-b text-left">Dados Biométricos</th>
              <th class="py-2 px-4 border-b text-left">Comp. Total (cm)</th>
              <th class="py-2 px-4 border-b text-left">Cabeça (cm)</th>
              <th class="py-2 px-4 border-b text-left">Cauda (cm)</th>
              <th class="py-2 px-4 border-b text-left">Fêmur (cm)</th>
              <th class="py-2 px-4 border-b text-left">Orelha (cm)</th>
              <th class="py-2 px-4 border-b text-left">Peso (g)</th>
              <th class="py-2 px-4 border-b text-left">Status Federal</th>
              <th class="py-2 px-4 border-b text-left">Status IUCN</th>
              <th class="py-2 px-4 border-b text-left">Ações</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(resultado, index) in resultadosRecords" :key="index" class="hover:bg-gray-50">
              <td class="py-2 px-4 border-b">
                <input v-model="form.resultados[index].modulo" type="text" class="form-control" />
                <InputError :message="form.errors[`resultados.${index}.modulo`]" />
              </td>
              <td class="py-2 px-4 border-b">
                <input v-model="form.resultados[index].parcela" type="text" class="form-control" />
                <InputError :message="form.errors[`resultados.${index}.parcela`]" />
              </td>
              <td class="py-2 px-4 border-b">
                <input v-model="form.resultados[index].id_armadilha" type="text" class="form-control" />
                <InputError :message="form.errors[`resultados.${index}.id_armadilha`]" />
              </td>
              <td class="py-2 px-4 border-b">
                <input v-model="form.resultados[index].grupo_amostrado" type="text" class="form-control" />
                <InputError :message="form.errors[`resultados.${index}.grupo_amostrado`]" />
              </td>
              <td class="py-2 px-4 border-b">
                <input v-model="form.resultados[index].data_registro" type="date" class="form-control" />
                <InputError :message="form.errors[`resultados.${index}.data_registro`]" />
              </td>
              <td class="py-2 px-4 border-b">
                <input v-model="form.resultados[index].hora_registro" type="time" class="form-control" />
                <InputError :message="form.errors[`resultados.${index}.hora_registro`]" />
              </td>
              <td class="py-2 px-4 border-b">
                <input v-model="form.resultados[index].categoria" type="text" class="form-control" />
                <InputError :message="form.errors[`resultados.${index}.categoria`]" />
              </td>
              <td class="py-2 px-4 border-b">
                <input v-model="form.resultados[index].classe" type="text" class="form-control" />
                <InputError :message="form.errors[`resultados.${index}.classe`]" />
              </td>
              <td class="py-2 px-4 border-b">
                <input v-model="form.resultados[index].ordem" type="text" class="form-control" />
                <InputError :message="form.errors[`resultados.${index}.ordem`]" />
              </td>
              <td class="py-2 px-4 border-b">
                <input v-model="form.resultados[index].familia" type="text" class="form-control" />
                <InputError :message="form.errors[`resultados.${index}.familia`]" />
              </td>
              <td class="py-2 px-4 border-b">
                <input v-model="form.resultados[index].genero" type="text" class="form-control" />
                <InputError :message="form.errors[`resultados.${index}.genero`]" />
              </td>
              <td class="py-2 px-4 border-b">
                <input v-model="form.resultados[index].especie" type="text" class="form-control" />
                <InputError :message="form.errors[`resultados.${index}.especie`]" />
              </td>
              <td class="py-2 px-4 border-b">
                <input v-model="form.resultados[index].nome_comum" type="text" class="form-control" />
                <InputError :message="form.errors[`resultados.${index}.nome_comum`]" />
              </td>
              <td class="py-2 px-4 border-b">
                <input v-model="form.resultados[index].sexo" type="text" class="form-control" />
                <InputError :message="form.errors[`resultados.${index}.sexo`]" />
              </td>
              <td class="py-2 px-4 border-b">
                <input v-model="form.resultados[index].faixa_etaria" type="text" class="form-control" />
                <InputError :message="form.errors[`resultados.${index}.faixa_etaria`]" />
              </td>
              <td class="py-2 px-4 border-b">
                <input v-model="form.resultados[index].qnt_individuos" type="number" class="form-control" />
                <InputError :message="form.errors[`resultados.${index}.qnt_individuos`]" />
              </td>
              <td class="py-2 px-4 border-b">
                <input v-model="form.resultados[index].num_marcacao" type="text" class="form-control" />
                <InputError :message="form.errors[`resultados.${index}.num_marcacao`]" />
              </td>
              <td class="py-2 px-4 border-b">
                <input v-model="form.resultados[index].coletado" type="text" class="form-control" />
                <InputError :message="form.errors[`resultados.${index}.coletado`]" />
              </td>
              <td class="py-2 px-4 border-b">
                <input v-model="form.resultados[index].num_tombamento" type="text" class="form-control" />
                <InputError :message="form.errors[`resultados.${index}.num_tombamento`]" />
              </td>
              <td class="py-2 px-4 border-b">
                <input v-model="form.resultados[index].dados_biometricos" type="text" class="form-control" />
                <InputError :message="form.errors[`resultados.${index}.dados_biometricos`]" />
              </td>
              <td class="py-2 px-4 border-b">
                <input v-model="form.resultados[index].comp_total" type="number" step="any" class="form-control" />
                <InputError :message="form.errors[`resultados.${index}.comp_total`]" />
              </td>
              <td class="py-2 px-4 border-b">
                <input v-model="form.resultados[index].cabeca" type="number" step="any" class="form-control" />
                <InputError :message="form.errors[`resultados.${index}.cabeca`]" />
              </td>
              <td class="py-2 px-4 border-b">
                <input v-model="form.resultados[index].cauda" type="number" step="any" class="form-control" />
                <InputError :message="form.errors[`resultados.${index}.cauda`]" />
              </td>
              <td class="py-2 px-4 border-b">
                <input v-model="form.resultados[index].femur" type="number" step="any" class="form-control" />
                <InputError :message="form.errors[`resultados.${index}.femur`]" />
              </td>
              <td class="py-2 px-4 border-b">
                <input v-model="form.resultados[index].orelha" type="number" step="any" class="form-control" />
                <InputError :message="form.errors[`resultados.${index}.orelha`]" />
              </td>
              <td class="py-2 px-4 border-b">
                <input v-model="form.resultados[index].peso" type="number" step="any" class="form-control" />
                <InputError :message="form.errors[`resultados.${index}.peso`]" />
              </td>
              <td class="py-2 px-4 border-b">
                <input v-model="form.resultados[index].status_conservacao_federal" type="text" class="form-control" />
                <InputError :message="form.errors[`resultados.${index}.status_conservacao_federal`]" />
              </td>
              <td class="py-2 px-4 border-b">
                <input v-model="form.resultados[index].status_conservacao_iucn" type="text" class="form-control" />
                <InputError :message="form.errors[`resultados.${index}.status_conservacao_iucn`]" />
              </td>
              <td class="py-2 px-4 border-b">
                <button type="button" class="btn btn-danger btn-sm" @click="removeResultado(index)">Remover</button>
              </td>
            </tr>
          </tbody>
        </table>
        <button type="button" class="btn btn-primary btn-sm mt-2" @click="addResultado">Adicionar Resultado</button>
      </div>
      <div v-else class="alert alert-info text-center">
        Nenhum resultado disponível. Clique em "Adicionar Resultado" para começar.
      </div>

      <div class="mb-6">
        <InputLabel value="CONSIDERAÇÕES" for="consideracoes" />
        <textarea v-model="form.consideracoes" class="form-control" id="consideracoes" rows="4"></textarea>
        <InputError :message="form.errors.consideracoes" />
      </div>

      <div class="mb-6">
        <InputLabel value="PLANILHA" for="planilha" />
        <input type="file" @change="form.planilha = $event.target.files[0] || null" accept=".xlsx,.xls,.csv" class="form-control" id="planilha" />
        <InputError :message="form.errors.planilha" />
      </div>

      <div class="d-flex justify-content-between mt-4">
        <NavButton type="button" type-button="secondary" title="Voltar" @click="$emit('prev')" />
        <NavButton type="button" type-button="primary" title="Avançar" @click="$emit('next')" />
      </div>
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
table {
  border-collapse: collapse;
}
th,
td {
  padding: 0.5rem 1rem;
  border: 1px solid #dee2e6;
}
thead {
  background-color: #f8f9fa;
}
tr:hover {
  background-color: #f1f5f9;
}
.btn-sm {
  padding: 0.25rem 0.5rem;
  font-size: 0.875rem;
}
</style>