<template>
  <div>
    <Head :title="'Empreendimentos - SUBPRODUTOS: edição'" />
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
              <span  @click="abrirEdicao(linha, coluna)" :class="'cursor-pointer ' + (linha[coluna] ? '':'text-info')">{{ linha[coluna] ?? 's/info' }}</span>
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
      <!-- Paginação -->
      <div class="pagination">
        <button class="page-link" v-for="link in links" :key="link.label" :disabled="!link.url" @click="mudarPagina(link.url)">
          {{link.label}}
        </button>
      </div>
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

const fecharEdicao = () => {
  campoEditando.value = { id: null, campo: null };
};

// Vamos trazer todas as colunas
const page = usePage();
const dados = ref(page.props.empreendimentos.data);
const links = ref(page.props.empreendimentos.links); // Links de paginação
// Pegando todas as chaves do primeiro objeto como colunas
const todasColunas = Object.keys(dados.value[0] || {});
const mudarPagina = (url) => {
    if (url) {
        router.get(url); // Faz a requisição para a nova página
    }
};


const salvarEdicao = () => {
  router.post(
    `/sgc/gestao/updatecampoprodutos/${empreendimentoEdit.value.id}`,
    { [empreendimentoEdit.value.campo]: empreendimentoEdit.value.valor },
    {
      onSuccess: () => {
        campoEditando.value = { id: null, campo: null };
        dados.value = [...page.props.empreendimentos.data];
      },
    }
  );
};

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
