<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import NavbarContrato from '@/Pages/Sgc/Contratada/NavbarContrato.vue';
import { ref, computed } from 'vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import NavButton from '@/Components/NavButton.vue';
import PreviewApresentacaoModal from './Pmqa/Components/Modals/PreviewApresentacaoModal.vue';
import { produtoConfig, getConfig, hasAction, hasFeature } from './Config/produtoConfig';

const props = defineProps({
    subprodutos: { type: Array, default: () => [] },
    contrato: { type: [Number, String], required: true },
    produto: { type: String, required: true },
    contratos: { type: Object, required: true },
    campanhas: { type: Array, default: () => [] },
    canApprove: { type: Boolean, default: false },
    auth: { type: Object, required: true },
    vinculacoes: { type: Object },
    mostrarArquivadas: { type: Boolean, default: false },
    totalArquivadas: { type: Number, default: 0 },
});

// Carregar configuração do produto
const config = computed(() => getConfig(props.produto));

const isPerfil3 = computed(() => (props.auth.user.perfis_id ?? 0) === 3);

const previewModal = ref(null);

// Lista de produtos disponíveis
const produtos = [
  { title: "Fauna", routeParam: "fauna" },
  { title: "Espeleologia", routeParam: "espeleologia" },
  { title: "Patrimônio", routeParam: "patrimonio" },
  { title: "Indígena", routeParam: "indigena" },
  { title: "Quilombola", routeParam: "quilombola" },
  { title: "Malarígeno", routeParam: "malarigeno" },
  { title: "Eia", routeParam: "eia" },
  { title: "Rima", routeParam: "rima" },
  { title: "Audiência", routeParam: "audiencia" },
  { title: "PBA", routeParam: "pba" },
  { title: "ASV", routeParam: "asv" },
  { title: "Viagens", routeParam: "viagens" },
];

// Estado reativo para produto e subproduto
const selectedProduto = ref(props.produto.toLowerCase());
const selectedSubproduto = ref('');

// Estado para ordenação
const sortColumn = ref('id_campanha');
const sortDirection = ref('asc');

// Estado para modais
const showModalAprovarTudo = ref(false);
const showModalReprovarTudo = ref(false);
const campanhaEmAnalise = ref(null);
const justificativaReprovacao = ref('');
const erroReprovacao = ref('');

// Atualizar a rota quando o produto mudar
const updateProduto = () => {
  selectedSubproduto.value = '';

  router.get(
    route('sgc.contratada.produtos.index', [props.contrato, selectedProduto.value]),
    {},
    {
      preserveState: true,
      preserveScroll: true,
      onError: (errors) => console.error('Erro ao mudar produto:', errors),
      onSuccess: () => console.log('Produto alterado com sucesso:', selectedProduto.value),
    }
  );
};

const normalizarTexto = (texto) => {
  return texto
    ?.trim()
    .toLowerCase()
    .normalize('NFD')
    .replace(/[\u0300-\u036f]/g, '');
};

const isSubprodutoPaipa = (subproduto) => {
  return normalizarTexto(subproduto)
    ?.startsWith('elaboracao do projeto');
};

const isProdutoPmqa = computed(() => ['pmqa', 'eia'].includes(selectedProduto.value));

const isSubprodutoRelatorioQualidadeAgua = (subproduto) => {
  return normalizarTexto(subproduto) === 'relatorio da analise da qualidade da agua';
};

// Lista única de descrições de subprodutos
const uniqueSubprodutos = computed(() => {
  const descriptions = props.subprodutos.map(sub => sub.descricao_revisada).filter(desc => desc);
  return [...new Set(descriptions)];
});

const campanhasFiltradas = computed(() => {
  if (isProdutoPmqa.value && !isSubprodutoRelatorioQualidadeAgua(selectedSubproduto.value)) {
    return [];
  }

  if (!selectedSubproduto.value) {
    return props.campanhas;
  }

  return props.campanhas.filter(campanha => campanha.subproduto === selectedSubproduto.value);
});

// Função para alternar ordenação
const toggleSort = (column) => {
  if (sortColumn.value === column) {
    sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
  } else {
    sortColumn.value = column;
    sortDirection.value = 'asc';
  }
};

