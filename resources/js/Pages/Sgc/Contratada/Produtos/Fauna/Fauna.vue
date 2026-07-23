<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import NavbarContrato from '@/Pages/Sgc/Contratada/NavbarContrato.vue';
import { ref, computed } from 'vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import NavButton from '@/Components/NavButton.vue';
import PreviewApresentacaoModal from '../Pmqa/Components/Modals/PreviewApresentacaoModal.vue';

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
    produtosModulo: { type: Array, default: () => [] },
    isModuloProduto: { type: Boolean, default: false },
});

const isPerfil3 = computed(() => (props.auth.user.perfis_id ?? 0) === 3);

const previewModal = ref(null);

// Lista de produtos disponíveis: os fixos do sistema + os criados dinamicamente a partir de um módulo
const produtosFixos = [
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

const produtos = computed(() => {
  const dinamicos = (props.produtosModulo ?? [])
    .filter(p => !produtosFixos.some(fixo => fixo.routeParam === p.produto_slug))
    .map(p => ({ title: p.produto_titulo, routeParam: p.produto_slug }));

  return [...produtosFixos, ...dinamicos];
});

// Estado reativo para produto e subproduto
const selectedProduto = ref(props.produto.toLowerCase());
const selectedSubproduto = ref('');

// Estado para ordenação
const sortColumn = ref('id_campanha');
const sortDirection = ref('asc'); // 'asc' ou 'desc'

// Helpers
const isEia = computed(() => selectedProduto.value === 'eia');
const isEspeleologia = computed(() => selectedProduto.value === 'espeleologia');
const isEmElaboracao = (c) => c.status === 'Em elaboração';

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

// Lista única de descrições de subprodutos
const uniqueSubprodutos = computed(() => {
  const descriptions = props.subprodutos.map(sub => sub.descricao_revisada).filter(desc => desc);
  return [...new Set(descriptions)];
});

const campanhasFiltradas = computed(() => {
  if (!selectedSubproduto.value) {
    return props.campanhas;
  }

  return props.campanhas.filter(campanha => campanha.subproduto === selectedSubproduto.value);
});

// Função para alternar ordenação
const toggleSort = (column) => {
  if (sortColumn.value === column) {
    // Se clicar na mesma coluna, inverte a direção
    sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
  } else {
    // Se clicar em coluna diferente, começa com ascendente
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

    // Tratamento para valores nulos/undefined
    if (valorA === null || valorA === undefined) valorA = '';
    if (valorB === null || valorB === undefined) valorB = '';

    // Conversão para datas se for coluna de data
    if (sortColumn.value === 'data_inicial' || sortColumn.value === 'data_final') {
      valorA = new Date(valorA).getTime() || 0;
      valorB = new Date(valorB).getTime() || 0;
    }

    // Comparação de números
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
  if (sortColumn.value !== column) return '⇅'; // Ícone neutro
  return sortDirection.value === 'asc' ? '↑' : '↓';
};

// Redirecionar para criação com validação de subproduto
const goToCreate = () => {
  if (!selectedSubproduto.value && !props.isModuloProduto) {
    alert('Por favor, selecione um subproduto antes de cadastrar.');
    return;
  }
  const subproduto = selectedSubproduto.value;
  router.get(
    route('sgc.contratada.produtos.create', [props.contrato, selectedProduto.value]),
    { subproduto },
    {
      preserveState: true,
      preserveScroll: true,
      onError: (errors) => console.error('Erro ao redirecionar:', errors),
      onSuccess: () => console.log('Redirecionamento bem-sucedido para create'),
    }
  );
};

const continuarCampanha = (campanha) => {
    router.get(
        route('sgc.contratada.produtos.create', [props.contrato, selectedProduto.value]),
        { subproduto: campanha.subproduto },
        { preserveState: true, preserveScroll: true }
    );
};

// Redirecionar para visualização
const visualizarCampanha = (campanha, modulo = null) => {
  // Para EIA, abrir modal de preview
  if (isEia.value) {
    previewModal.value.abrirModal(campanha);
    return;
  }

  // Espeleologia tem controller/rota dedicados, que carregam os relacionamentos
  // (justificativas, metodologia, anexos, resultados, profissionais) — a rota
  // genérica de Fauna não faz esse eager loading e devolve a campanha incompleta.
  if (isEspeleologia.value) {
    router.get(
      route('sgc.contratada.produtos.espeleo.show', [
        props.contrato,
        'espeleologia',
        campanha.id,
      ])
    );
    return;
  }

  router.get(
    route('sgc.contratada.produtos.show', [
      props.contrato,
      selectedProduto.value,
      campanha.id,
      modulo,
    ])
  );
};

// Gerenciar PMQA (específico para EIA)
const gerenciarCampanha = (pmqaId) => {
  router.get(
    route(
      'contratos.contratada.sgc.pmqa.configuracao.ponto.index',
      [props.contrato, selectedProduto.value, pmqaId]
    )
  );
};

// Redirecionar para análise
const analisarCampanha = (campanhaId) => {
  router.get(route('sgc.contratada.produtos.analise', [props.contrato, selectedProduto.value, campanhaId]));
};

const editarCampanha = (campanha) => {
  router.get(
    route('sgc.contratada.produtos.edit', [props.contrato, selectedProduto.value, campanha.id]),
    { subproduto: campanha.subproduto },
    { preserveState: true, preserveScroll: true }
  );
};

const excluirCampanha = (campanhaId) => {
    if (!confirm('Tem certeza que deseja excluir esta campanha? Esta ação não pode ser desfeita.')) return;
    router.delete(route('sgc.contratada.produtos.destroy', [props.contrato, selectedProduto.value, campanhaId]));
};

const getStatusLabel = (status) => {
  if (status === 'Rejeitada') return 'Em revisão';
  return status || 'N/A';
};

const showModalAprovarTudo = ref(false);
const showModalReprovarTudo = ref(false);
const campanhaEmAnalise = ref(null);
const justificativaReprovacao = ref('');
const erroReprovacao = ref('');

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
    route('sgc.contratada.produtos.aprovarTudo', [props.contrato, selectedProduto.value, campanhaEmAnalise.value.id]),
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
    route('sgc.contratada.produtos.reprovarTudo', [props.contrato, selectedProduto.value, campanhaEmAnalise.value.id]),
    { comentario: justificativaReprovacao.value },
    {
      onSuccess: () => {
        showModalReprovarTudo.value = false;
        alert('✅ Campanha rejeitada com sucesso!');
      },
      onError: (errors) => {
        console.error('Erro ao reprovar:', errors);
        erroReprovacao.value = 'comentario' in errors ? errors.comentario : 'Erro ao reprovar campanha.';
      },
    }
  );
};

