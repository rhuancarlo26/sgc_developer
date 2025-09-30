<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import NavbarContrato from '@/Pages/Sgc/Contratada/NavbarContrato.vue';
import { ref, computed } from 'vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import NavButton from '@/Components/NavButton.vue';

const props = defineProps({
    subprodutos: { type: Array, default: () => [] },
    requests: { type: Array, default: () => [] },
    contrato: { type: [Number, String], required: true },
    produto: { type: String, required: true },
    contratos: { type: Object, required: true },
    campanhas: { type: Array, default: () => [] },
    canApprove: { type: Boolean, default: false },
    auth: { type: Object, required: true },
});

console.log('Auth:', props.auth);
console.log('requests:', props.requests);

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

// Atualizar a rota quando o produto mudar
const updateProduto = () => {
  router.get(
    route('sgc.contratada.produtos.index', [props.contrato, selectedProduto.value, selectedSubproduto.value || '']),
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

// Redirecionar para criação com validação de subproduto
const goToCreate = () => {
  if (!selectedSubproduto.value) {
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

// Redirecionar para visualização
const visualizarCampanha = (campanhaId) => {
  router.get(route('sgc.contratada.produtos.show', [props.contrato, selectedProduto.value, campanhaId]));
};

// Redirecionar para análise
const analisarCampanha = (campanhaId) => {
  router.get(route('sgc.contratada.produtos.analise', [props.contrato, selectedProduto.value, campanhaId]));
};

// Redirecionar para edição
const editarCampanha = (campanhaId) => {
  router.get(route('sgc.contratada.produtos.edit', [props.contrato, selectedProduto.value, campanhaId]));
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
            <div v-if="!subprodutos.length" class="alert alert-danger">
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
                      <select v-model="selectedSubproduto" @change="updateProduto" class="form-select">
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
                    </div>
                  </div>
                </div>
              </div>

              <!-- Tabela de Campanhas -->
              <div class="col-md-12 mt-4">
                <div class="block-card">
                  <h4 class="text-center mb-4">CAMPANHAS DE {{ produtos.find(p => p.routeParam === selectedProduto.value)?.title.toUpperCase() || produto.toUpperCase() }}</h4>
                  <div class="table-responsive">
                    <table class="table table-bordered">
                      <thead>
                        <tr>
                          <th class="text-center">ID Campanha</th>
                          <th class="text-center">Empreendimento</th>
                          <th class="text-center">Subproduto</th>
                          <th class="text-center">Data Inicial</th>
                          <th class="text-center">Data Final</th>
                          <th class="text-center">Status</th>
                          <th class="text-center">Ação</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr v-for="campanha in props.campanhas" :key="campanha.id">
                          <td class="text-center">{{ campanha.id || 'N/A' }}</td>
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
                              v-else-if="campanha.status === 'Em análise'"
                              class="status-circle status-circle-in-analysis"
                            ></span>
                            <span
                              v-else-if="campanha.status === 'Em elaboração'"
                              class="status-circle status-circle-draft"
                            ></span>
                            {{ campanha.status || 'N/A' }}
                          </td>
                          <td class="text-center">
                            <NavButton
                              type-button="info"
                              title="Visualizar"
                              @click="visualizarCampanha(campanha.id)"
                            />
                            <NavButton
                              v-if="canApprove && campanha.status === 'Em análise'"
                              type-button="success"
                              title="Analisar"
                              @click="analisarCampanha(campanha.id)"
                            />
                            <NavButton
                              v-if="(campanha.status === 'Rejeitada' || campanha.status === 'Em elaboração') && (props.auth.user.perfis_id ?? 0) !== 2"
                              type-button="warning"
                              title="Editar"
                              @click="editarCampanha(campanha.id)"
                            />
                          </td>
                        </tr>
                        <tr v-if="!props.campanhas.length">
                          <td colspan="7" class="text-center">Nenhuma campanha disponível.</td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </template>
    </NavbarContrato>
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
  background-color: #6c757d; /* Cor para "Em elaboração" */
}
</style>