// Computado para campanhas ordenadas
const campanhasOrdenadas = computed(() => {
  const items = [...campanhasFiltradas.value];

  items.sort((a, b) => {
    let valorA = a[sortColumn.value];
    let valorB = b[sortColumn.value];

    if (valorA === null || valorA === undefined) valorA = '';
    if (valorB === null || valorB === undefined) valorB = '';

    if (sortColumn.value === 'data_inicial' || sortColumn.value === 'data_final') {
      valorA = new Date(valorA).getTime() || 0;
      valorB = new Date(valorB).getTime() || 0;
    }

    if (sortDirection.value === 'asc') {
      return valorA > valorB ? 1 : valorA < valorB ? -1 : 0;
    } else {
      return valorA < valorB ? 1 : valorA > valorB ? -1 : 0;
    }
  });

  return items;
});

// Helper para mostrar ícone de ordenação
const getSortIcon = (column) => {
  if (sortColumn.value !== column) return '⇅';
  return sortDirection.value === 'asc' ? '↑' : '↓';
};

// Redirecionar para criação com validação de subproduto
const goToCreate = () => {
  if (!selectedSubproduto.value) {
    alert('Por favor, selecione um subproduto antes de cadastrar.');
    return;
  }

  const createRoute = config.value?.rotaNome?.create;
  if (!createRoute) {
    alert('Rota de cadastro não configurada para este produto.');
    return;
  }

  if (selectedProduto.value === 'patrimonio' && !isSubprodutoPaipa(selectedSubproduto.value)) {
    alert('Este subproduto de patrimônio ainda não possui tela de cadastro implementada.');
    return;
  }

  if (isProdutoPmqa.value && !isSubprodutoRelatorioQualidadeAgua(selectedSubproduto.value)) {
    alert('Para PMQA, selecione o subproduto "Relatório da Análise da Qualidade da Água".');
    return;
  }

  const subproduto = selectedSubproduto.value;
  router.get(
    route(createRoute, [props.contrato, selectedProduto.value]),
    { subproduto },
    {
      preserveState: true,
      preserveScroll: true,
      onError: (errors) => console.error('Erro ao redirecionar:', errors),
    }
  );
};

const continuarCampanha = (campanha) => {
    if (selectedProduto.value === 'patrimonio') {
      router.get(
        route('sgc.contratada.produtos.create', [props.contrato, selectedProduto.value]),
        { subproduto: campanha.subproduto, paipa_id: campanha.id },
        { preserveState: true, preserveScroll: true }
      );
      return;
    }

    router.get(
        route(config.value.rotaNome.create, [props.contrato, selectedProduto.value]),
        { subproduto: campanha.subproduto },
        { preserveState: true, preserveScroll: true }
    );
};

// Redirecionar para visualização
const visualizarCampanha = (campanha, modulo = null) => {
  // Para produtos com modal preview
  if (config.value.modalPreview) {
    previewModal.value.abrirModal(campanha);
    return;
  }

  // Para produtos que passam módulo como parâmetro
  const parametroModulo = config.value.passaModulo || modulo;

  if (selectedProduto.value === 'patrimonio') {
    router.get(
      route('sgc.contratada.produtos.create', [props.contrato, selectedProduto.value]),
      { subproduto: campanha.subproduto, paipa_id: campanha.id },
      { preserveState: true, preserveScroll: true }
    );
    return;
  }

  const params = [props.contrato, selectedProduto.value, campanha.id];
  if (parametroModulo) {
    params.push(parametroModulo);
  }

  router.get(route(config.value.rotaNome.show, params));
};

// Gerenciar campanha (específico para EIA/PMQA)
const gerenciarCampanha = (pmqaId) => {
  router.get(
    route(
      config.value.rotaNome.gerenciar,
      [props.contrato, selectedProduto.value, pmqaId]
    )
  );
};

// Redirecionar para análise
const analisarCampanha = (campanhaId) => {
  router.get(route(config.value.rotaNome.analise, [props.contrato, selectedProduto.value, campanhaId]));
};

