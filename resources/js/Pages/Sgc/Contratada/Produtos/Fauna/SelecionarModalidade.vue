<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import NavbarContrato from '@/Pages/Sgc/Contratada/NavbarContrato.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import { Head, router } from '@inertiajs/vue3';

const props = defineProps({
    contrato: [Number, String],
    produto: String,
    contratos: Object,
    subproduto: String,
});

const selecionar = (modo) => router.get(
    route('sgc.contratada.produtos.create', [props.contrato, 'fauna']),
    { subproduto: props.subproduto, modo },
);
</script>

<template>
    <Head title="Modalidade da campanha de fauna" />
    <AuthenticatedLayout>
        <template #header>
            <Breadcrumb :links="[{ route: route('sgc.contratada.produtos.index', [contrato, 'fauna']), label: 'Produtos' }, { route: '#', label: 'Nova campanha de fauna' }]" />
        </template>
        <NavbarContrato :tipo="contratos">
          <template #body>
            <div class="p-3">
                <h2 class="mb-2">Nova campanha de fauna</h2>
                <p class="text-muted mb-4">Escolha a modalidade de preenchimento para <strong>{{ subproduto }}</strong>.</p>
                <div class="row g-4">
                    <div class="col-md-6">
                        <button class="card h-100 w-100 text-start modalidade" @click="selecionar('completo')">
                            <div class="card-body p-4">
                                <h3>Campanha completa</h3>
                                <p class="mb-0 text-muted">Preencha todas as etapas, módulos amostrais, metodologias, resultados e anexos do módulo de Fauna.</p>
                            </div>
                        </button>
                    </div>
                    <div class="col-md-6">
                        <button class="card h-100 w-100 text-start modalidade" @click="selecionar('simplificado')">
                            <div class="card-body p-4">
                                <h3>Campanha simplificada</h3>
                                <p class="mb-0 text-muted">Envie a planilha do modelo, fotos e anexos, no mesmo fluxo enxuto usado pelos produtos simplificados.</p>
                            </div>
                        </button>
                    </div>
                </div>
            </div>
          </template>
        </NavbarContrato>
    </AuthenticatedLayout>
</template>

<style scoped>
.modalidade { border: 1px solid #dfe4ea; background: #fff; transition: .2s ease; }
.modalidade:hover { border-color: #1fa050; box-shadow: 0 .5rem 1rem rgba(0,0,0,.1); transform: translateY(-2px); }
</style>
