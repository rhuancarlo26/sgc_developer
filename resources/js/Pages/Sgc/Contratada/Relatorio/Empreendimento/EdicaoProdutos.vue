<template>
  <div>
    <Head :title="'Empreendimentos - SUBPRODUTOS: edição'" />
    <AuthenticatedLayout>
      <div class="content-card">
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
          Cadastrar Novo Subproduto
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
      <div class="mb-4 p-3">
        <div class="row align-items-end">
          <div class="col-md-8">
            <label for="filtroContrato" class="form-label fw-bold">Filtrar por Contrato Ambiental:</label>
            <select id="filtroContrato" v-model="filtroContrato" class="form-select" @change="aplicarFiltro">
              <option value="">-- Todos os Contratos --</option>
              <option v-for="contrato in props.contratosDisponiveis" :key="contrato" :value="contrato">{{ contrato }}</option>
            </select>
          </div>
          <div class="col-md-4"><button v-if="filtroContrato" @click="limparFiltros" class="btn btn-outline-secondary w-100"><i class="bi bi-x-circle me-2"></i>Limpar Filtro</button></div>
        </div>
      </div>
      </div>
      <div class="content-card">

    <div class="modal fade" id="detalhesModal" tabindex="-1" aria-labelledby="detalhesModalLabel" aria-hidden="true" ref="modalRef">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 v-if="registroSelecionado" class="modal-title" id="detalhesModalLabel">Registro: <b class="text-uppercase">{{ registroSelecionado.nome }}</b></h5>
            <h5 v-else class="modal-title" id="detalhesModalLabel">Alteração no Empreendimento</h5>
            <button type="button" class="btn-close" @click="fecharModal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div v-if="registroSelecionado">
              <!-- TIMELINE -->
              <div class="mt-4">
                <h4 class="fw-bold mb-3">Histórico de alterações:</h4>
                <ul class="timeline">
                  <li v-for="(log, index) in registroSelecionado.changelogs" :key="index" class="mb-4">
                    <div class="d-flex">
                      <div class="me-3">
                        <span class="badge bg-primary rounded-pill text-white">
                          {{ formatarData(log.created_at) }}
                        </span>
                      </div>
                      <div class="flex-grow-1">
                        <p class="mb-2">
                          <strong>{{ log.user?.name || 'Usuário desconhecido' }}</strong>
                          alterou <strong>{{ log.field }}</strong>
                        </p>

                        <!-- Separação DE/PARA com visual melhorado -->
                        <div class="row g-2">
                          <div class="col-md-6">
                            <div class="change-box">
                              <label class="change-label">De:</label>
                              <div class="change-value">
                                {{ log.old_value || '(vazio)' }}
                              </div>
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div class="change-box change-box-new">
                              <label class="change-label">Para:</label>
                              <div class="change-value">
                                {{ log.new_value || '(vazio)' }}
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </li>
                </ul>
              </div>
            </div>
            <div v-else>
                Carregando...
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" @click="fecharModal">Fechar</button>
          </div>
        </div>
      </div>
    </div>

      <div class="d-flex justify-content-between align-items-center mb-3 gap-3 flex-wrap">
        <span class="text-muted small">Total de registros: <strong>{{ props.empreendimentos.total }}</strong></span>
        <button @click="exportExcel" class="btn btn-success text-white"><i class="bi bi-file-earmark-excel me-2"></i>Exportar Excel</button>
      </div>
      <div class="table-responsive">
      <table
        class="table table-striped table-hover table-light"
      >
        <thead class="table-dark">
          <tr>
            <th
            v-for="coluna in todasColunas"
            :key="coluna"
            v-show="colunasVisiveis.includes(coluna) && !camposocultos.includes(coluna)"
            class="fw-bolder fs-5 cursor-pointer-header sortable-header"
            @click="ordenarPorColuna(coluna)"
            :class="{
              'header-ativo': colunaOrdenacao === coluna,
              'header-hover': true
            }"
            :title="`Clique para ordenar por ${coluna}`"
          >
            <div class="d-flex align-items-center justify-content-between">
              <span>{{ coluna }}</span>
              <span v-if="colunaOrdenacao === coluna" class="ms-2">
                <i v-if="direcaoOrdenacao === 'asc'" class="bi bi-sort-up text-warning"></i>
                <i v-else class="bi bi-sort-down text-warning"></i>
              </span>
              <span v-else class="ms-2 opacity-50">
                <i class="bi bi-arrow-down-up"></i>
              </span>
            </div>
          </th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="!dadosFiltrados.length"><td :colspan="Math.max(1, colunasVisiveis.filter(coluna => !camposocultos.includes(coluna)).length)" class="text-center text-muted py-4">Nenhum registro encontrado para este contrato.</td></tr>
          <tr v-for="(linha, index) in dadosFiltrados" :key="index">
            <td
              v-for="coluna in todasColunas"
              :key="coluna"
              v-show="colunasVisiveis.includes(coluna) && !camposocultos.includes(coluna)"
            >
              <div class="position-relative">
              <!-- {{ linha[coluna] }} -->
              <span
                v-if="campoFoiEditado(linha, coluna)"
                class="badge bg-warning text-white rounded-pill small float-right"
                role="button"
                style="float: right;"
                @click="abrirModal({
                  nome: linha['cod_emp'] || linha['subproduto'] || linha['cod_siac'] || linha.id,
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
                class="edit-popup"
              >
                <textarea
                  v-model="empreendimentoEdit.valor"
                  class="edit-textarea"
                ></textarea>

                <div class="mt-2 text-end">
                  <button
                    class="btn btn-sm btn-success me-2"
                    @click="salvarEdicao"
                  >
                    Salvar
                  </button>

                  <button
                    class="btn btn-sm btn-secondary"
                    @click="fecharEdicao"
                  >
                    Cancelar
                  </button>
                </div>
              </div>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
      </div>
      <!-- Paginação -->
      <div class="pagination">
        <button class="page-link" v-for="link in links" :key="link.label" :disabled="!link.url || link.active" :class="{ active: link.active }" :aria-current="link.active ? 'page' : undefined" @click="mudarPagina(link.url)">
          <span v-html="link.label"></span>
        </button>
      </div>
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

