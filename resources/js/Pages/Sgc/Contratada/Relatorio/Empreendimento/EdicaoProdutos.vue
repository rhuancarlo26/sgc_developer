<template>
  <div>
    <Head :title="'Empreendimentos - SUBPRODUTOS: edição'" />
    <AuthenticatedLayout>
      <H3>Módulo de EDIÇÃO</H3>
      <ul class="nav nav-tabs nav-center">
          <li class="nav-item">
            <Link class="nav-link" :href="route('sgc.contratada.edicao')">Empreendimentos</Link>
          </li>
            <li class="nav-item">
              <Link class="nav-link" :href="route('sgc.contratada.edicaoestudos')"> Estudos</Link>
            </li>
          <li class="nav-item">
            <a class="nav-link active"  aria-current="page" href="#"><b>Subprodutos</b></a>
          </li>
      </ul>
      <br>
      <p>
        <a
          class="btn btn-defaut w-full fw-bold fs-underline"
          data-bs-toggle="collapse"
          href="#collapseNovoEmp"
          role="button"
          aria-expanded="false"
          aria-controls="collapseExample"
        >
          Cadastrar Novo Estudo
        </a>
      </p>
      <div class="collapse bg-white" id="collapseNovoEmp">
        <CadastroModal :empreendimentos="camposfixos" @salvar="handleSalvar" />
      </div>
      <p>
        <a
          class="btn btn-defaut w-full fw-bold fs-underline"
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
            <div class="form-check form-switch col-12 mb-2">
                <label class="form-check-label">
                    <input
                    class="form-check-input"
                    type="checkbox"
                    :checked="todosSelecionados"
                    @change="toggleSelecionarTodos"
                    />
                    Marcar/Desmarcar Todos
                </label>
            </div>
            <!-- <hr> -->
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
      <!-- Botões de controle -->
       <div class="row">
            <div class="col-5">
                <button @click="ordenar('id', 'asc')" :class="'mx-2 btn ' + ordemid_ativo" title="Ordem Padrão - ID">
                    🔝 Ordem Padrão
                </button>
                <button @click="ordenar('updated_at', 'desc')" :class="'mx-2 btn ' + ordemup_ativo" title="Ordem GERAL - Últimos Alterados">
                    🔝 Alterados Recentemente
                </button>
            </div>
            <div class="col-5">
                <!-- <button @click="ordenar('cod_emp', ordenamento)" title="" :class="'mx-2 btn ' + ordememp_ativo">
                    Ordenar por Empreendimento
                </button> -->
                <!-- <div class="row">
                    <div class="col-6">
                        <select v-model="ordenarpor" class="form-select w-full mx-2 btn btn-info"  @change="ordenar(ordenarpor, ordenamento)">
                            <option value="cod_siac">Cód. SIAC</option>
                            <option value="produto">Produto</option>
                            <option value="subproduto">subproduto</option>
                            <option value="familia">Família</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <select v-model="ordenamento" class="form-select w-full mx-2 btn" @change="ordenar(ordenarpor, ordenamento)">
                            <option value="asc">Crescente</option>
                            <option value="desc">Decrecente</option>
                        </select>
                    </div>
                </div> -->
            </div>
            <div class="col-2">
                <button
                    @click="exportExcel"
                    class="px-4 py-2 btn btn-success text-white rounded float-end mb-3 mb-5"
                >
                    Exportar Excel <i class="bi bi-file-earmark-excel"></i>
                </button>
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
              <!-- {{ coluna }} -->
              <button class="btn btn-link" @click="ordenar(coluna, (props.ordem === 'asc' ? 'desc' : 'asc'))">{{ coluna }}</button>
              {{ props.coluna === coluna ? (props.ordem === 'asc' ? '⬆️' : '⬇️') : '' }}
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

import { ref, computed, onMounted, watch } from "vue";
import { router, usePage, Link } from "@inertiajs/vue3";

import { Head } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import CadastroModal from './CadastroModal.vue';

