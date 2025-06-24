<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import NavbarContrato from '@/Pages/Sgc/Contratada/NavbarContrato.vue';
import { ref, computed } from 'vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';

const props = defineProps({
    subprodutos: { type: Array, default: () => [] },
    contrato: { type: [Number, String], required: true },
    produto: { type: String, required: true },
    contratos: { type: Object, required: true },
});

// Estado reativo para o subproduto selecionado
const selectedSubproduto = ref('');

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
                                            <!-- Botões desativados até implementação -->
                                            <div class="col-md-4 mb-4">
                                                <div class="block-card block-card-short action-button bg-success text-white cursor-not-allowed" title="Funcionalidade em desenvolvimento">
                                                    Analisar
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-4">
                                                <div class="block-card block-card-short action-button bg-info text-white cursor-not-allowed" title="Funcionalidade em desenvolvimento">
                                                    Visualizar
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Lista de Subprodutos -->
                            <div class="col-md-12 mt-4">
                                <div class="block-card">
                                    <h4 class="text-center mb-4">{{ produto.toUpperCase() }} DETALHES</h4>
                                    <ul class="list-group">
                                        <li v-for="subproduto in filteredSubprodutos" :key="subproduto.id" class="list-group-item">
                                            <strong>ID:</strong> {{ subproduto.id }} |
                                            <strong>Código SIAC:</strong> {{ subproduto.cod_siac || 'N/A' }} |
                                            <strong>Descrição:</strong> {{ subproduto.descricao_revisada || 'N/A' }} |
                                            <strong>Contrato:</strong> {{ subproduto.contrato_id || 'N/A' }} |
                                            <strong>Família:</strong> {{ subproduto.familia || 'N/A' }}
                                        </li>
                                    </ul>
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
    background-color: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 5px;
    padding: 15px;
    min-height: 200px;
    transition: all 0.3s ease;
}

.block-card:hover {
    background-color: #e9ecef;
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

.list-group-item {
    margin-bottom: 5px;
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
</style>