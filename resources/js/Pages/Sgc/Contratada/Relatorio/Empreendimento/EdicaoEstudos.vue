<template>
  <div>
    <Head :title="'Empreendimentos - ESTUDOS: edição'" />
    <AuthenticatedLayout>
      <H3>Módulo de EDIÇÃO</H3>
      <ul class="nav nav-tabs">
          <li class="nav-item">
          <a class="nav-link" href="/sgc/gestao/2/edicao">Empreendimentos</a>
          </li>
          <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="#"><b>Estudos</b></a>
          </li>
          <li class="nav-item">
          <a class="nav-link" href="/sgc/gestao/2/edicao-produtos">Subprodutos</a>
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
       <!-- <button @click="ordenar('id', 'asc')" class="btn" :class="{ 'btn-primary': ordenacao === '', 'btn-outline-primary': ordenacao !== '' }"> -->
       <button @click="ordenar('id', 'asc')" :class="'mx-2 btn ' + ordemid_ativo" title="Ordem Padrão - ID">
            🔝 Ordem Padrão
        </button>

        <!-- <button @click="ordenacao = 'created_at'" class="btn mx-2" :class="{ 'btn-primary': ordenacao === 'created_at', 'btn-outline-primary': ordenacao !== 'created_at' }"> -->
        <!-- <button @click="ordenacao = 'created_at'" class="btn mx-2 btn-primary btn-outline-primary">
            🔝 Mais Recentes: página <strong class="mx-2">{{ paginaAtual }}</strong>
        </button> -->

        <button @click="ordenar('updated_at', 'desc')" :class="'mx-2 btn ' + ordemup_ativo" title="Ordem GERAL - Últimos Alterados">
            🔝 Alterados Recentes
        </button>

        <button @click="ordenar('cod_emp', 'asc')" title="" :class="'mx-2 btn ' + ordememp_ativo">
            🔝 Por Empreendimento
        </button>

        <button
            @click="exportExcel"
            class="px-4 py-2 btn btn-success text-white rounded float-end mb-3 mb-5"
        >
            Exportar Excel <i class="bi bi-file-earmark-excel"></i>
        </button>

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
import { ref, computed, onMounted, watch } from "vue";
import { router, usePage } from "@inertiajs/vue3";
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
// -------------------------------------------------------------------- reload com ordenamento
const ordenar = (campo, ordem = 'asc') => {
  router.get(route('sgc.gestao.edicaoestudos', { id: 2 }), {
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
    case 'cod_emp':
        ordemid_ativo.value = 'btn-outline-primary';
        ordemup_ativo.value =  'btn-outline-primary';
      ordememp_ativo.value = 'btn-primary';
      break;
  }
  ordem_importacao.value = ordem;
  ordenarpor.value = campo;
};

// -------------------------------------------------------------------- reload com ordenamento

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
// const paginaAtual = computed(() => page.props.empreendimentos.current_page);


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

const ordenacao = ref('');

const dadosFiltrados = computed(() => {
  const lista = [...dados.value];

  if (ordenacao.value === 'alterados_cima') {
    lista.sort((a, b) => {
      const aTem = temAlteracao(a.changelogs);
      const bTem = temAlteracao(b.changelogs);

      if (aTem && !bTem) return -1;
      if (!aTem && bTem) return 1;
      return 0;
    });
  }

  if (ordenacao.value === 'created_at') {
    lista.sort((a, b) => {
      const dataA = extrairDataAlteracao(a.changelogs);
      const dataB = extrairDataAlteracao(b.changelogs);

      if (dataA && dataB) return new Date(dataB) - new Date(dataA);
      if (dataA) return -1;
      if (dataB) return 1;
      return 0;
    });
  }

  // Faz o filtro das colunas
  return lista.map((item) => {
    let filtrado = {};
    todasColunas.forEach((coluna) => {
      if (colunasVisiveis.value.includes(coluna)) {
        filtrado[coluna] = item[coluna];
      } else {
        filtrado[coluna] = null;
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
// ------------------------------------------------------------------------- Exportar para Excel
function exportExcel() {
    const camposvalidos = colunasVisiveis.value.filter(coluna => !camposocultos.includes(coluna));
    const params = new URLSearchParams({
        campos: camposvalidos.join(','),
        ordenarpor: ordenarpor.value,
        ordem: ordem_importacao.value,
        // status: 'ativo',
        // cidade: 'Caxias'
    })

    const url = `estudos-export?${params.toString()}`
    window.location.href = url
}
// ------------------------------------------------------------------------- Exportar para Excel
// ------------------------------------------------------------------------- Ordenar Changelogs na frente
function temAlteracao(changelogs) {
  if (!changelogs) return false;

  if (Array.isArray(changelogs)) return changelogs.length > 0;
  if (typeof changelogs === 'object') return Object.keys(changelogs).length > 0;
  if (typeof changelogs === 'string') return changelogs.trim() !== '';

  return false;
}
function extrairDataAlteracao(changelogs) {
  if (!changelogs) return null;

  if (Array.isArray(changelogs)) {
    const datas = changelogs
      .map(c => c.created_at)
      .filter(d => !!d)
      .map(d => new Date(d));

    if (datas.length === 0) return null;

    return new Date(Math.max(...datas.map(d => d.getTime()))); // Mais recente
  }

  if (typeof changelogs === 'object') {
    return changelogs.created_at ? new Date(changelogs.created_at) : null;
  }

  return null;
}
// ------------------------------------------------------------------------- Ordenar Changelogs na frente
// ------------------------------------------------------------------------- Salvar Novo Estudo
function handleSalvar(dados) {
    router.post(route('sgc.gestao.cadastrarestudo', { id: 2 }), dados);
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