const editarCampanha = (campanha) => {
  if (selectedProduto.value === 'patrimonio') {
    router.get(
      route('sgc.contratada.produtos.create', [props.contrato, selectedProduto.value]),
      { subproduto: campanha.subproduto, paipa_id: campanha.id },
      { preserveState: true, preserveScroll: true }
    );
    return;
  }

  router.get(
    route(config.value.rotaNome.edit, [props.contrato, selectedProduto.value, campanha.id]),
    { subproduto: campanha.subproduto },
    { preserveState: true, preserveScroll: true }
  );
};

const excluirCampanha = (campanhaId) => {
    if (!confirm('Tem certeza que deseja excluir esta campanha? Esta ação não pode ser desfeita.')) return;
    router.delete(route(config.value.rotaNome.destroy, [props.contrato, selectedProduto.value, campanhaId]));
};

const getStatusLabel = (status) => {
  if (status === 'Rejeitada' || status === 'Reprovada') return 'Em revisão';
  return status || 'N/A';
};

const abrirModalAprovarTudo = (campanha) => {
  campanhaEmAnalise.value = campanha;
  showModalAprovarTudo.value = true;
};

const abrirModalReprovarTudo = (campanha) => {
  campanhaEmAnalise.value = campanha;
  justificativaReprovacao.value = '';
  erroReprovacao.value = '';
  showModalReprovarTudo.value = true;
};

const confirmarAprovarTudo = () => {
  if (!campanhaEmAnalise.value) return;

  router.post(
    route(config.value.rotaNome.aprovarTudo, [props.contrato, selectedProduto.value, campanhaEmAnalise.value.id]),
    {},
    {
      onSuccess: () => {
        showModalAprovarTudo.value = false;
        alert('✅ Campanha aprovada com sucesso!');
      },
      onError: (errors) => {
        console.error('Erro ao aprovar:', errors);
        alert('❌ Erro: ' + (Object.values(errors).join(', ') || 'Tente novamente.'));
      },
    }
  );
};

const confirmarReprovarTudo = () => {
  if (!campanhaEmAnalise.value) return;

  if (!justificativaReprovacao.value.trim()) {
    erroReprovacao.value = 'A justificativa é obrigatória.';
    return;
  }

  if (justificativaReprovacao.value.trim().length < 10) {
    erroReprovacao.value = 'A justificativa deve ter no mínimo 10 caracteres.';
    return;
  }

  router.post(
    route(config.value.rotaNome.reprovarTudo, [props.contrato, selectedProduto.value, campanhaEmAnalise.value.id]),
    {
      comentario: justificativaReprovacao.value,
      observacoes: justificativaReprovacao.value,
    },
    {
      onSuccess: () => {
        showModalReprovarTudo.value = false;
        alert('✅ Campanha rejeitada com sucesso!');
      },
      onError: (errors) => {
        console.error('Erro ao reprovar:', errors);
        erroReprovacao.value = errors.comentario || errors.observacoes || 'Erro ao reprovar campanha.';
      },
    }
  );
};

// Arquivar e restaurar campanhas
const verArquivadas = () => {
  router.get(
    route(config.value.rotaNome.index, [props.contrato, selectedProduto.value]),
    { arquivadas: true },
    { preserveState: true, preserveScroll: true }
  );
};

const verAtivas = () => {
  router.get(
    route(config.value.rotaNome.index, [props.contrato, selectedProduto.value]),
    {},
    { preserveState: true, preserveScroll: true }
  );
};

// Ir para configurações do módulo
const goToModulosConfiguracoes = () => {
  const url = route('sgc.contratada.produtos.modulos.configuracoes.index', [props.contrato, selectedProduto.value]);
  const w = window.open(url, '_blank');
  if (w) w.opener = null;
};

const arquivarCampanha = (campanha) => {
  if (!confirm(`Arquivar a campanha ${campanha.id_campanha || campanha.id}?`)) return;

  router.post(
    route(config.value.rotaNome.arquivar, [props.contrato, selectedProduto.value, campanha.id])
  );
};

const restaurarCampanha = (campanha) => {
  router.post(
    route(config.value.rotaNome.restaurar, [props.contrato, selectedProduto.value, campanha.id])
  );
};

// Helpers para verificar se ação deve ser exibida
const deveExibirAcao = (acao) => hasAction(props.produto, acao);
const deveExibirColuna = (coluna) => config.value.colunas.includes(coluna);

