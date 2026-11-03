<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import BadgeSituacao from "./Partials/BadgeSituacao.vue";
import { formatarData } from "./Partials/utils.js";
import { Head, Link, router } from "@inertiajs/vue3";
import { computed, ref } from "vue";
import { IconPlus } from "@tabler/icons-vue";

const props = defineProps({
    ano: Number,
    anos: Array,
    siglas: Array,
    linhas: Array,
});

const anoSelecionado = ref(props.ano);
const somenteComAo = ref(false);

const trocarAno = () => router.get(route('sgc.licencas.regularizacao'), { ano: anoSelecionado.value }, { preserveState: true });

const linhasVisiveis = computed(() => somenteComAo.value ? props.linhas.filter(l => l.aos.length) : props.linhas);

// Resumo do ano por relatório: quantas UFs cumpriram, estão pendentes ou vencidas
const resumo = computed(() => props.siglas.map(sigla => {
    const prazos = props.linhas.flatMap(l => l.aos.flatMap(ao => ao.relatorios[sigla] ?? []));
    const contar = (valores) => prazos.filter(p => valores.includes(p.situacao.valor)).length;
    return {
        sigla,
        total: prazos.length,
        cumpridos: contar(['cumprido', 'cumprido_atraso', 'dispensado']),
        pendentes: contar(['no_prazo', 'a_vencer']),
        vencidos: contar(['vencido']),
    };
}));

const totalComAo = computed(() => props.linhas.filter(l => l.aos.length).length);
</script>

<template>
    <Head title="Regularização ambiental" />

    <AuthenticatedLayout>
        <template #header>
            <div class="w-100 d-flex flex-wrap gap-2 justify-content-between">
                <Breadcrumb class="align-self-center" :links="[
                    { route: route('sgc.licencas.painel'), label: 'Gestão de Prazos' },
                    { route: '#', label: 'Regularização ambiental (AO, RCOI, RARA)' },
                ]" />
                <Link class="btn btn-info" :href="route('sgc.licencas.create', { regularizacao: 1 })">
                    <IconPlus class="me-1" /> Nova AO
                </Link>
            </div>
        </template>

        <div class="card card-body mb-3">
            <div class="row g-3 align-items-end">
                <div class="col-6 col-md-2">
                    <label class="form-label">Ano de entrega</label>
                    <select class="form-select" v-model="anoSelecionado" @change="trocarAno">
                        <option v-for="a in anos" :key="a" :value="a">{{ a }}</option>
                    </select>
                </div>
                <div class="col-6 col-md-3">
                    <label class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" v-model="somenteComAo">
                        <span class="form-check-label">Somente UFs com AO</span>
                    </label>
                </div>
                <div class="col-12 col-md-7 d-flex flex-wrap gap-3 justify-content-md-end">
                    <div class="text-secondary align-self-center">{{ totalComAo }} de {{ linhas.length }} UFs com AO ativa</div>
                    <div v-for="r in resumo" :key="r.sigla" class="border rounded px-3 py-2">
                        <div class="fw-bold">{{ r.sigla }} {{ ano }}</div>
                        <div class="small">
                            <span class="text-success">{{ r.cumpridos }} entregue(s)</span> ·
                            <span class="text-secondary">{{ r.pendentes }} pendente(s)</span> ·
                            <span class="text-danger">{{ r.vencidos }} vencido(s)</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="table-responsive">
                <table class="table card-table table-vcenter table-bordered">
                    <thead>
                        <tr>
                            <th class="text-center">UF</th>
                            <th>Autorização de Operação</th>
                            <th>Validade</th>
                            <th>Situação</th>
                            <th v-for="sigla in siglas" :key="sigla" class="text-center">{{ sigla }} {{ ano }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="!siglas.length && !totalComAo">
                            <td :colspan="4" class="text-center text-secondary py-4">
                                Nenhuma AO cadastrada. Cadastre a AO e suas condicionantes de relatório periódico
                                (use os modelos RCOI e RARA no cadastro da condicionante).
                            </td>
                        </tr>
                        <template v-for="linha in linhasVisiveis" :key="linha.uf">
                            <tr v-if="!linha.aos.length">
                                <td class="text-center"><span class="badge bg-secondary-lt">{{ linha.uf }}</span></td>
                                <td :colspan="3 + siglas.length" class="text-secondary">
                                    Sem AO cadastrada
                                    <Link class="ms-2" :href="route('sgc.licencas.create', { regularizacao: 1, uf_id: linha.uf_id })">Cadastrar</Link>
                                </td>
                            </tr>
                            <tr v-for="ao in linha.aos" :key="ao.id">
                                <td class="text-center"><span class="badge bg-warning text-white">{{ linha.uf }}</span></td>
                                <td class="text-nowrap">
                                    <Link :href="route('sgc.licencas.show', ao.id)">{{ ao.sigla }} nº {{ ao.numero }}</Link>
                                    <div class="small text-secondary">Emitida em {{ formatarData(ao.data_emissao) }}</div>
                                </td>
                                <td class="text-nowrap">{{ formatarData(ao.data_validade) || '-' }}</td>
                                <td><BadgeSituacao :situacao="ao.situacao" /></td>
                                <td v-for="sigla in siglas" :key="sigla" class="text-center">
                                    <template v-if="ao.relatorios[sigla]?.length">
                                        <div v-for="prazo in ao.relatorios[sigla]" :key="prazo.id">
                                            <Link :href="route('sgc.licencas.show', ao.id)" class="text-reset">
                                                <BadgeSituacao :situacao="prazo.situacao" />
                                            </Link>
                                            <div class="small text-secondary">
                                                <template v-if="prazo.data_cumprimento">entregue {{ formatarData(prazo.data_cumprimento) }}</template>
                                                <template v-else>até {{ formatarData(prazo.data_limite) }}</template>
                                                <span v-if="prazo.prorrogado" class="text-azure"> (prorrogado)</span>
                                            </div>
                                        </div>
                                    </template>
                                    <span v-else class="text-secondary">-</span>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
