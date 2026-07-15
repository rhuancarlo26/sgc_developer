<template>
  <div>
    <Head :title="'Empreendimentos: edição'" />
    <AuthenticatedLayout>

      <div class="content-card">
        <H3>Módulo de EDIÇÃO</H3>
        <ul class="nav nav-tabs">
            <li class="nav-item">
            <a class="nav-link active" aria-current="page" href="#"><b>Empreendimentos</b></a>
            </li>
            <li class="nav-item">
              <Link class="nav-link" :href="route('sgc.contratada.edicaoestudos')"> Estudos</Link>
            </li>
            <li class="nav-item">
              <Link class="nav-link" :href="route('sgc.contratada.edicaoprodutos')"> Subprodutos</Link>
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
          Cadastrar Novo Empreendimento
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
            <hr>
            <div
              class="form-check form-switch col-md-2"
              v-for="coluna in todasColunas"
              :key="coluna"
              v-show="!camposocultos.includes(coluna) && coluna !== 'id' && coluna !== 'cod_emp'"
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

      <!-- Filtro de Contrato -->
      <div class="mb-4 p-3">
        <div class="row align-items-end">
          <div class="col-md-8">
            <label for="filtroContrato" class="form-label fw-bold">Filtrar por Contrato Ambiental:</label>
            <select
              id="filtroContrato"
              v-model="filtroContrato"
              class="form-select"
              @change="aplicarFiltro"
            >
              <option value="">-- Todos os Contratos --</option>
              <option v-for="contrato in contratosDisponiveis" :key="contrato" :value="contrato">
                {{ contrato }}
              </option>
            </select>
          </div>
          <div class="col-md-4">
            <button
              v-if="filtroContrato"
              @click="limparFiltros"
              class="btn btn-outline-secondary w-100"
            >
              <i class="bi bi-x-circle me-2"></i>Limpar Filtro
            </button>
          </div>
        </div>
      </div>
      </div>

      <!-- Card da Tabela -->
      <div class="content-card">

    <!-- Modal de Histórico de Alterações -->
    <div class="modal fade" id="detalhesModal" tabindex="-1" aria-labelledby="detalhesModalLabel" aria-hidden="true" ref="modalRef">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 v-if="registroSelecionado" class="modal-title" id="detalhesModalLabel">Empreendimento: <b class="text-uppercase">{{ registroSelecionado.nome }}</b></h5>
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

    <!-- Barra de Ações -->
    <div class="d-flex justify-content-between align-items-center mb-3">
      <div>
        <span class="text-muted small">
          Total de registros: <strong>{{ dadosFiltrados.length }}</strong>
        </span>
      </div>
      <button
        @click="exportExcel"
        class="btn btn-success text-white"
      >
        <i class="bi bi-file-earmark-excel me-2"></i>Exportar Excel
      </button>
    </div>

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
        <tr v-for="(linha, index) in dadosFiltrados" :key="index">
          <td
            v-for="coluna in todasColunas"
            :key="coluna"
            v-show="colunasVisiveis.includes(coluna) && !camposocultos.includes(coluna)"
          >
            <div class="position-relative">
              <!-- Badge "editado" com data da última alteração -->
              <span
                v-if="campoFoiEditado(linha, coluna)"
                class="badge bg-warning text-white rounded-pill small"
                role="button"
                @click="abrirModal({
                  nome: linha['cod_emp'],
                  changelogs: linha['changelogs'].filter(
                    (log) => log.field === coluna
                  ),
                  coluna: coluna
                })"
                :title="`Clique para ver o histórico de alterações`"
              >
                editado {{ formatarDataCurta(obterDataUltimaAlteracao(linha, coluna)) }}
              </span>

              <!-- Valor do campo com ação de edição -->
              <span
                v-if="campoFoiEditado(linha, coluna)"
                @click="coluna !== 'id' ? abrirEdicao(linha, coluna) : null"
                :class="'cursor-pointer d-block mt-1 ' + (linha[coluna] ? '':'text-info') + (coluna === 'id' ? ' disabled-field' : '')"
              >
                {{ linha[coluna] ?? 's/info' }}
              </span>
              <span
                v-else
                @click="coluna !== 'id' ? abrirEdicao(linha, coluna) : null"
                :class="'cursor-pointer ' + (linha[coluna] ? '':'text-info') + (coluna === 'id' ? ' disabled-field' : '')"
              >
                {{ linha[coluna] ?? 's/info' }}
              </span>

              <!-- Modal de edição inline -->
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
    </AuthenticatedLayout>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { router, usePage, Link } from "@inertiajs/vue3";
import { Head } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import CadastroModal from './CadastroModal.vue';

import NavLink from '@/Components/NavLink.vue';

const props = defineProps({ empreendimentos: Array });
const campoEditando = ref({ id: null, campo: null });
const empreendimentoEdit = ref({ id: null, campo: "", valor: "" });

const camposocultos = [
  "change_field",
  "old_value",
  "new_value",
  "user_id",
  "change_user_id",
  "change_date",
  "change_field",
  "contrato_id",
  "created_at",
  "updated_at",
  "changelogs",
];

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
  return props.empreendimentos.map(item => {
    return Object.fromEntries(
      Object.entries(item).filter(
        ([chave]) => !camposocultos2.includes(chave)
      )
    );
  });
});

