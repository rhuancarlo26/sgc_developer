<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import NavButton from '@/Components/NavButton.vue';
import { ref, watch } from 'vue';
import * as XLSX from 'xlsx';

// Props
const props = defineProps({
  form: {
    type: Object,
    required: true,
  },
  resultadosRecords: {
    type: Array,
    default: () => [],
  },
  idCampanha: {
    type: [String, Number],
    required: true,
  },
});

// Emits
const emit = defineEmits(['update:resultadosRecords', 'prev', 'next']);

// Dados
const consideracoes = ref(props.form.consideracoes || '');
const resultadosRecords = ref(props.resultadosRecords);

// Sincronizar considerações com o form pai
watch(consideracoes, (newValue) => {
  props.form.consideracoes = newValue;
});

// Download do modelo de planilha
const downloadModelo = () => {
  const headers = [
    'ID Campanha', 'Módulo', 'Parcela', 'ID Armadilha', 'Grupo Amostrado', 'Data do Registro', 'Hora do Registro',
    'Categoria', 'Classe', 'Ordem', 'Família', 'Gênero', 'Espécie', 'Nome Comum', 'Sexo', 'Faixa Etária',
    'Qnt de Indivíduos', 'Num Marcação', 'Coletado', 'Num de Tombamento', 'Dados Biométricos', 'Comp total',
    'Cabeça', 'Cauda', 'Fêmur', 'Orelha', 'Peso', 'Status Conservação Federal', 'Status Conservação IUCN'
  ];
  const ws = XLSX.utils.json_to_sheet([{}], { header: headers });
  const wb = XLSX.utils.book_new();
  XLSX.utils.book_append_sheet(wb, ws, 'Resultados');
  
  const wbout = XLSX.write(wb, { bookType: 'xlsx', type: 'array' });
  const blob = new Blob([wbout], { type: 'application/octet-stream' });
  const url = window.URL.createObjectURL(blob);
  const link = document.createElement('a');
  link.href = url;
  link.download = 'modelo_resultados.xlsx';
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
  window.URL.revokeObjectURL(url);
};

// Processar upload da planilha
const processarPlanilha = (event) => {
  const file = event.target.files[0];
  if (!file) return;

  const reader = new FileReader();
  reader.onload = (e) => {
    const data = new Uint8Array(e.target.result);
    const workbook = XLSX.read(data, { type: 'array', cellDates: false });
    const sheet = workbook.Sheets[workbook.SheetNames[0]];
    const json = XLSX.utils.sheet_to_json(sheet);

    const novosResultados = json.map(row => ({
      id: null, // ID será gerado pelo backend
      id_campanha: row['ID Campanha'] || props.idCampanha,
      modulo: row['Módulo'] || null,
      parcela: row['Parcela'] || null,
      id_armadilha: row['ID Armadilha'] || null,
      grupo_amostrado: row['Grupo Amostrado'] || '',
      data_registro: row['Data do Registro'] || '',
      hora_registro: row['Hora do Registro'] || null,
      categoria: row['Categoria'] || null,
      classe: row['Classe'] || null,
      ordem: row['Ordem'] || null,
      familia: row['Família'] || null,
      genero: row['Gênero'] || null,
      especie: row['Espécie'] || '',
      nome_comum: row['Nome Comum'] || null,
      sexo: row['Sexo'] || null,
      faixa_etaria: row['Faixa Etária'] || null,
      qnt_individuos: row['Qnt de Indivíduos'] || null,
      num_marcacao: row['Num Marcação'] || null,
      coletado: row['Coletado'] || null,
      num_tombamento: row['Num de Tombamento'] || null,
      dados_biometricos: row['Dados Biométricos'] || null,
      comp_total: row['Comp total'] || null,
      cabeca: row['Cabeça'] || null,
      cauda: row['Cauda'] || null,
      femur: row['Fêmur'] || null,
      orelha: row['Orelha'] || null,
      peso: row['Peso'] || null,
      status_conservacao_federal: row['Status Conservação Federal'] || null,
      status_conservacao_iucn: row['Status Conservação IUCN'] || null,
    }));

    resultadosRecords.value = novosResultados;
    emit('update:resultadosRecords', novosResultados);
    props.form.planilha = file;
    props.form.resultados = novosResultados; // Sincroniza com o form pai
  };
  reader.readAsArrayBuffer(file);
};

// Excluir resultado
const excluirResultado = (id) => {
  const novosResultados = resultadosRecords.value.filter(item => item.id !== id);
  resultadosRecords.value = novosResultados;
  emit('update:resultadosRecords', novosResultados);
  props.form.resultados = novosResultados; // Sincroniza com o form pai
};

// Avançar
const avancar = () => {
  emit('next');
};
</script>

