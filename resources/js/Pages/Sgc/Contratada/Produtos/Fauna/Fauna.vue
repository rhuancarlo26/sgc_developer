<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import NavbarContrato from '@/Pages/Sgc/Contratada/NavbarContrato.vue';
import { ref, computed } from 'vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import NavButton from '@/Components/NavButton.vue';

const props = defineProps({
    subprodutos: { type: Array, default: () => [] },
    contrato: { type: [Number, String], required: true },
    produto: { type: String, required: true },
    contratos: { type: Object, required: true },
    campanhas: { type: Array, default: () => [] },
    canApprove: { type: Boolean, default: false },
    auth: { type: Object, required: true },
});

console.log('Auth:', props.auth);

// Estado reativo para o subproduto selecionado e exibição de campanhas
const selectedSubproduto = ref('');
const showCampanhas = ref(false);

// Lista única de descrições de subprodutos
const uniqueSubprodutos = computed(() => {
    const descriptions = props.subprodutos.map(sub => sub.descricao_revisada).filter(desc => desc);
    return [...new Set(descriptions)];
});

// Filtrar subprodutos com base no selecionado
const filteredSubprodutos = computed(() => {
    if (!selectedSubproduto.value) return props.subprodutos;
    return props.subprodutos.filter(sub => sub.descricao_revisada === selectedSubproduto.value);
});

// Filtrar campanhas com base no subproduto selecionado
const filteredCampanhas = computed(() => {
    if (!selectedSubproduto.value) return props.campanhas;
    return props.campanhas.filter(campanha => campanha.subproduto === selectedSubproduto.value);
});

// Redirecionar para a página de criação
const goToCreate = () => {
    const subproduto = selectedSubproduto.value || '';
    router.get(
        route('sgc.contratada.produtos.create', [props.contrato, props.produto.toLowerCase()]),
        { subproduto },
        {
            preserveState: true,
            preserveScroll: true,
            onError: (errors) => console.error('Erro ao redirecionar:', errors),
            onSuccess: () => console.log('Redirecionamento bem-sucedido para create'),
        }
    );
};

// Exibir campanhas ao clicar em "Visualizar"
const visualizarCampanhas = () => {
    showCampanhas.value = true;
};

// Redirecionar para a visualização de uma campanha específica
const visualizarCampanha = (campanhaId) => {
    router.get(route('sgc.contratada.produtos.show', [props.contrato, props.produto.toLowerCase(), campanhaId]));
};

// Redirecionar para a análise de uma campanha específica
const analisarCampanha = (campanhaId) => {
    router.get(route('sgc.contratada.produtos.analise', [props.contrato, props.produto.toLowerCase(), campanhaId]));
};

// Redirecionar para a edição de uma campanha específica
const editarCampanha = (campanhaId) => {
    router.get(route('sgc.contratada.produtos.edit', [props.contrato, props.produto.toLowerCase(), campanhaId]));
};
</script>

<template>
    <AuthenticatedLayout>
        <Head :title="`${produto} - Contrato ${contrato}`" />

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
                        <h2 class="text-center mb-4">FAUNA</h2>
                        <div v-if="!subprodutos.length" class="alert alert-danger">
                            Nenhum dado encontrado para {{ produto }}.
                        </div>
                        <div v-else class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <!-- Filtro -->
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
                                    <div class="col-md-8 mb-4">
                                        <div class="row">
                                            <div class="col-md-4 mb-4">
                                                <div class="block-card block-card-short action-button bg-primary text-white cursor-pointer" @click="goToCreate">
                                                    Cadastrar
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-4">
                                                <div
                                                    class="block-card block-card-short action-button bg-success text-white"
                                                    :class="{ 'cursor-not-allowed': !canApprove, 'cursor-pointer': canApprove }"
                                                    :title="!canApprove ? 'Nenhuma campanha em análise ou acesso negado' : 'Analisar campanhas'"
                                                    @click="canApprove && visualizarCampanhas()"
                                                >
                                                    Analisar
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-4">
                                                <div class="block-card block-card-short action-button bg-info text-white cursor-pointer" @click="visualizarCampanhas">
                                                    Visualizar
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tabela de Campanhas -->
                            <div v-if="showCampanhas" class="col-md-12 mt-4">
                                <div class="block-card">
                                    <h4 class="text-center mb-4">CAMPANHAS DE {{ produto.toUpperCase() }}</h4>
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
                                                <tr v-for="campanha in filteredCampanhas" :key="campanha.id">
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
                                                            v-if="campanha.status === 'Rejeitada' && (props.auth.user.perfis_id ?? 0) !== 2"
                                                            type-button="warning"
                                                            title="Editar"
                                                            @click="editarCampanha(campanha.id)"
                                                        />
                                                    </td>
                                                </tr>
                                                <tr v-if="!filteredCampanhas.length">
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
    background-color: #ffc107 !important; /* Estilo para o botão Editar */
}

.action-button.bg-warning:not(.cursor-not-allowed):hover {
    background-color: #e0a800 !important; /* Hover para warning */
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
</style>