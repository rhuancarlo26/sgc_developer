<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import BadgeSituacao from "./Partials/BadgeSituacao.vue";
import { formatarData, textoDias } from "./Partials/utils.js";
import { Head, Link, router } from "@inertiajs/vue3";
import { computed, reactive } from "vue";
import { IconAlertTriangle, IconClock, IconCalendarDue, IconFileText, IconPlus, IconMap, IconEraser } from "@tabler/icons-vue";

const props = defineProps({
    indicadores: Object,
    prazos: Array,
    cumpridos_recentes: Array,
    janela: Number,
    filtros: Object,
    ufs: Array,
    orgaos: Array,
});

const filtro = reactive({
    regularizacao: props.filtros.regularizacao ?? '',
    uf_id: props.filtros.uf_id ?? '',
    orgao_emissor: props.filtros.orgao_emissor ?? '',
});

const filtrar = () => {
    const params = Object.fromEntries(Object.entries(filtro).filter(([, v]) => v !== '' && v !== null));
    router.get(route('sgc.licencas.painel'), params, { preserveState: true, preserveScroll: true });
};

const limpar = () => {
    Object.assign(filtro, { regularizacao: '', uf_id: '', orgao_emissor: '' });
    filtrar();
};

const vencidos = computed(() => props.prazos.filter(p => p.dias_restantes < 0));
const proximos = computed(() => props.prazos.filter(p => p.dias_restantes >= 0));

const cards = computed(() => [
    { titulo: 'Prazos vencidos', valor: props.indicadores.vencidos, cor: 'danger', icone: IconAlertTriangle },
    { titulo: `A vencer (${props.janela} dias)`, valor: props.indicadores.a_vencer, cor: 'warning', icone: IconClock },
    { titulo: 'Próximos 90 dias', valor: props.indicadores.proximos_90, cor: 'azure', icone: IconCalendarDue },
    { titulo: 'Licenças ativas', valor: props.indicadores.licencas_ativas, cor: 'success', icone: IconFileText },
    { titulo: 'Licenças vencidas', valor: props.indicadores.licencas_vencidas, cor: 'danger', icone: IconAlertTriangle },
    { titulo: 'Renovações próximas', valor: props.indicadores.renovacoes_proximas, cor: 'warning', icone: IconClock },
]);
</script>

<template>
    <Head title="Painel de prazos" />

    <AuthenticatedLayout>
        <template #header>
            <div class="w-100 d-flex flex-wrap gap-2 justify-content-between">
                <Breadcrumb class="align-self-center" :links="[
                    { route: '#', label: 'Gestão de Prazos' },
                    { route: '#', label: 'Painel de prazos' },
                ]" />
                <div class="d-flex gap-2">
                    <Link class="btn btn-outline-info" :href="route('sgc.licencas.regularizacao')">
                        <IconMap class="me-1" /> Regularização por UF
                    </Link>
                    <Link class="btn btn-info" :href="route('sgc.licencas.create')">
                        <IconPlus class="me-1" /> Nova licença
                    </Link>
                </div>
            </div>
        </template>

        <!-- Filtros -->
        <div class="card card-body mb-3">
            <form class="row g-2 align-items-end" @submit.prevent="filtrar">
                <div class="col-12 col-md-4">
                    <label class="form-label">Categoria</label>
                    <select class="form-select" v-model="filtro.regularizacao" @change="filtrar">
                        <option value="">Todas</option>
                        <option value="0">Licenças e autorizações</option>
                        <option value="1">Regularização ambiental (AO)</option>
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label">UF</label>
                    <select class="form-select" v-model="filtro.uf_id" @change="filtrar">
                        <option value="">Todas</option>
                        <option v-for="uf in ufs" :key="uf.id" :value="uf.id">{{ uf.uf }}</option>
                    </select>
                </div>
                <div class="col-6 col-md-4">
                    <label class="form-label">Órgão emissor</label>
                    <select class="form-select" v-model="filtro.orgao_emissor" @change="filtrar">
                        <option value="">Todos</option>
                        <option v-for="orgao in orgaos" :key="orgao" :value="orgao">{{ orgao }}</option>
                    </select>
                </div>
                <div class="col-12 col-md-2 text-end">
                    <button type="button" class="btn btn-secondary" title="Limpar filtros" @click="limpar">
                        <IconEraser />
                    </button>
                </div>
            </form>
        </div>

        <!-- Indicadores -->
        <div class="row row-cards mb-3">
            <div class="col-6 col-md-4 col-xl-2" v-for="card in cards" :key="card.titulo">
                <div class="card card-sm h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <span class="avatar" :class="`bg-${card.cor}-lt`">
                            <component :is="card.icone" />
                        </span>
                        <div>
                            <div class="h2 mb-0">{{ card.valor }}</div>
                            <div class="text-secondary small">{{ card.titulo }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row row-cards">
            <!-- Prazos pendentes -->
            <div class="col-12 col-xl-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Prazos pendentes (vencidos e próximos 90 dias)</h3>
                    </div>
                    <div class="table-responsive">
                        <table class="table card-table table-vcenter table-hover">
                            <thead>
                                <tr>
                                    <th>Data-limite</th>
                                    <th>Prazo</th>
                                    <th>Licença</th>
                                    <th>Situação</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="!prazos.length">
                                    <td colspan="4" class="text-center text-secondary py-4">Nenhum prazo pendente no período.</td>
                                </tr>
                                <template v-for="(grupo, g) in [vencidos, proximos]" :key="g">
                                    <tr v-for="prazo in grupo" :key="prazo.id">
                                        <td class="text-nowrap">
                                            <div class="fw-bold">{{ formatarData(prazo.data_limite) }}</div>
                                            <div class="small" :class="prazo.dias_restantes < 0 ? 'text-danger' : 'text-secondary'">
                                                {{ textoDias(prazo.dias_restantes) }}
                                            </div>
                                        </td>
                                        <td>
                                            {{ prazo.descricao }}
                                            <span v-if="prazo.prorrogado" class="badge bg-azure-lt ms-1">prorrogado</span>
                                        </td>
                                        <td class="text-nowrap">
                                            <Link :href="route('sgc.licencas.show', prazo.licenca_id)">{{ prazo.licenca?.rotulo }}</Link>
                                            <div class="small text-secondary">{{ prazo.licenca?.uf }} · {{ prazo.licenca?.orgao_emissor }}</div>
                                        </td>
                                        <td><BadgeSituacao :situacao="prazo.situacao" /></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Cumpridos recentes -->
            <div class="col-12 col-xl-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Cumpridos nos últimos 30 dias</h3>
                    </div>
                    <div class="list-group list-group-flush">
                        <div v-if="!cumpridos_recentes.length" class="list-group-item text-secondary">Nenhum cumprimento recente.</div>
                        <Link v-for="prazo in cumpridos_recentes" :key="prazo.id" class="list-group-item list-group-item-action"
                            :href="route('sgc.licencas.show', prazo.licenca_id)">
                            <div class="d-flex justify-content-between gap-2">
                                <span class="fw-bold">{{ prazo.licenca?.rotulo }}</span>
                                <span class="small text-secondary text-nowrap">{{ formatarData(prazo.data_cumprimento) }}</span>
                            </div>
                            <div class="small">{{ prazo.descricao }}</div>
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