// ========================================
// FUNÇÕES DE FORMATAÇÃO DE DATA
// ========================================

/**
 * Formata a data completa no padrão brasileiro
 * @param {string|Date} data - Data a ser formatada
 * @returns {string} Data formatada (DD/MM/YYYY HH:mm)
 */
function formatarData(data) {
  if (!data) return '';

  const dataObj = new Date(data);
  const dia = String(dataObj.getDate()).padStart(2, '0');
  const mes = String(dataObj.getMonth() + 1).padStart(2, '0');
  const ano = dataObj.getFullYear();
  const horas = String(dataObj.getHours()).padStart(2, '0');
  const minutos = String(dataObj.getMinutes()).padStart(2, '0');

  return `${dia}/${mes}/${ano} ${horas}:${minutos}`;
}

/**
 * Formata a data de forma curta/resumida
 * @param {string|Date} data - Data a ser formatada
 * @returns {string} Data formatada (DD/MM/YY ou "hoje" ou "ontem")
 */
function formatarDataCurta(data) {
  if (!data) return '';

  const dataObj = new Date(data);
  const hoje = new Date();
  const ontem = new Date(hoje);
  ontem.setDate(ontem.getDate() - 1);

  // Compara apenas as datas (sem hora)
  const ehHoje = dataObj.toDateString() === hoje.toDateString();
  const ehOntem = dataObj.toDateString() === ontem.toDateString();

  if (ehHoje) {
    const horas = String(dataObj.getHours()).padStart(2, '0');
    const minutos = String(dataObj.getMinutes()).padStart(2, '0');
    return `hoje ${horas}:${minutos}`;
  }

  if (ehOntem) {
    return 'ontem';
  }

  // Formato padrão DD/MM/YY
  const dia = String(dataObj.getDate()).padStart(2, '0');
  const mes = String(dataObj.getMonth() + 1).padStart(2, '0');
  const ano = String(dataObj.getFullYear()).slice(-2);

  return `${dia}/${mes}/${ano}`;
}

/**
 * Obtém a data da última alteração de um campo específico
 * @param {object} linha - Objeto com os dados da linha
 * @param {string} coluna - Nome do campo
 * @returns {string|null} Data mais recente de alteração ou null
 */
function obterDataUltimaAlteracao(linha, coluna) {
  if (!linha.changelogs || !Array.isArray(linha.changelogs)) {
    return null;
  }

  const alteracoesDosCampo = linha.changelogs.filter(log => log.field === coluna);

  if (alteracoesDosCampo.length === 0) {
    return null;
  }

  // Encontra a data mais recente
  const dataMaisRecente = alteracoesDosCampo.reduce((mais_recente, log) => {
    const dataAtual = new Date(log.created_at);
    const dataMais = new Date(mais_recente.created_at);
    return dataAtual > dataMais ? log : mais_recente;
  });

  return dataMaisRecente.created_at;
}

// ========================================
// EDIÇÃO INLINE
// ========================================

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
    route('sgc.contratada.updatecampo', empreendimentoEdit.value.id),
    { [empreendimentoEdit.value.campo]: empreendimentoEdit.value.valor },
    {
      preserveScroll: true,
      onSuccess: () => {
        campoEditando.value = { id: null, campo: null };
        dados.value = [...page.props.empreendimentos];
      },
    }
  );
};

const fecharEdicao = () => {
  campoEditando.value = { id: null, campo: null };
};

// ========================================
// DADOS E COLUNAS
// ========================================

const page = usePage();
const dados = ref(page.props.empreendimentos);
const todasColunas = Object.keys(dados.value[0] || {});

// Visíveis apenas as 15 primeiras colunas no carregamento
const colunasVisiveis = ref(todasColunas.slice(0, 15));
colunasVisiveis.value.push(todasColunas[todasColunas.length - 1]);

// Garantir que ID e COD_EMP sempre estejam visíveis
if (!colunasVisiveis.value.includes('id')) {
  colunasVisiveis.value.unshift('id');
}
if (!colunasVisiveis.value.includes('cod_emp')) {
  const indexId = colunasVisiveis.value.indexOf('id');
  colunasVisiveis.value.splice(indexId + 1, 0, 'cod_emp');
}

// ========================================
// FILTRO DE CONTRATO
// ========================================

const filtroContrato = ref('');

const contratosDisponiveis = computed(() => {
  const contratos = new Set();
  dados.value.forEach(item => {
    if (item.contrato_est_ambiental) {
      contratos.add(item.contrato_est_ambiental);
    }
  });
  return Array.from(contratos).sort();
});

function aplicarFiltro() {
  // A filtragem é feita no computed dadosFiltrados
}

function limparFiltros() {
  filtroContrato.value = '';
}

// ========================================
// ORDENAÇÃO
// ========================================

const colunaOrdenacao = ref('');
const direcaoOrdenacao = ref('asc');

/**
 * Ordena a tabela por coluna
 * Click alternado: asc → desc → sem ordenação
 */