const props = defineProps({ empreendimentos: Object, contratosDisponiveis: { type: Array, default: () => [] }, filtros: { type: Object, default: () => ({}) } });
const filtroContrato = ref(props.filtros.contrato || '');
const colunaOrdenacao = ref(props.filtros.ordenarPor || '');
const direcaoOrdenacao = ref(props.filtros.ordem || 'asc');
function aplicarFiltro() {
  campoEditando.value = { id: null, campo: null };
  router.get(route('sgc.contratada.edicaoprodutos'), { contrato: filtroContrato.value, ordenarPor: colunaOrdenacao.value, ordem: direcaoOrdenacao.value }, { preserveState: true, preserveScroll: true });
}
function limparFiltros() { filtroContrato.value = ''; aplicarFiltro(); }
function ordenarPorColuna(coluna) {
  if (colunaOrdenacao.value === coluna && direcaoOrdenacao.value === 'desc') { colunaOrdenacao.value = ''; direcaoOrdenacao.value = 'asc'; }
  else if (colunaOrdenacao.value === coluna) direcaoOrdenacao.value = 'desc';
  else { colunaOrdenacao.value = coluna; direcaoOrdenacao.value = 'asc'; }
  aplicarFiltro();
}
watch(() => props.filtros, filtros => { filtroContrato.value = filtros.contrato || ''; colunaOrdenacao.value = filtros.ordenarPor || ''; direcaoOrdenacao.value = filtros.ordem || 'asc'; });
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
  if (campo === 'id') return;
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
        router.get(url, {}, { preserveState: true, preserveScroll: true }); // Faz a requisição para a nova página
    }
};