</script>

<template>
  <AuthenticatedLayout>
    <Head :title="`${produtos.find(p => p.routeParam === selectedProduto.value)?.title || produto} - Contrato ${contrato}`" />

    <template #header>
      <div class="w-100 d-flex justify-content-between">
        <Breadcrumb
          class="align-self-center"
          :links="[
            { route: route('sgc.gestao.listagem', contratos.tipo_contrato), label: `Gestão de Contratos` },
            { route: '#', label: contratos.contratada }
          ]"
        />
      </div>
    </template>

    <NavbarContrato :tipo="{ id: contrato }">
      <template #body>
        <div class="card">
          <div class="card-body">
            <h2 class="text-center mb-4">{{ config.nome.toUpperCase() }}</h2>
            <div v-if="!subprodutos.length" class="alert alert-danger">
              Nenhum dado encontrado para {{ config.nome }}.
            </div>
            <div v-else class="row">
              <div class="col-md-12">
                <div class="row">
                  <!-- Filtro de Produto -->
                  <div class="col-md-4 mb-4">
                    <div class="block-card block-card-short">
                      <h4 class="text-center mb-2">ESCOLHER PRODUTO</h4>
                      <select v-model="selectedProduto" @change="updateProduto" class="form-select">
                        <option v-for="produto in produtos" :key="produto.routeParam" :value="produto.routeParam">
                          {{ produto.title }}
                        </option>
                      </select>
                    </div>
                  </div>
                  <!-- Filtro de Subproduto -->
                  <div class="col-md-4 mb-4">
                    <div class="block-card block-card-short">
                      <h4 class="text-center mb-2">ESCOLHER SUBPRODUTO</h4>
                      <select v-model="selectedSubproduto" class="form-select">
                        <option value="">Todos</option>
                        <option v-for="desc in uniqueSubprodutos" :key="desc" :value="desc">
                          {{ desc }}
                        </option>
                      </select>
                    </div>
                  </div>
                  <!-- Botões -->
                  <div class="col-md-4 mb-4">
                    <div class="row">
                      <div class="col-md-6 mb-4">
                        <div class="block-card block-card-short action-button bg-primary text-white cursor-pointer" @click="goToCreate">
                          Cadastrar
                        </div>
                      </div>
                      <div class="col-md-6 mb-4">
                        <div class="block-card block-card-short action-button bg-info text-white cursor-pointer" @click="goToModulosConfiguracoes">
                          Configurações
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Tabela de Campanhas -->
              <div class="col-md-12 mt-4">
                <div class="block-card">
                  <div class="d-flex justify-content-between align-items-center mb-4">
                    <div style="width: 140px;"></div>

                    <h4 class="text-center m-0">
                      {{ props.mostrarArquivadas
                        ? 'CAMPANHAS ARQUIVADAS'
                        : 'CAMPANHAS DE ' + config.nome.toUpperCase()
                      }}
                    </h4>

                    <button
                      v-if="config.temArquivo"
                      type="button"
                      class="btn btn-sm btn-outline-secondary archived-toggle"
                      @click="props.mostrarArquivadas ? verAtivas() : verArquivadas()"
                    >
                      {{ props.mostrarArquivadas ? 'Ver ativas' : `Arquivadas (${props.totalArquivadas})` }}
                    </button>
                    <div v-else style="width: 140px;"></div>
                  </div>

                  <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                      <thead>
                        <tr>
                          <!-- Renderizar apenas colunas configuradas -->
                          <th v-if="deveExibirColuna('id_campanha')" class="text-center sortable-header" @click="toggleSort('id_campanha')">
                            Campanha
                            <span class="sort-icon">{{ getSortIcon('id_campanha') }}</span>
                          </th>
                          <th v-if="deveExibirColuna('empreendimento')" class="text-center sortable-header" @click="toggleSort('empreendimento')">
                            Empreendimento
                            <span class="sort-icon">{{ getSortIcon('empreendimento') }}</span>
                          </th>
                          <th v-if="deveExibirColuna('tema')" class="text-center sortable-header" @click="toggleSort('tema')">
                            Tema
                            <span class="sort-icon">{{ getSortIcon('tema') }}</span>
                          </th>
                          <th v-if="deveExibirColuna('cod_emp')" class="text-center sortable-header" @click="toggleSort('cod_emp')">
                            Código Empreendimento
                            <span class="sort-icon">{{ getSortIcon('cod_emp') }}</span>
                          </th>
                          <th v-if="deveExibirColuna('subproduto')" class="text-center sortable-header" @click="toggleSort('subproduto')">
                            Subproduto
                            <span class="sort-icon">{{ getSortIcon('subproduto') }}</span>
                          </th>
                          <th v-if="deveExibirColuna('data_inicial')" class="text-center sortable-header" @click="toggleSort('data_inicial')">
                            Data Inicial
                            <span class="sort-icon">{{ getSortIcon('data_inicial') }}</span>
                          </th>
                          <th v-if="deveExibirColuna('data_final')" class="text-center sortable-header" @click="toggleSort('data_final')">
                            Data Final
                            <span class="sort-icon">{{ getSortIcon('data_final') }}</span>
                          </th>
                          <th v-if="deveExibirColuna('status') || deveExibirColuna('status_aprovacao')" class="text-center sortable-header" @click="toggleSort('status')">
                            Status
                            <span class="sort-icon">{{ getSortIcon('status') }}</span>
                          </th>
                          <th class="text-center">Ação</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr v-for="campanha in campanhasOrdenadas" :key="campanha.id">
                          <td v-if="deveExibirColuna('id_campanha')" class="text-center">{{ campanha.id_campanha || 'N/A' }}</td>
                          <td v-if="deveExibirColuna('empreendimento')" class="text-center">{{ campanha.empreendimento || 'N/A' }}</td>
                          <td v-if="deveExibirColuna('tema')" class="text-center">{{ campanha.tema || 'N/A' }}</td>
                          <td v-if="deveExibirColuna('cod_emp')" class="text-center">{{ campanha.cod_emp || 'N/A' }}</td>
                          <td v-if="deveExibirColuna('subproduto')" class="text-center">{{ campanha.subproduto || 'N/A' }}</td>
                          <td v-if="deveExibirColuna('data_inicial')" class="text-center">{{ campanha.data_inicial || 'N/A' }}</td>
                          <td v-if="deveExibirColuna('data_final')" class="text-center">{{ campanha.data_final || 'N/A' }}</td>
                          <td v-if="deveExibirColuna('status') || deveExibirColuna('status_aprovacao')" class="text-center">
                            <span
                              v-if="campanha.status === 'Aprovada' || campanha.status_aprovacao === 'Aprovada'"
                              class="status-circle status-circle-approved"
                            ></span>
                            <span
                              v-else-if="campanha.status === 'Rejeitada' || campanha.status_aprovacao === 'Rejeitada' || campanha.status === 'Reprovada' || campanha.status_aprovacao === 'Reprovada'"
                              class="status-circle status-circle-rejected"
                            ></span>
                            <span
                              v-else-if="campanha.status === 'rascunho' || campanha.status === 'Rascunho'"
                              class="status-circle status-circle-draft"
                            ></span>
                            <span
                              v-else-if="campanha.status === 'Em análise'"
                              class="status-circle status-circle-in-analysis"
                            ></span>
                            <span
                              v-else-if="campanha.status === 'Em elaboração'"
                              class="status-circle status-circle-draft"
                            ></span>
                            {{ getStatusLabel(campanha.status || campanha.status_aprovacao) }}
                          </td>
                          <td class="text-center">
                            <!-- Visualizar -->
                            <NavButton
                              v-if="deveExibirAcao('visualizar')"
                              type-button="info"
                              title="Visualizar"
                              @click="visualizarCampanha(campanha)"
                            />

                            <!-- Gerenciar (EIA/PMQA) -->
                            <NavButton
                              v-if="deveExibirAcao('gerenciar') && campanha.status?.trim() === 'Em elaboração'"
                              type-button="primary"
                              title="Gerenciar"
                              @click="gerenciarCampanha(campanha.id)"
                            />

                            <!-- Continuar (Em elaboração, não é perfil 3) -->
                            <NavButton
                              v-if="deveExibirAcao('editar') && campanha.status === 'Em elaboração' && (props.auth.user.perfis_id ?? 0) !== 3"
                              type-button="warning"
                              title="Editar"
                              @click="continuarCampanha(campanha)"
                            />

                            <!-- Analisar (Em análise, perfil com permissão) -->
                            <NavButton
                              v-if="deveExibirAcao('analisar') && canApprove && campanha.status === 'Em análise'"
                              type-button="success"
                              title="Analisar"
                              @click="analisarCampanha(campanha.id)"
                            />

                            <!-- Arquivar (apenas perfil 3) -->
                            <NavButton
                              v-if="deveExibirAcao('arquivar') && isPerfil3 && !props.mostrarArquivadas"
                              type-button="secondary"
                              title="Arquivar"
                              @click="arquivarCampanha(campanha)"
                            />
                            <!-- Restaurar (apenas perfil 3, apenas em visualização de arquivadas) -->
                            <NavButton
                              v-if="deveExibirAcao('restaurar') && isPerfil3 && props.mostrarArquivadas"
                              type-button="warning"
                              title="Restaurar"
                              @click="restaurarCampanha(campanha)"
                            />

                            <!-- Menu de Análise em Massa (dropdown) -->
                            <div v-if="deveExibirAcao('aprovar') && deveExibirAcao('reprovar') && canApprove && campanha.status === 'Em análise'" class="dropdown d-inline-block">
                              <button
                                class="btn btn-sm btn-outline-secondary dropdown-toggle"
                                type="button"
                                :id="`dropdownMenu-${campanha.id}`"
                                data-bs-toggle="dropdown"
                                aria-expanded="false"
                              >
                                ⋮
                              </button>
                              <ul :aria-labelledby="`dropdownMenu-${campanha.id}`" class="dropdown-menu">
                                <li>
                                  <a class="dropdown-item text-success" href="#" @click.prevent="abrirModalAprovarTudo(campanha)">
                                    <strong>✓ Aprovar</strong>
                                  </a>
                                </li>
                                <li>
                                  <a class="dropdown-item text-danger" href="#" @click.prevent="abrirModalReprovarTudo(campanha)">
                                    <strong>✗ Reprovar</strong>
                                  </a>
                                </li>
                              </ul>
                            </div>

                            <!-- Excluir (Em elaboração, não é perfil 4) -->
                            <NavButton
                              v-if="deveExibirAcao('excluir') && campanha.status === 'Em elaboração' && (props.auth.user.perfis_id ?? 0) !== 4"
                              type-button="danger"
                              title="Excluir"
                              @click="excluirCampanha(campanha.id)"
                            />

                            <!-- Editar Rejeitada/Reprovada (não é perfil 3) -->
                            <NavButton
                              v-if="deveExibirAcao('editar') && ['Rejeitada', 'Reprovada'].includes(campanha.status) && (props.auth.user.perfis_id ?? 0) !== 3"
                              type-button="warning"
                              title="Editar"
                              @click="editarCampanha(campanha)"
                            />
                          </td>
                        </tr>
                        <tr v-if="!campanhasOrdenadas.length">
                          <td :colspan="config.colunas.length + 1" class="text-center">
                            {{ props.mostrarArquivadas ? 'Nenhuma campanha arquivada.' : 'Nenhuma campanha disponível.' }}
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Modal Aprovar Tudo -->
        <div class="modal fade" :class="{ 'show d-block': showModalAprovarTudo }" tabindex="-1" role="dialog" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
              <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Aprovar Campanha</h5>
                <button type="button" class="btn-close btn-close-white" @click="showModalAprovarTudo = false"></button>
              </div>
              <div class="modal-body">
                <p class="mb-0">
                  <strong>Tem certeza que deseja aprovar TODAS as etapas da campanha <span class="text-primary">{{ campanhaEmAnalise?.id_campanha }}</span>?</strong>
                </p>
                <p class="text-muted mt-2">Esta ação não pode ser desfeita. O status da campanha será alterado para <strong>Aprovada</strong>.</p>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" @click="showModalAprovarTudo = false">Cancelar</button>
                <button type="button" class="btn btn-success" @click="confirmarAprovarTudo">Sim, Aprovar Tudo</button>
              </div>
            </div>
          </div>
        </div>
        <div v-if="showModalAprovarTudo" class="modal-backdrop fade show"></div>

        <!-- Modal Reprovar Tudo -->
        <div class="modal fade" :class="{ 'show d-block': showModalReprovarTudo }" tabindex="-1" role="dialog" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
              <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Reprovar Campanha</h5>
                <button type="button" class="btn-close btn-close-white" @click="showModalReprovarTudo = false"></button>
              </div>
              <div class="modal-body">
                <p><strong>Campanha: {{ campanhaEmAnalise?.id_campanha }}</strong></p>
                <p class="text-muted">Digite a justificativa para reprovar TODAS as etapas dessa campanha.</p>
                <div class="mb-3">
                  <label class="form-label">Justificativa *</label>
                  <textarea
                    v-model="justificativaReprovacao"
                    class="form-control"
                    rows="5"
                    placeholder="Descreva os motivos da reprovação (mínimo 10 caracteres)..."
                  ></textarea>
                  <small v-if="erroReprovacao" class="text-danger d-block mt-2">{{ erroReprovacao }}</small>
                  <small class="text-muted d-block mt-1">{{ justificativaReprovacao.length }} caracteres</small>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" @click="showModalReprovarTudo = false">Cancelar</button>
                <button type="button" class="btn btn-danger" @click="confirmarReprovarTudo">Sim, Reprovar Tudo</button>
              </div>
            </div>
          </div>
        </div>
        <div v-if="showModalReprovarTudo" class="modal-backdrop fade show"></div>

      </template>
    </NavbarContrato>

    <!-- Modal de Preview (EIA/PMQA) -->
    <PreviewApresentacaoModal
      v-if="config.modalPreview"
      ref="previewModal"
      :contrato="contrato"
      :produto="produto"
    />
  </AuthenticatedLayout>