<template>
  <div class="card">
    <div class="card-body">
      <h4 class="mb-3" style="text-align: center;">RESULTADOS</h4>
      
      <!-- Upload da Planilha -->
      <div class="mb-3">
        <button class="btn btn-primary" @click="downloadModelo">Baixar Planilha Modelo</button>
      </div>
      <div class="mb-3">
        <InputLabel for="planilha" value="Upload da Planilha Preenchida" />
        <input
          type="file"
          class="form-control"
          id="planilha"
          accept=".xlsx,.xls"
          @change="processarPlanilha"
        />
        <InputError :message="form.errors.planilha" />
      </div>
      
      <!-- Considerações -->
      <div class="mb-3">
        <InputLabel for="consideracoes" value="CONSIDERAÇÕES" />
        <textarea
          v-model="consideracoes"
          class="form-control"
          id="consideracoes"
          rows="4"
          placeholder="Digite suas considerações aqui..."
        ></textarea>
        <InputError :message="form.errors.consideracoes" />
      </div>
      
      <!-- Tabela de Resultados -->
      <div v-if="resultadosRecords.length" class="overflow-x-auto mb-6">
        <table class="min-w-full bg-white border border-gray-300">
          <thead>
            <tr class="bg-gray-100">
              <th class="py-2 px-4 border-b text-left">ID</th>
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
            <tr v-for="resultado in resultadosRecords" :key="resultado.id || Math.random()" class="hover:bg-gray-50">
              <td class="py-2 px-4 border-b">{{ resultado.id || 'Não informado' }}</td>
              <td class="py-2 px-4 border-b">{{ resultado.modulo || 'Não informado' }}</td>
              <td class="py-2 px-4 border-b">{{ resultado.parcela || 'Não informado' }}</td>
              <td class="py-2 px-4 border-b">{{ resultado.id_armadilha || 'Não informado' }}</td>
              <td class="py-2 px-4 border-b">{{ resultado.grupo_amostrado || 'Não informado' }}</td>
              <td class="py-2 px-4 border-b">{{ resultado.data_registro || 'Não informado' }}</td>
              <td class="py-2 px-4 border-b">{{ resultado.hora_registro || 'Não informado' }}</td>
              <td class="py-2 px-4 border-b">{{ resultado.categoria || 'Não informado' }}</td>
              <td class="py-2 px-4 border-b">{{ resultado.classe || 'Não informado' }}</td>
              <td class="py-2 px-4 border-b">{{ resultado.ordem || 'Não informado' }}</td>
              <td class="py-2 px-4 border-b">{{ resultado.familia || 'Não informado' }}</td>
              <td class="py-2 px-4 border-b">{{ resultado.genero || 'Não informado' }}</td>
              <td class="py-2 px-4 border-b">{{ resultado.especie || 'Não informado' }}</td>
              <td class="py-2 px-4 border-b">{{ resultado.nome_comum || 'Não informado' }}</td>
              <td class="py-2 px-4 border-b">{{ resultado.sexo || 'Não informado' }}</td>
              <td class="py-2 px-4 border-b">{{ resultado.faixa_etaria || 'Não informado' }}</td>
              <td class="py-2 px-4 border-b">{{ resultado.qnt_individuos || 'Não informado' }}</td>
              <td class="py-2 px-4 border-b">{{ resultado.num_marcacao || 'Não informado' }}</td>
              <td class="py-2 px-4 border-b">{{ resultado.coletado || 'Não informado' }}</td>
              <td class="py-2 px-4 border-b">{{ resultado.num_tombamento || 'Não informado' }}</td>
              <td class="py-2 px-4 border-b">{{ resultado.dados_biometricos || 'Não informado' }}</td>
              <td class="py-2 px-4 border-b">{{ resultado.comp_total || 'Não informado' }}</td>
              <td class="py-2 px-4 border-b">{{ resultado.cabeca || 'Não informado' }}</td>
              <td class="py-2 px-4 border-b">{{ resultado.cauda || 'Não informado' }}</td>
              <td class="py-2 px-4 border-b">{{ resultado.femur || 'Não informado' }}</td>
              <td class="py-2 px-4 border-b">{{ resultado.orelha || 'Não informado' }}</td>
              <td class="py-2 px-4 border-b">{{ resultado.peso || 'Não informado' }}</td>
              <td class="py-2 px-4 border-b">{{ resultado.status_conservacao_federal || 'Não informado' }}</td>
              <td class="py-2 px-4 border-b">{{ resultado.status_conservacao_iucn || 'Não informado' }}</td>
              <td class="py-2 px-4 border-b">
                <button class="btn btn-danger btn-sm" @click="excluirResultado(resultado.id)">Excluir</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div v-else class="alert alert-info text-center">
        Nenhum resultado disponível. Faça o upload de uma planilha para visualizar os dados.
      </div>
      
      <!-- Navegação -->
      <div class="d-flex justify-content-between mt-4">
        <NavButton type="button" type-button="secondary" title="Voltar" @click="$emit('prev')" />
        <NavButton type="button" type-button="primary" title="Avançar" @click="avancar" />
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
th, td {
  padding: 0.5rem 1rem;
  border: 1px solid #dee2e6;
}
thead {
  background-color: #f8f9fa;
}
tr:hover {
  background-color: #f1f5f9;
}
</style>