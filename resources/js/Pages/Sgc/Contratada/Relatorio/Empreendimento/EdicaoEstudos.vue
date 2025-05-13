<template>
  <div>
    <Head :title="'Empreendimentos - ESTUDOS: edição'" />
    <AuthenticatedLayout>
      <div style="display: block; float: right;">
        <ul class="nav">
          <li class="nav-item">
            <a class="nav-link" href="/sgc/gestao/2/edicao">Empreendimentos</a>
          </li>
          <li class="nav-item">
            <a class="nav-link disabled" aria-current="page" href="/sgc/gestao/2/edicao-estudos"> >> Estudos</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="/sgc/gestao/2/edicao-produtos"> >> Subprodutos</a>
          </li>
        </ul>
      </div>
      <h3><strong>Empreendimentos - ESTUDOS</strong></h3>
      <H4>[EDIÇÃO]</H4>
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
              v-show="!camposocultos.includes(coluna)"
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
      <div class="modal fade" id="detalhesModal" tabindex="-1" aria-labelledby="detalhesModalLabel" aria-hidden="true" ref="modalRef">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 v-if="registroSelecionado" class="modal-title" id="detalhesModalLabel">Alteração em <b class="text-uppercase">{{ registroSelecionado.nome }}</b></h5>
            <h5 v-else class="modal-title" id="detalhesModalLabel">Alteração no Empreendimento</h5>
            <button type="button" class="btn-close" @click="fecharModal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <!-- MODAL BODY -->
            <div class="modal-body">
              <div v-if="registroSelecionado">
                <p><strong>Nome:</strong> {{ registroSelecionado.nome }}</p>
                <p><strong>Descrição:</strong> {{ registroSelecionado.descricao }}</p>

                <!-- TIMELINE -->
                <div class="mt-4">
                  <h6 class="fw-bold mb-3">Histórico de alterações:</h6>
                  <ul class="timeline">
                    <li v-for="(log, index) in registroSelecionado.changelogs" :key="index" class="mb-4">
                      <div class="d-flex">
                        <div class="me-3">
                          <span class="badge bg-primary rounded-pill text-white">
                            <!-- {{ log.user?.name || 'Usuário desconhecido' }} -->
                            {{ new Date(log.created_at).toLocaleDateString() }}
                          </span>
                        </div>
                        <div>
                          <p class="mb-1">
                            <strong>{{ log.user?.name || 'Usuário desconhecido' }}</strong>
                            alterou <strong>{{ log.field }}</strong>
                          </p>
                          <p class="mb-0">
                            <span class="text-muted">De:</span> {{ log.old_value }} <br>
                            <span class="text-muted">Para:</span> {{ log.new_value }}
                          </p>
                        </div>
                      </div>
                    </li>
                  </ul>
                </div>
              </div>
                <div v-else>
                    cerregando...
                </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" @click="fecharModal">Fechar</button>
          </div>
        </div>
      </div>
      </div>
      </div>
      <table
        class="table table-striped table-hover table-light"
      >
        <thead class="table-dark">
          <tr>
            <th class="fw-bolder fs-5"
              v-for="coluna in todasColunas"
              :key="coluna"
              v-show="colunasVisiveis.includes(coluna) && !camposocultos.includes(coluna)"
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
              v-show="colunasVisiveis.includes(coluna) && !camposocultos.includes(coluna)"
            >
              <!-- {{ linha[coluna] }} -->
              <span
                v-if="campoFoiEditado(linha, coluna)"
                class="badge bg-warning text-white rounded-pill small float-right"
                role="button"
                style="float: right;"
                @click="abrirModal({
                  nome: linha['cod_emp'],
                  changelogs: linha['changelogs'].filter(
                    (log) => log.field === coluna
                  ),
                  coluna: coluna
                })"
              >
                editado
              </span>
              <br v-if="campoFoiEditado(linha, coluna)"/>
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
import { ref, computed, onMounted } from "vue";
import { router, usePage } from "@inertiajs/vue3";
import { Head } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

const camposocultos = [
  "contrato_id",
  "created_at",
  "updated_at",
  "changelogs",
];

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
    `/sgc/gestao/updatecampoestudos/${empreendimentoEdit.value.id}`,
    { [empreendimentoEdit.value.campo]: empreendimentoEdit.value.valor },
    {
      onSuccess: () => {
        campoEditando.value = { id: null, campo: null };
        dados.value = [...page.props.empreendimentos.data];
      },
    }
  );
};

// Definir visíveis apenas as 15 primeiras colunas no carregamento
const colunasVisiveis = ref(todasColunas.slice(0, 10));
colunasVisiveis.value.push(todasColunas[todasColunas.length - 1]);

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
//-------------------------------------------------------------------- 29/09/2023
// Campo foi editado
function campoFoiEditado(linha, campo) {
  return linha.changelogs?.some(change => change.field === campo)
}

//Modal de histórico
// Bootstrap Modal (garante que Bootstrap JS esteja incluído)
let modalInstance = null
const modalRef = ref(null)

const registroSelecionado = ref(null)

function abrirModal(item) {
  registroSelecionado.value = item
  if (modalInstance) modalInstance.show()
}

function fecharModal() {
  if (modalInstance) modalInstance.hide()
}

onMounted(() => {
  const modalEl = modalRef.value
  if (modalEl) {
    // Bootstrap Modal instance
    modalInstance = new bootstrap.Modal(modalEl)
  }
})
</script>
<style>
.cursor-pointer {
  cursor: pointer;
}
</style>
