<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import Table from "@/Components/Table.vue";
import BadgeSituacao from "./Partials/BadgeSituacao.vue";
import { formatarData } from "./Partials/utils.js";
import { Head, Link, router } from "@inertiajs/vue3";
import { reactive } from "vue";
import { IconEraser, IconPlus, IconSearch } from "@tabler/icons-vue";

const props = defineProps({
    licencas: Object,
    filtros: Object,
    tipos: Array,
    ufs: Array,
});

const filtro = reactive({
    busca: props.filtros.busca ?? '',
    tipo_id: props.filtros.tipo_id ?? '',
    uf_id: props.filtros.uf_id ?? '',
    status: props.filtros.status ?? 'ativa',
    regularizacao: props.filtros.regularizacao ?? '',
});

const filtrar = () => {
    const params = Object.fromEntries(Object.entries(filtro).filter(([, v]) => v !== '' && v !== null));
    router.get(route('sgc.licencas.index'), params, { preserveState: true });
};

const limpar = () => {
    Object.assign(filtro, { busca: '', tipo_id: '', uf_id: '', status: 'ativa', regularizacao: '' });
    filtrar();
};
</script>

<template>
    <Head title="Licenças e autorizações" />

    <AuthenticatedLayout>
        <template #header>
            <div class="w-100 d-flex flex-wrap gap-2 justify-content-between">
                <Breadcrumb class="align-self-center" :links="[
                    { route: route('sgc.licencas.painel'), label: 'Gestão de Prazos' },
                    { route: '#', label: 'Licenças e autorizações' },
                ]" />
                <Link class="btn btn-info" :href="route('sgc.licencas.create')">
                    <IconPlus class="me-1" /> Nova licença
                </Link>
            </div>
        </template>

        <div class="card card-body space-y-3">
            <form class="row g-2 align-items-end" @submit.prevent="filtrar">
                <div class="col-12 col-lg-3">
                    <label class="form-label">Pesquisar</label>
                    <input class="form-control" v-model="filtro.busca" placeholder="Número, órgão, empreendimento, processo">
                </div>
                <div class="col-6 col-lg-2">
                    <label class="form-label">Categoria</label>
                    <select class="form-select" v-model="filtro.regularizacao">
                        <option value="">Todas</option>
                        <option value="0">Licenças e autorizações</option>
                        <option value="1">Regularização (AO)</option>
                    </select>
                </div>
                <div class="col-6 col-lg-2">
                    <label class="form-label">Tipo</label>
                    <select class="form-select" v-model="filtro.tipo_id">
                        <option value="">Todos</option>
                        <option v-for="tipo in tipos" :key="tipo.id" :value="tipo.id">{{ tipo.sigla }} - {{ tipo.nome }}</option>
                    </select>
                </div>
                <div class="col-6 col-lg-1">
                    <label class="form-label">UF</label>
                    <select class="form-select" v-model="filtro.uf_id">
                        <option value="">Todas</option>
                        <option v-for="uf in ufs" :key="uf.id" :value="uf.id">{{ uf.uf }}</option>
                    </select>
                </div>
                <div class="col-6 col-lg-2">
                    <label class="form-label">Status</label>
                    <select class="form-select" v-model="filtro.status">
                        <option value="ativa">Ativas</option>
                        <option value="substituida">Substituídas</option>
                        <option value="arquivada">Arquivadas</option>
                        <option value="todas">Todas</option>
                    </select>
                </div>
                <div class="col-12 col-lg-2 text-end">
                    <button type="button" class="btn btn-secondary me-2" title="Limpar filtros" @click="limpar">
                        <IconEraser />
                    </button>
                    <button class="btn btn-primary" title="Pesquisar">
                        <IconSearch />
                    </button>
                </div>
            </form>

            <Table :columns="['UF', 'Licença', 'Órgão', 'Empreendimento', 'Emissão', 'Validade', 'Requerer renovação até', 'Situação', 'Prazos vencidos']"
                :records="licencas" table-class="table-hover table-vcenter">
                <template #body="{ item }">
                    <tr class="cursor-pointer" @click="router.visit(route('sgc.licencas.show', item.id))">
                        <td class="text-center"><span v-if="item.uf" class="badge bg-warning text-white">{{ item.uf }}</span></td>
                        <td class="text-nowrap">
                            <Link :href="route('sgc.licencas.show', item.id)" @click.stop>
                                {{ item.tipo?.sigla }} nº {{ item.numero }}
                            </Link>
                        </td>
                        <td>{{ item.orgao_emissor }}</td>
                        <td>{{ item.empreendimento }}</td>
                        <td class="text-nowrap">{{ formatarData(item.data_emissao) }}</td>
                        <td class="text-nowrap">{{ formatarData(item.data_validade) || 'Sem validade' }}</td>
                        <td class="text-nowrap">{{ formatarData(item.prazo_renovacao) }}</td>
                        <td><BadgeSituacao :situacao="item.situacao" /></td>
                        <td class="text-center">
                            <span v-if="item.prazos_vencidos_count" class="badge bg-danger text-white">{{ item.prazos_vencidos_count }}</span>
                            <span v-else class="text-secondary">-</span>
                        </td>
                    </tr>
                </template>
            </Table>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.cursor-pointer {
    cursor: pointer;
}
</style>
