<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import NavbarContrato from '@/Pages/Sgc/Contratada/NavbarContrato.vue';
import { ref, computed } from 'vue';

const props = defineProps({
    subprodutos: Array,
    contrato: [Number, String],
    produto: String,
    contratos: Object
});

const selectedSubproduto = ref('');
const uniqueSubprodutos = computed(() => {
    const descriptions = props.subprodutos.map(sub => sub.descricao_revisada).filter(desc => desc);
    return [...new Set(descriptions)];
});

const handleFilter = () => {
    console.log('Filtro selecionado:', selectedSubproduto.value);
};

const handleAction = (action) => {
    console.log(`Ação ${action} clicada - Contrato: ${props.contrato}, Produto: ${props.produto}`);
};

const filteredSubprodutos = computed(() => {
    if (!selectedSubproduto.value) return props.subprodutos;
    return props.subprodutos.filter(sub => sub.descricao_revisada === selectedSubproduto.value);
});
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
                        <h2 class="text-center mb-4">ESPELEOLOGIA</h2>
                        <div v-if="!subprodutos || subprodutos.length === 0" class="alert alert-danger">
                            Nenhum dado encontrado para {{ produto }}.
                        </div>
                        <div v-else class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <!-- Filtro -->
                                    <div class="col-md-4 mb-4">
                                        <div class="block-card block-card-short">
                                            <h4 class="text-center mb-2">ESCOLHER SUBPRODUTO</h4>
                                            <select v-model="selectedSubproduto" class="form-select" @change="handleFilter">
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
                                                <div class="block-card block-card-short action-button bg-primary text-white cursor-pointer" @click="handleAction('cadastrar')">
                                                    Cadastrar
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-4">
                                                <div class="block-card block-card-short action-button bg-success text-white cursor-pointer" @click="handleAction('analisar')">
                                                    Analisar
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-4">
                                                <div class="block-card block-card-short action-button bg-info text-white cursor-pointer" @click="handleAction('visualizar')">
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
                                            <strong>Contrato:</strong> {{ subproduto.contrato || 'N/A' }} |
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
    min-height: 100px; /* Reduz a altura mínima */
    padding: 10px; /* Reduz o padding interno */
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

.form-select {
    width: 100%;
    padding: 0.5rem;
}

/* Estilo específico para os botões */
.action-button {
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem; /* Aumenta o tamanho do texto */
    font-weight: 500;
    text-align: center;
    border-radius: 8px; /* Bordas mais arredondadas */
    transition: transform 0.2s ease, background-color 0.3s ease;
}

.action-button:hover {
    transform: scale(1.05); /* Efeito de leve aumento ao passar o mouse */
    background-color: #e9ecef;
}
</style>