const camposocultos = [
  "contrato_id",
  "created_at",
  "updated_at",
  "changelogs",
];
// -------------------------------------------------------------------- reload com ordenamento
const ordemid_ativo = ref('btn-outline-primary');
const ordemup_ativo =  ref('btn-outline-primary');
const ordememp_ativo = ref('btn-outline-primary');
const ordem_importacao = ref('asc');
const ordenarpor = ref('id');
const ordenamento = ref('asc');
// -------------------------------------------------------------------- reload com ordenamento
const ordenar = (campo, ordem = 'asc') => {
  router.get(route('sgc.gestao.edicaoprodutos', { id: 2 }), {
    ordenarPor: campo,
    ordem: ordem,
  }, { preserveState: true, preserveScroll: true });
  switch (campo) {
    case 'id':
      ordemid_ativo.value = 'btn-primary';
      ordememp_ativo.value = 'btn-outline-primary';
      ordemup_ativo.value =  'btn-outline-primary';
      break;
    case 'updated_at':
      ordemid_ativo.value = 'btn-outline-primary';
      ordemup_ativo.value =  'btn-primary';
      ordememp_ativo.value = 'btn-outline-primary';
      break;
  }
  ordem_importacao.value = ordem;
  ordenarpor.value = campo;
};

// -------------------------------------------------------------------- reload com ordenamento

const props = defineProps({
    empreendimentos: Array,
    ordem: String,
    coluna: String
});
const campoEditando = ref({ id: null, campo: null });
const empreendimentoEdit = ref({ id: null, campo: "", valor: "" });

const camposocultos2 = [
  "change_field",
  "old_value",
  "new_value",
  "user_id",
  "change_user_id",
  "change_date",
  "change_field",
  "id",
  "contrato_id",
  "created_at",
  "updated_at",
  "changelogs",
];

const camposfixos = computed(() => {
  return props.empreendimentos.data.map(item => {
    return Object.fromEntries(
      Object.entries(item).filter(
        ([chave]) => !camposocultos2.includes(chave)
      )
    );
  });
});

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
// Atualiza `dados` sempre que os dados da página mudarem
watch(
  () => page.props.empreendimentos.data,
  (novaLista) => {
    dados.value = novaLista;
  }
);
const links = ref(page.props.empreendimentos.links); // Links de paginação

/** espera bem aqui */
// Atualiza os links se necessário
watch(
  () => page.props.empreendimentos.links,
  (novosLinks) => {
    links.value = novosLinks;
  }
);
// Pegando todas as chaves do primeiro objeto como colunas
const todasColunas = Object.keys(dados.value[0] || {});
const mudarPagina = (url) => {
    if (url) {
        router.get(url); // Faz a requisição para a nova página
    }
};


const salvarEdicao = () => {
  router.post(
    route('sgc.contratada.updatecampoprodutos', empreendimentoEdit.value.id),
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
// ------------------------------------------------------------------------- Selecionar Todos

const colunaTravada = 'changelogs'

// 🔍 Computando
const todosSelecionados = computed(() => {
  const colunasFiltradas = todasColunas.filter(
    c => !camposocultos.includes(c) && c !== colunaTravada
  )
  return colunasFiltradas.every(c => colunasVisiveis.value.includes(c))
})

// 🔘 Selecionar
function toggleSelecionarTodos(event) {
  const checked = event.target.checked
  const colunasFiltradas = todasColunas.filter(
    c => !camposocultos.includes(c) && c !== colunaTravada
  )

  const atuais = colunasVisiveis.value.includes(colunaTravada)
    ? ['changelogs']
    : []

  if (checked) {
    colunasVisiveis.value = [...colunasFiltradas, ...atuais]
  } else {
    colunasVisiveis.value = [...atuais]
  }
}
// ------------------------------------------------------------------------- Salvar Novo Estudo
// ------------------------------------------------------------------------- Exportar para Excel
function exportExcel() {
    const camposvalidos = colunasVisiveis.value.filter(coluna => !camposocultos.includes(coluna));
    const params = new URLSearchParams({
        campos: camposvalidos.join(','),
        ordenarpor: ordenarpor.value,
        ordem: ordem_importacao.value,
    })

    const url = `subprodutos-export?${params.toString()}`
    window.location.href = url
}
// ------------------------------------------------------------------------- Exportar para Excel
function handleSalvar(dados) {
    router.post(route('sgc.gestao.cadastrarsubproduto', { id: 2 }), dados);
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
li .active {
    border-bottom: 2px solid #f6f8fb !important;
}
</style>
