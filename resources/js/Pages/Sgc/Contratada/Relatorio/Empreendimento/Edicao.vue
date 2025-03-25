<template>
  <div>
    <Head :title="'Empreendimentos: edição'" />
    <AuthenticatedLayout>
      <p>
        <a
          class="btn btn-primary"
          data-bs-toggle="collapse"
          href="#collapseExample"
          role="button"
          aria-expanded="false"
          aria-controls="collapseExample"
        >
          Selecionar Campos
        </a>
      </p>
      <div class="collapse" id="collapseExample">
        <div class="card card-body">
          <div class="row">
            <div
              class="form-check form-switch col-md-2"
              v-for="coluna in todasColunas"
              :key="coluna"
            >
              <div class="">
                <label class="form-check-label">
                  <input
                    class="form-check-input"
                    type="checkbox"
                    v-model="colunasVisiveis"
                    :value="coluna"
                  />
                  {{ coluna }}
                </label>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="my-3"><hr></div>
      <table
        class="table table-striped table-hover table-light"
      >
        <thead class="table-dark">
          <tr>
            <th class="fw-bolder fs-5"
              v-for="coluna in todasColunas"
              :key="coluna"
              v-show="colunasVisiveis.includes(coluna)"
            >
              {{ coluna }}
            </th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(linha, index) in dadosFiltrados" :key="index">
            <td
              v-for="coluna in todasColunas"
              :key="coluna"
              v-show="colunasVisiveis.includes(coluna)"
            >
              <!-- {{ linha[coluna] }} -->
              <span @click="abrirEdicao(linha, coluna)" class="cursor-pointer">{{ linha[coluna] }}</span>
              <!--
                valor id: {{ linha.id }}
                campo: {{ coluna }}
                valor campo: {{ linha[coluna] }}
              -->
              <div
                v-if="
                  campoEditando.id === linha.id &&
                  campoEditando.campo === coluna
                "
                class="absolute bg-white shadow-lg p-2 border rounded"
              >
                <input
                  v-model="empreendimentoEdit.valor"
                  class="border p-1"
                  @keyup.enter="salvarEdicao"
                  @blur="fecharEdicao"
                />
              </div>
            </td>
          </tr>
        </tbody>
      </table>
      <!--
      <h1 class="text-xl font-bold mb-4">Lista de Empreendimentos</h1>
      <table class="table table-bordered table-striped table-hover table-sm">
        <thead class="thead-dark">
          <tr class="bg-gray-200">
            <th class="border px-4 py-2" scope="row">ID</th>
            <th class="border px-4 py-2">cod_emp</th>
            <th class="border px-4 py-2">tipo_de_intervencao</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="emp in empreendimentos" :key="emp.id">
            <td class="border px-4 py-2 w-min">{{ emp.id }}</td>
            <td class="border px-4 py-2 relative">
              <span @click="abrirEdicao(emp, 'cod_emp')" class="cursor-pointer">{{ emp.cod_emp }}</span>
              <div v-if="campoEditando.id === emp.id && campoEditando.campo === 'cod_emp'" class="absolute bg-white shadow-lg p-2 border rounded">
                <input v-model="empreendimentoEdit.valor" class="border p-1" @keyup.enter="salvarEdicao" @blur="fecharEdicao" />
              </div>
            </td>
            <td class="border px-4 py-2 relative">
              <span @click="abrirEdicao(emp, 'tipo_de_intervencao')" class="cursor-pointer">{{ emp.tipo_de_intervencao }}</span>
              <div v-if="campoEditando.id === emp.id && campoEditando.campo === 'tipo_de_intervencao'" class="absolute bg-white shadow-lg p-2 border rounded">
                <input v-model="empreendimentoEdit.valor" class="border p-1" @keyup.enter="salvarEdicao" @blur="fecharEdicao" />
              </div>
            </td>
          </tr>
        </tbody>
      </table>
      -->
    </AuthenticatedLayout>
  </div>
</template>
<script setup>
import { ref, computed } from "vue";
import { router, usePage } from "@inertiajs/vue3";
import { Head } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

const props = defineProps({ empreendimentos: Array });
const campoEditando = ref({ id: null, campo: null });
const empreendimentoEdit = ref({ id: null, campo: "", valor: "" });

const abrirEdicao = (empreendimento, campo) => {
  empreendimentoEdit.value = {
    id: empreendimento.id,
    campo,
    valor: empreendimento[campo],
  };
  campoEditando.value = { id: empreendimento.id, campo };
};

const salvarEdicao = () => {
  router.post(
    `/sgc/gestao/updatecampo/${empreendimentoEdit.value.id}`,
    { [empreendimentoEdit.value.campo]: empreendimentoEdit.value.valor },
    {
      onSuccess: () => {
        campoEditando.value = { id: null, campo: null };
      },
    }
  );
};

const fecharEdicao = () => {
  campoEditando.value = { id: null, campo: null };
};

// Vamos trazer todas as colunas
const page = usePage();
const dados = ref(page.props.empreendimentos);
// Pegando todas as chaves do primeiro objeto como colunas
// Pegando todas as chaves do primeiro objeto como colunas
const todasColunas = Object.keys(dados.value[0] || {});

// Definir visíveis apenas as 6 primeiras colunas no carregamento
const colunasVisiveis = ref(todasColunas.slice(0, 15));

const dadosFiltrados = computed(() => {
  return dados.value.map((item) => {
    let filtrado = {};
    todasColunas.forEach((coluna) => {
      if (colunasVisiveis.value.includes(coluna)) {
        filtrado[coluna] = item[coluna];
      } else {
        filtrado[coluna] = null; // Mantém a posição original da coluna
      }
    });
    return filtrado;
  });
});
</script>
<style>
.cursor-pointer {
  cursor: pointer;
}
</style>