const salvarEdicao = () => {
  router.post(
    route('sgc.contratada.updatecampoprodutos', empreendimentoEdit.value.id),
    { [empreendimentoEdit.value.campo]: empreendimentoEdit.value.valor },
    {
      preserveScroll: true,
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

const dadosFiltrados = computed(() => dados.value.map(item => {
  const filtrado = Object.fromEntries(todasColunas.map(coluna => [coluna, colunasVisiveis.value.includes(coluna) ? item[coluna] : null]));
  filtrado.id = item.id;
  filtrado.changelogs = item.changelogs;
  return filtrado;
}));//-------------------------------------------------------------------- 29/09/2023
// Campo foi editado
function formatarData(valor) { return valor ? new Date(valor).toLocaleString('pt-BR') : ''; }
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
    colunasVisiveis.value = ['id', ...atuais]
  }
}
// ------------------------------------------------------------------------- Salvar Novo Estudo
// ------------------------------------------------------------------------- Exportar para Excel
function exportExcel() {
    const camposvalidos = colunasVisiveis.value.filter(coluna => !camposocultos.includes(coluna));
    const params = new URLSearchParams({
        campos: camposvalidos.join(','),
        ordenarpor: colunaOrdenacao.value || 'id',
        contrato: filtroContrato.value,
        ordem: direcaoOrdenacao.value,
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
<style scoped>

.cursor-pointer {
  cursor: pointer;
  transition: color 0.2s ease;
}

.cursor-pointer:hover {
  color: #0d6efd;
}

.cursor-pointer-header {
  cursor: pointer;
  user-select: none;
  transition: all 0.2s ease;
}

.sortable-header {
  padding: 1rem 0.75rem !important;
  background-color: #f8f9fa !important;
  border-bottom: 2px solid #0d6efd !important;
  font-weight: 600;
  color: #212529 !important;
}

.sortable-header:hover {
  background-color: #e9ecef !important;
}

.header-ativo {
  background-color: rgba(173, 216, 230, 0.25) !important;
  color: #0d6efd;
  font-weight: 700;
}

.header-hover:hover {
  transform: translateY(-2px);
}

.position-relative {
  position: relative;
}

.d-block {
  display: block;
}

.d-flex {
  display: flex;
}

.mt-1 {
  margin-top: 0.25rem;
}

.align-items-center {
  align-items: center;
}

.justify-content-between {
  justify-content: space-between;
}

.ms-2 {
  margin-left: 0.5rem;
}

.opacity-50 {
  opacity: 0.5;
}

.timeline {
  list-style: none;
  padding-left: 0;
  position: relative;
}

.timeline::before {
  content: '';
  position: absolute;
  left: 12px;
  top: 0;
  bottom: 0;
  width: 2px;
  background: #dee2e6;
}

.timeline li {
  position: relative;
  padding-left: 2rem;
}

.timeline li::before {
  content: '';
  position: absolute;
  left: 6px;
  top: 6px;
  width: 12px;
  height: 12px;
  background-color: #0d6efd;
  border-radius: 50%;
  z-index: 1;
}

.badge {
  cursor: pointer;
  transition: background-color 0.2s ease;
}

.badge:hover {
  opacity: 0.9;
}

.edit-textarea:focus {
  border-color: #0d6efd;
  box-shadow: 0 0 0 2px rgba(13,110,253,0.2);
}

.edit-popup {
  position: absolute;
  z-index: 1000;
  background: white;
  padding: 10px;
  border-radius: 8px;
  border: 1px solid #dee2e6;
  box-shadow: 0 8px 20px rgba(0,0,0,0.15);
}

.edit-textarea {
  width: 670px;
  min-height: 220px;
  resize: vertical;
  padding: 8px;
  border: 1px solid #ced4da;
  border-radius: 6px;
}

/* Modal mais largo */
.modal-dialog.modal-lg {
  max-width: 1200px !important;
}

.modal-dialog.modal-lg .modal-content {
  width: 100%;
}

/* Estilos para boxes DE/PARA no modal */
.change-box {
  padding: 0.75rem;
  background-color: #f8f9fa;
  border-left: 3px solid #dee2e6;
  border-radius: 4px;
}

.change-box-new {
  border-left-color: #0d6efd;
  background-color: rgba(13, 110, 253, 0.05);
}

.change-label {
  display: block;
  font-size: 0.875rem;
  font-weight: 600;
  color: #6c757d;
  margin-bottom: 0.5rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.change-value {
  color: #212529;
  word-break: break-word;
  line-height: 1.5;
}

/* Container Card Branco Padrão */
.content-card {
  background-color: #ffffff;
  border-radius: 8px;
  padding: 2rem;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  margin-bottom: 2rem;
}

/* Campo desabilitado (ID) */
.disabled-field {
  cursor: not-allowed !important;
  opacity: 0.6;
  color: #6c757d !important;
}

/* Header fixo/sticky na tabela */
.table thead {
  position: sticky;
  top: 0;
  z-index: 10;
}

.table thead th {
  position: sticky;
  top: 0;
  z-index: 11;
}

.cursor-pointer {
  cursor: pointer;
}
li .active {
    border-bottom: 2px solid #f6f8fb !important;
}
</style>