// Arquivar e restaurar campanhas
const verArquivadas = () => {
  router.get(
    route('sgc.contratada.produtos.index', [props.contrato, selectedProduto.value]),
    { arquivadas: true },
    { preserveState: true, preserveScroll: true }
  );
};

const verAtivas = () => {
  router.get(
    route('sgc.contratada.produtos.index', [props.contrato, selectedProduto.value]),
    {},
    { preserveState: true, preserveScroll: true }
  );
};

// Ir para configurações do módulo (rota nomeada) — abre em nova aba
const goToModulosConfiguracoes = () => {
  const url = route('sgc.contratada.produtos.modulos.configuracoes.index', [props.contrato, selectedProduto.value]);
  const w = window.open(url, '_blank');
  if (w) w.opener = null;
};

const arquivarCampanha = (campanha) => {
  if (!confirm(`Arquivar a campanha ${campanha.id_campanha || campanha.id}?`)) return;

  router.post(
    route('sgc.contratada.produtos.arquivar', [props.contrato, selectedProduto.value, campanha.id])
  );
};

const restaurarCampanha = (campanha) => {
  router.post(
    route('sgc.contratada.produtos.restaurar', [props.contrato, selectedProduto.value, campanha.id])
  );
};

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
            <h2 class="text-center mb-4">{{ produtos.find(p => p.routeParam === selectedProduto.value)?.title.toUpperCase() || produto.toUpperCase() }}</h2>
            <div v-if="!subprodutos.length && !isModuloProduto" class="alert alert-danger">
              Nenhum dado encontrado para {{ produtos.find(p => p.routeParam === selectedProduto.value)?.title || produto }}.
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
                        : 'CAMPANHAS DE ' + (produtos.find(p => p.routeParam === selectedProduto.value)?.title.toUpperCase() || produto.toUpperCase())
                      }}
                    </h4>

                    <button
                      type="button"
                      class="btn btn-sm btn-outline-secondary archived-toggle"
                      @click="props.mostrarArquivadas ? verAtivas() : verArquivadas()"
                    >
                      {{ props.mostrarArquivadas ? 'Ver ativas' : `Arquivadas (${props.totalArquivadas})` }}
                    </button>
                  </div>
                  
                  <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                      <thead>
                        <tr>
                          <th class="text-center sortable-header" @click="toggleSort('id_campanha')">
                            Campanha
                            <span class="sort-icon">{{ getSortIcon('id_campanha') }}</span>
                          </th>
                          <th class="text-center sortable-header" @click="toggleSort('empreendimento')">
                            Empreendimento
                            <span class="sort-icon">{{ getSortIcon('empreendimento') }}</span>
                          </th>
                          <th class="text-center sortable-header" @click="toggleSort('subproduto')">
                            Subproduto
                            <span class="sort-icon">{{ getSortIcon('subproduto') }}</span>
                          </th>
                          <th class="text-center sortable-header" @click="toggleSort('data_inicial')">
                            Data Inicial
                            <span class="sort-icon">{{ getSortIcon('data_inicial') }}</span>
                          </th>
                          <th class="text-center sortable-header" @click="toggleSort('data_final')">
                            Data Final
                            <span class="sort-icon">{{ getSortIcon('data_final') }}</span>
                          </th>
                          <th class="text-center sortable-header" @click="toggleSort('status')">
                            Status
                            <span class="sort-icon">{{ getSortIcon('status') }}</span>
                          </th>
                          <th class="text-center">Ação</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr v-for="campanha in campanhasOrdenadas" :key="campanha.id">
                          <td class="text-center">{{ campanha.id_campanha || 'N/A' }}</td>
                          <td class="text-center">{{ campanha.empreendimento || 'N/A' }}</td>
                          <td class="text-center">{{ campanha.subproduto || 'N/A' }}</td>
                          <td class="text-center">{{ campanha.data_inicial || 'N/A' }}</td>
                          <td class="text-center">{{ campanha.data_final || 'N/A' }}</td>
                          <td class="text-center">
                            <span
                              v-if="campanha.status === 'Aprovada'"
                              class="status-circle status-circle-approved"
                            ></span>
                            <span
                              v-else-if="campanha.status === 'Rejeitada'"
                              class="status-circle status-circle-rejected"
                            ></span>
                            <span
                              v-else-if="campanha.status === 'rascunho'"
                              class="status-circle status-circle-rejected"
                            ></span>
                            <span
                              v-else-if="campanha.status === 'Em análise'"
                              class="status-circle status-circle-in-analysis"
                            ></span>
                            <span
                              v-else-if="campanha.status === 'Em elaboração'"
                              class="status-circle status-circle-draft"
                            ></span>
                            <!-- {{ campanha.status || 'N/A' }} -->
                            {{ getStatusLabel(campanha.status) }}
                          </td>
                          <td class="text-center">
                            <!-- Visualizar -->
                            <NavButton
                              type-button="info"
                              title="Visualizar"
                              @click="visualizarCampanha(campanha, isEspeleologia ? 'espeleologia' : null)"
                            />
                            
                            <!-- Gerenciar PMQA (EIA) -->
                            <NavButton
                              v-if="isEia && campanha.status?.trim() === 'Em elaboração'"
                              type-button="primary"
                              title="Gerenciar"
                              @click="gerenciarCampanha(campanha.id)"
                            />

                            <!-- Continuar (Em elaboração, não é perfil 3) -->
                            <NavButton
                              v-if="campanha.status === 'Em elaboração' && (props.auth.user.perfis_id ?? 0) !== 3"
                              type-button="warning"
                              title="Editar"
                              @click="continuarCampanha(campanha)"
                            />
                            
                            <!-- Analisar (Em análise, perfil com permissão) -->
                            <NavButton
                              v-if="canApprove && campanha.status === 'Em análise'"
                              type-button="success"
                              title="Analisar"
                              @click="analisarCampanha(campanha.id)"
                            />

                          <NavButton
                            v-if="isPerfil3 && !props.mostrarArquivadas"
                            type-button="secondary"
                            title="Arquivar"
                            @click="arquivarCampanha(campanha)"
                          />
                          <NavButton
                            v-if="isPerfil3 && props.mostrarArquivadas"
                            type-button="warning"
                            title="Restaurar"
                            @click="restaurarCampanha(campanha)"
                          />

                          <!-- Menu de Análise em Massa (dropdown) -->
                          <div v-if="canApprove && campanha.status === 'Em análise'" class="dropdown d-inline-block">
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
                              v-if="campanha.status === 'Em elaboração' && (props.auth.user.perfis_id ?? 0) !== 4"
                              type-button="danger"
                              title="Excluir"
                              @click="excluirCampanha(campanha.id)"
                            />

                            <!-- Editar Rejeitada (Rejeitada, não é perfil 3) -->
                            <NavButton
                              v-if="campanha.status === 'Rejeitada' && (props.auth.user.perfis_id ?? 0) !== 3"
                              type-button="warning"
                              title="Editar"
                              @click="editarCampanha(campanha)"
                            />
                          </td>
                        </tr>
                        <tr v-if="!campanhasOrdenadas.length">
                          <td colspan="7" class="text-center">
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

    <!-- Modal de Preview (EIA) -->
    <PreviewApresentacaoModal
      ref="previewModal"
      :contrato="contrato"
      :produto="produto"
      @aprovado="(id) => atualizarStatus(id, 'Em elaboracao')"
      @reprovado="(id) => atualizarStatus(id, 'Reprovado')"
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

/* ─── Estilos para ordenação ─────────────────────────────────────────────── */
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
