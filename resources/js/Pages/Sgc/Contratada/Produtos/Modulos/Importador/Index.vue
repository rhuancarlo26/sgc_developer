<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import {
    IconCirclePlus,
    IconEye,
    IconTrash,
    IconAlertTriangle,
    IconDoorExit,
    IconSearch,
    IconX,
} from "@tabler/icons-vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import Table from "@/Components/Table.vue";
import { dateTimeFormat } from "@/Utils/DateTimeUtils";
import { badgeStatus } from "@/Utils/ImportadorUtils";

import ModalErros from "./Components/ModalErros.vue";
import { computed, ref } from "vue";

const props = defineProps({
    modulos: Object,
    importadores: Object,
    contextoImportador: {
        type: Object,
        default: () => ({}),
    },
    campanhasDisponiveis: {
        type: Array,
        default: () => [],
    },
    modulosFiltro: {
        type: Array,
        default: () => [],
    },
    temasFiltro: {
        type: Array,
        default: () => [],
    },
    campanhasFiltro: {
        type: Array,
        default: () => [],
    },
    filtros: {
        type: Object,
        default: () => ({}),
    },
});

const ModalErrosRef = ref(null);

const abrirModalErros = (erros) => {
    ModalErrosRef.value.abrirModal(erros);
};

const toNumberOrNull = (valor) => {
    return valor === null || valor === undefined || valor === ""
        ? null
        : Number(valor);
};

const filtrosForm = ref({
    filtro_modulo_id: toNumberOrNull(props.filtros?.filtro_modulo_id),
    filtro_tema_id: toNumberOrNull(props.filtros?.filtro_tema_id),
    campanha: toNumberOrNull(props.filtros?.campanha),
    updated_at: props.filtros?.updated_at ?? null,
});

const servicosUrl = computed(() => {
    if (!props.contextoImportador?.contrato_id) {
        return route("modulos.importador.index");
    }

    return route("contratos.contratada.servicos.index", props.contextoImportador.contrato_id);
});

const contextoFormulario = computed(() => ({
    contrato_id: props.contextoImportador.contrato_id,
    modulo_id: props.contextoImportador.modulo_id,
    servico_id: props.contextoImportador.servico_id,
    tema_id: props.contextoImportador.tema_id,
    origem_servico: props.contextoImportador.origem_servico,
}));

const limparParametrosVazios = (params) => {
    return Object.fromEntries(
        Object.entries(params).filter(([_, value]) => {
            return value !== null && value !== undefined && value !== "";
        })
    );
};

const montarParametros = (comFiltros = true) => {
    const params = {
        ...contextoFormulario.value,
    };

    if (comFiltros) {
        params.filtro_modulo_id = filtrosForm.value.filtro_modulo_id;
        params.filtro_tema_id = filtrosForm.value.filtro_tema_id;
        params.campanha = filtrosForm.value.campanha;
        params.updated_at = filtrosForm.value.updated_at;
    }

    return limparParametrosVazios(params);
};

const pesquisar = () => {
    router.get(
        route("modulos.importador.index"),
        montarParametros(true),
        {
            preserveScroll: true,
            preserveState: true,
        }
    );
};

const limparFiltros = () => {
    filtrosForm.value = {
        filtro_modulo_id: null,
        filtro_tema_id: null,
        campanha: null,
        updated_at: null,
    };

    router.get(
        route("modulos.importador.index"),
        montarParametros(false),
        {
            preserveScroll: true,
            preserveState: false,
        }
    );
};

const removerImportacao = (id) => {
    router.delete(route("modulos.importador.destroy", [id]));
};
</script>

