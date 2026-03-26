<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import NavbarContrato from '@/Pages/Sgc/Contratada/NavbarContrato.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import NavButton from '@/Components/NavButton.vue';
import { ref, computed } from 'vue';

const props = defineProps({
    subprodutos: { type: Array, default: () => [] },
    contrato: { type: [Number, String], required: true },
    produto: { type: String, required: true },
    contratos: { type: Object, required: true },
    campanhas: { type: Array, default: () => [] },
});

const produtos = [
    { title: 'Fauna', routeParam: 'fauna' },
    { title: 'Espeleologia', routeParam: 'espeleologia' },
    { title: 'Patrimonio', routeParam: 'patrimonio' },
    { title: 'Indigena', routeParam: 'indigena' },
    { title: 'Quilombola', routeParam: 'quilombola' },
    { title: 'Malarigeno', routeParam: 'malarigeno' },
    { title: 'Eia', routeParam: 'eia' },
    { title: 'Rima', routeParam: 'rima' },
    { title: 'Audiencia', routeParam: 'audiencia' },
    { title: 'Pba', routeParam: 'pba' },
    { title: 'Asv', routeParam: 'asv' },
    { title: 'Viagens', routeParam: 'viagens' },
];

const selectedProduto = ref(props.produto.toLowerCase());
const selectedSubproduto = ref('');

const updateProduto = () => {
    router.get(route('sgc.contratada.produtos.index', [props.contrato, selectedProduto.value]));
};

const uniqueSubprodutos = computed(() => {
    const descriptions = props.subprodutos.map((sub) => sub.descricao_revisada).filter((desc) => desc);
    return [...new Set(descriptions)];
});

const campanhasFiltradas = computed(() => {
    if (!selectedSubproduto.value) {
        return props.campanhas;
    }
    return props.campanhas.filter((campanha) => campanha.subproduto === selectedSubproduto.value);
});

const goToCreate = () => {
    if (!selectedSubproduto.value) {
        alert('Selecione um subproduto antes de cadastrar.');
        return;
    }

    router.get(
        route('sgc.contratada.produtos.create', [props.contrato, 'espeleologia']),
        { subproduto: selectedSubproduto.value }
    );
};

const visualizarCampanha = (campanhaId) => {
    router.get(route('sgc.contratada.produtos.espeleo.show', [props.contrato, 'espeleologia', campanhaId]));
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
                        <h2 class="text-center mb-4">ESPELEOLOGIA</h2>
                        <div v-if="!subprodutos.length" class="alert alert-danger">
                            Nenhum dado encontrado para Espeleologia.
                        </div>
                        <div v-else class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-4 mb-4">
                                        <div class="block-card block-card-short">
                                            <h4 class="text-center mb-2">ESCOLHER PRODUTO</h4>
                                            <select v-model="selectedProduto" class="form-select" @change="updateProduto">
                                                <option v-for="item in produtos" :key="item.routeParam" :value="item.routeParam">
                                                    {{ item.title }}
                                                </option>
                                            </select>
                                        </div>
                                    </div>

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

                                    <div class="col-md-4 mb-4">
                                        <div class="row">
                                            <div class="col-md-12 mb-4">
                                                <div class="block-card block-card-short action-button bg-primary text-white cursor-pointer" @click="goToCreate">
                                                    Cadastrar
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12 mt-4">
                                <div class="block-card">
                                    <h4 class="text-center mb-4">CAMPANHAS DE ESPELEOLOGIA</h4>
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th class="text-center">ID Campanha</th>
                                                    <th class="text-center">Empreendimento</th>
                                                    <th class="text-center">Subproduto</th>
                                                    <th class="text-center">Status</th>
                                                    <th class="text-center">Ação</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="campanha in campanhasFiltradas" :key="campanha.id">
                                                    <td class="text-center">{{ campanha.id || 'N/A' }}</td>
                                                    <td class="text-center">{{ campanha.empreendimento || 'N/A' }}</td>
                                                    <td class="text-center">{{ campanha.subproduto || 'N/A' }}</td>
                                                    <td class="text-center">{{ campanha.status || 'N/A' }}</td>
                                                    <td class="text-center">
                                                        <NavButton
                                                            type-button="info"
                                                            title="Visualizar"
                                                            @click="visualizarCampanha(campanha.id)"
                                                        />
                                                    </td>
                                                </tr>
                                                <tr v-if="!campanhasFiltradas.length">
                                                    <td colspan="5" class="text-center">Nenhuma campanha disponível.</td>
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

.table th, .table td {
    vertical-align: middle;
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
