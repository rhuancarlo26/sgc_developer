<template>
  <div class="max-w-5xl mx-auto p-6">
    <!-- <h2 class="text-2xl font-semibold mb-4">Cadastrar Empreendimento</h2> -->

    <!-- Abas -->
    <ul class="nav nav-tabs mb-4">
      <li
        v-for="(aba, index) in abas"
        :key="index"
        @click="abaAtiva = index"
        class="nav-item px-3 py-1"
        :class="{
          'nav-item px-3 py-1 bg-blue-600 bolder': abaAtiva === index,
          'nav-item px-3 py-1 bg-gray-100 text-gray-700': abaAtiva !== index,
        }"
      >
        <span :class="'nav-link' + (abaAtiva === index ? ' active' : '')">{{
          aba.nome
        }}</span>
      </li>
    </ul>

    <!-- Campos da aba ativa -->
    <div class="row">
      <!-- <form> -->
      <div
        v-for="campo in abas[abaAtiva].campos"
        :key="campo"
        class="col-md-5 mb-4 form-row"
      >
        <label class="block text-sm text-gray-600 mb-1 capitalize" style="text-align: end;">
          {{ campo.replaceAll("_", " ") }}
        </label>
        <select
          v-if="camposSelect[campo]"
          v-model="form[campo]"
          class="w-full border rounded-lg px-3 py-2"
          :class="{ 'border-red-500': erros[campo] }"
        >
          <option value="">Selecione...</option>
          <option v-for="opcao in camposSelect[campo]" :key="opcao" :value="opcao">
            {{ opcao }}
          </option>
        </select>
        <input
          v-else
          v-model="form[campo]"
          :type="tipoInput(campo)"
          class="w-full border rounded-lg px-3 py-2 border-bl"
          :class="{ 'border-red-500': erros[campo] }"
        />
        <p v-if="erros[campo]" class="text-sm text-red-500 mt-1">
          {{ erros[campo] }}
        </p>
      </div>
      <!-- </form> -->
    </div>

    <!-- Botões -->
    <div class="mt-6 flex gap-4">
      <button @click="salvar" class="btn btn-success">Salvar</button>
      <button @click="limpar" class="btn btn-secondary mx-1">
        <i class="fas fa-eraser"></i>
        Limpar
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, defineEmits, defineProps } from "vue";

const props = defineProps({
  empreendimentos: {
    type: Array,
    required: true,
  },
});

const emit = defineEmits(["salvar"]);

const todasColunas = Object.keys(props.empreendimentos[0] || {});
const camposPorAba = 10;

const camposSelect = {
  status: ['Ativo', 'Inativo', 'Em Construção'],
  // selects que vem da aba de empreendimentos
  tipo_de_intervencao: ['Implantação/Pavimentação', 'Adequação', 'Duplicação', 'OAE'],
  criticidade_incra: ['Alta', 'Normal'],
  criticidade_icmbio: ['Alta', 'Normal'],
};

const abas = computed(() => {
  const totalAbas = Math.ceil(todasColunas.length / camposPorAba);
  return Array.from({ length: totalAbas }, (_, i) => ({
    nome: `Campos: ${i + 1}`,
    campos: todasColunas.slice(i * camposPorAba, (i + 1) * camposPorAba),
  }));
});

const abaAtiva = ref(0);
const form = ref({});
const erros = ref({});

todasColunas.forEach((coluna) => (form.value[coluna] = ""));

function tipoInput(campo) {
  campo = campo.toLowerCase();
  if (campo.includes("data")) return "date";
  if (
    campo.includes("preco") ||
    campo.includes("valor") ||
    campo.includes("area")
  )
    return "number";
  if (campo.includes("email")) return "email";
  return "text";
}

const camposObrigatorios = todasColunas.slice(0, 10); // ajuste conforme sua regra

function validar() {
  erros.value = {};
  camposObrigatorios.forEach((campo) => {
    if (!form.value[campo]) {
      erros.value[campo] = "Campo obrigatório";
    }
  });
  return Object.keys(erros.value).length === 0;
}

function salvar() {
  if (!validar()) return;
  emit("salvar", { ...form.value });
}

function limpar() {
  todasColunas.forEach((coluna) => (form.value[coluna] = ""));
  erros.value = {};
}
</script>
<style scoped>
li .active {
  border-bottom: 2px solid #f6f8fb !important;
}
.border-bl {
  border-color: #d1d5db !important;
}

.form-container {
  background-color: white;
  padding: 30px;
  border-radius: 12px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
  width: 100%;
  /* max-width: 900px; */
}

h2 {
  margin-bottom: 20px;
  color: #333;
  text-align: center;
}

.form-row {
  display: flex;
  flex-wrap: wrap;
  margin-bottom: 15px;
  align-items: center;
}

.form-row label {
  flex: 1 0 150px;
  margin-right: 10px;
  color: #555;
}

.form-row input,
.form-row select,
.form-row textarea {
  flex: 2 0 300px;
  padding: 8px 12px;
  border: 1px solid #ccc;
  border-radius: 8px;
  transition: border-color 0.3s;
  font-size: 1rem;
}

.form-row input:focus,
.form-row select:focus,
.form-row textarea:focus {
  border-color: #0077ff;
  outline: none;
}

.form-actions {
  text-align: center;
  margin-top: 20px;
}

.form-actions button {
  background-color: #0077ff;
  color: white;
  border: none;
  padding: 10px 20px;
  border-radius: 8px;
  cursor: pointer;
  font-size: 1rem;
  transition: background-color 0.3s;
}

.form-actions button:hover {
  background-color: #005fcc;
}

.capitalize {
  text-transform: capitalize;
}

@media (max-width: 600px) {
  .form-row {
    flex-direction: column;
    align-items: stretch;
  }

  .form-row label {
    margin-bottom: 5px;
  }

  .form-row input,
  .form-row select,
  .form-row textarea {
    flex: 1 0 100%;
  }
}
</style>
<!--
    Campos que contém fórmulas ou cálculos não devem ser editáveis:
    Empreendimentos:
      - extensao
      - lp_avanco
      - li_avanco
-->