<template>

    <Head title="Módulo Importador" />

    <AuthenticatedLayout>
        <template #header>
            <div class="w-100 d-flex justify-content-between align-items-center">
                <Breadcrumb class="align-self-center" :links="[
                    {
                        route: servicosUrl,
                        label: 'Serviços'
                    },
                    {
                        route: '#',
                        label: 'Módulo Importador'
                    }
                ]" />

                <Link class="btn btn-info" :href="servicosUrl">
                    <IconDoorExit class="me-2" />
                    Voltar
                </Link>
            </div>
        </template>

        <div class="card card-body">
            <div class="row align-items-end g-3 mb-4">

                <div class="col-lg-2">
                    <label class="form-label fw-bold">Tema</label>

                    <v-select v-model="filtrosForm.filtro_tema_id" :options="temasFiltro" :reduce="option => option.id"
                        label="nome_tema" placeholder="Selecione o tema">
                        <template #no-options>
                            Nenhum tema encontrado.
                        </template>
                    </v-select>
                </div>

                <div class="col-lg-3">
                    <label class="form-label fw-bold">Módulo</label>

                    <v-select v-model="filtrosForm.filtro_modulo_id" :options="modulosFiltro"
                        :reduce="option => option.id" label="nome" placeholder="Selecione o módulo">
                        <template #no-options>
                            Nenhum módulo encontrado.
                        </template>
                    </v-select>
                </div>

                <div class="col-lg-2">
                    <label class="form-label fw-bold">Campanha</label>

                    <v-select v-model="filtrosForm.campanha" :options="campanhasFiltro"
                        placeholder="Selecione a campanha">
                        <template #no-options>
                            Nenhuma campanha encontrada.
                        </template>
                    </v-select>
                </div>

                <div class="col-lg-2">
                    <label class="form-label fw-bold">Data de atualização</label>

                    <input type="date" class="form-control" v-model="filtrosForm.updated_at" />
                </div>

                <div class="col-lg-auto d-flex gap-2">
                    <button type="button" class="btn btn-primary" title="Pesquisar" @click="pesquisar">
                        <IconSearch />
                    </button>

                    <button type="button" class="btn btn-outline-danger" title="Limpar filtros" @click="limparFiltros">
                        <IconX />
                    </button>
                </div>

                <div class="col-lg-auto ms-auto d-flex justify-content-end">
                    <Link :href="route('modulos.importador.formulario', contextoFormulario)" class="btn btn-info">
                        <IconCirclePlus class="me-2" />
                        Nova Importação
                    </Link>
                </div>
            </div>

            <Table :columns="[
                'Tema',
                'Modulo',
                'Contrato',
                'Mes/Ano Referencia',
                'Status',
                'Revisão',
                'Atualizado em',
                'Campanha',
                'Ações'
            ]" :records="importadores" table-class="table-hover">
                <template #body="{ item }">
                    <tr class="cursor-pointer">
                        <td class="text-center">
                            {{ item.servico.tema.nome_tema }}
                        </td>
                        <td class="text-center">
                            {{ item.modulo?.nome ?? '-' }}
                        </td>

                        <td class="text-center">
                            {{ item.contrato?.numero_contrato ?? '-' }}
                        </td>

                        <td class="text-center">
                            {{ item.mes_ano_referencia }}
                        </td>

                        <td class="text-center">
                            <span class="badge" :class="badgeStatus(item.status)">
                                {{ item.status_formatado }}
                            </span>
                        </td>

                        <td class="text-center">
                            {{ item.revisao }}
                        </td>

                        <td class="text-center">
                            {{ dateTimeFormat(item.updated_at) }}
                        </td>

                        <td class="text-center">
                            {{ item.campanha }}
                        </td>

                        <td class="text-center">
                            <div class="d-flex gap-2 justify-content-center">
                                <button v-if="item.load" type="button" class="d-flex gap-2 btn btn-sm btn-primary">
                                    Importando

                                    <div class="spinner-border spinner-border-sm text-light" role="status"
                                        style="border-width: 3px">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                </button>

                                <template v-else>
                                    <button v-if="item.desc_erros" type="button"
                                        @click="abrirModalErros(item.desc_erros)" class="btn btn-sm btn-warning"
                                        title="Ver erros">
                                        <IconAlertTriangle />
                                    </button>

                                    <Link :href="route('modulos.importador.formulario', {
                                        importador: item.id,
                                        contrato_id: contextoImportador.contrato_id,
                                        modulo_id: contextoImportador.modulo_id,
                                        servico_id: contextoImportador.servico_id,
                                        tema_id: contextoImportador.tema_id,
                                        origem_servico: contextoImportador.origem_servico,
                                    })" type="button" class="btn btn-sm btn-info" title="Abrir importação">
                                        <IconEye />
                                    </Link>

                                    <button v-if="item.status == 1" @click="removerImportacao(item.id)" type="button"
                                        class="btn btn-sm btn-danger" title="Excluir importação">
                                        <IconTrash />
                                    </button>
                                </template>
                            </div>
                        </td>
                    </tr>
                </template>
            </Table>
        </div>

        <ModalErros ref="ModalErrosRef" />
    </AuthenticatedLayout>
</template>