function ordenarPorColuna(coluna) {
  if (colunaOrdenacao.value === coluna) {
    // Se já está ordenada, alterna direção
    if (direcaoOrdenacao.value === 'asc') {
      direcaoOrdenacao.value = 'desc';
    } else if (direcaoOrdenacao.value === 'desc') {
      // Volta ao padrão (sem ordenação)
      colunaOrdenacao.value = '';
      direcaoOrdenacao.value = 'asc';
    }
  } else {
    // Nova coluna, começa com asc
    colunaOrdenacao.value = coluna;
    direcaoOrdenacao.value = 'asc';
  }
}

/**
 * Compara valores de qualquer tipo para ordenação
 */
function compararValores(a, b, coluna, direcao) {
  let valueA = a[coluna];
  let valueB = b[coluna];

  // Tratar valores nulos/undefined
  if (valueA == null && valueB == null) return 0;
  if (valueA == null) return direcao === 'asc' ? 1 : -1;
  if (valueB == null) return direcao === 'asc' ? -1 : 1;

  // Converter para número se ambos forem numéricos
  if (!isNaN(valueA) && !isNaN(valueB)) {
    const result = Number(valueA) - Number(valueB);
    return direcao === 'asc' ? result : -result;
  }

  // Converter para data se parecer ser data
  const dateA = new Date(valueA);
  const dateB = new Date(valueB);
  if (!isNaN(dateA.getTime()) && !isNaN(dateB.getTime())) {
    const result = dateA - dateB;
    return direcao === 'asc' ? result : -result;
  }

  // Comparação de string (case-insensitive)
  const strA = String(valueA).toLowerCase();
  const strB = String(valueB).toLowerCase();
  const result = strA.localeCompare(strB, 'pt-BR');
  return direcao === 'asc' ? result : -result;
}

const dadosFiltrados = computed(() => {
  let lista = [...dados.value];

  // Aplicar filtro de contrato
  if (filtroContrato.value) {
    lista = lista.filter(item => item.contrato_est_ambiental === filtroContrato.value);
  }

  // Aplicar ordenação se houver
  if (colunaOrdenacao.value) {
    lista.sort((a, b) => compararValores(a, b, colunaOrdenacao.value, direcaoOrdenacao.value));
  }

  // Faz o filtro das colunas, mas SEMPRE mantém changelogs para badges funcionarem
  return lista.map((item) => {
    let filtrado = {};
    todasColunas.forEach((coluna) => {
      if (colunasVisiveis.value.includes(coluna)) {
        filtrado[coluna] = item[coluna];
      } else {
        filtrado[coluna] = null;
      }
    });
    // IMPORTANTE: Sempre manter changelogs para os badges de "editado" funcionarem
    filtrado.changelogs = item.changelogs;
    return filtrado;
  });
});

// ========================================
// EXPORTAÇÃO E MODAL
// ========================================

function exportExcel() {
    const camposvalidos = colunasVisiveis.value.filter(coluna => !camposocultos.includes(coluna));
    const params = new URLSearchParams({
      campos: camposvalidos.join(','),
    })

    const url = `empreendimentos-export?${params.toString()}`
    window.location.href = url
}

function campoFoiEditado(linha, campo) {
  return linha.changelogs?.some(change => change.field === campo)
}

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

// ========================================
// SELEÇÃO DE TODOS OS CAMPOS
// ========================================

const colunaTravada = 'changelogs'

const todosSelecionados = computed(() => {
  const colunasFiltradas = todasColunas.filter(
    c => !camposocultos.includes(c) && c !== colunaTravada && c !== 'id' && c !== 'cod_emp'
  )
  return colunasFiltradas.every(c => colunasVisiveis.value.includes(c))
})

function toggleSelecionarTodos(event) {
  const checked = event.target.checked
  const colunasFiltradas = todasColunas.filter(
    c => !camposocultos.includes(c) && c !== colunaTravada && c !== 'id' && c !== 'cod_emp'
  )

  // Sempre manter ID e COD_EMP
  const colunasFixas = ['id', 'cod_emp'];

  if (checked) {
    // Adicionar as filtradas + manter as fixas
    colunasVisiveis.value = [...colunasFixas, ...colunasFiltradas];

    // Adicionar changelogs se tiver
    if (colunasVisiveis.value.includes(colunaTravada) === false) {
      colunasVisiveis.value.push(colunaTravada);
    }
  } else {
    // Quando deseleciona, manter apenas as fixas (ID e COD_EMP)
    colunasVisiveis.value = [...colunasFixas];
  }
}

// ========================================
// FUNÇÕES DE SUPORTE
// ========================================

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

    return new Date(Math.max(...datas.map(d => d.getTime())));
  }

  if (typeof changelogs === 'object') {
    return changelogs.created_at ? new Date(changelogs.created_at) : null;
  }

  return null;
}

function handleSalvar(dados) {
    router.post(route('sgc.gestao.cadastrarempreendimento', { id: 2 }), dados);
}

// ========================================
// LIFECYCLE
// ========================================

onMounted(() => {
  const modalEl = modalRef.value
  if (modalEl) {
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
</style>