</template>

<style scoped>
.block-card {
  background-color: #fffffff3;
  border: 1px solid #e2e4e6;
  border-radius: 5px;
  padding: 15px;
  min-height: 200px;
  transition: all 0.3s ease;
}

.block-card:hover {
  background-color: #ffffff;
}

.block-card-short {
  min-height: 100px;
  padding: 10px;
}

.bg-primary {
  background-color: #1fa050 !important;
}

.bg-success {
  background-color: #f59505 !important;
}

.bg-info {
  background-color: #17a2b8 !important;
}

.bg-warning {
  background-color: #ffc107 !important;
}

.action-button.bg-warning:not(.cursor-not-allowed):hover {
  background-color: #e0a800 !important;
}

.table-responsive {
  margin-bottom: 1rem;
}

.table th, .table td {
  vertical-align: middle;
}

.cursor-pointer {
  cursor: pointer;
}

.cursor-not-allowed {
  cursor: not-allowed;
  opacity: 0.6;
}

.form-select {
  width: 100%;
  padding: 0.5rem;
}

.action-button {
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.25rem;
  font-weight: 500;
  text-align: center;
  border-radius: 8px;
  transition: transform 0.2s ease, background-color 0.3s ease;
}

.action-button:not(.cursor-not-allowed):hover {
  transform: scale(1.05);
  background-color: #e9ecef;
}

.status-circle {
  display: inline-block;
  width: 12px;
  height: 12px;
  border-radius: 50%;
  margin-right: 6px;
  vertical-align: middle;
}

.status-circle-approved {
  background-color: #28a745;
}

.status-circle-rejected {
  background-color: #dc3545;
}

.status-circle-in-analysis {
  background-color: #fd7e14;
}

.status-circle-draft {
  background-color: #6c757d;
}

.sortable-header {
  cursor: pointer;
  user-select: none;
  padding: 0.75rem !important;
  transition: background-color 0.2s ease;
  font-weight: 600;
}

.sortable-header:hover {
  background-color: #f0f0f0;
}

.sortable-header.active {
  background-color: #e8e8e8;
}

.sort-icon {
  margin-left: 6px;
  font-size: 0.85em;
  opacity: 0.7;
  transition: opacity 0.2s ease;
}

.sortable-header:hover .sort-icon {
  opacity: 1;
}

.archived-toggle {
  min-width: 140px;
  font-weight: 500;
}
</style>
