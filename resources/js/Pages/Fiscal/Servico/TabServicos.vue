<template>

    <Head :title="`${contrato.contratada.slice(0, 10)}...`" />

    <Navbar :contrato="contrato">
        <template #body>

            <!-- Pesquisa-->
            <div class="card card-body mb-3">
                <div class="row align-items-end g-3">
                    <div class="col-lg-2">
                        <label class="form-label fw-bold">Tema</label>

                        <v-select v-model="filtrosForm.filtro_tema_id" :options="temasFiltro"
                            :reduce="option => option.id" label="nome_tema" placeholder="Selecione o tema">
                            <template #no-options>
                                Nenhum tema encontrado.
                            </template>
                        </v-select>
                    </div>

                    <div class="col-lg-2">
                        <label class="form-label fw-bold">Serviço</label>

                        <input v-model="filtrosForm.filtro_servico" type="text" class="form-control"
                            placeholder="Digite o serviço" @keyup.enter="pesquisar" />
                    </div>

                    <div class="col-lg-3">
                        <label class="form-label fw-bold">Especificação</label>

                        <input v-model="filtrosForm.filtro_especificacao" type="text" class="form-control"
                            placeholder="Digite a especificação" @keyup.enter="pesquisar" />
                    </div>

                    <div class="col-lg-2">
                        <label class="form-label fw-bold">Licença</label>

                        <input v-model="filtrosForm.filtro_licenca" type="text" class="form-control"
                            placeholder="Nº da licença" @keyup.enter="pesquisar" />
                    </div>

                    <div class="col-lg-2">
                        <label class="form-label fw-bold">Status</label>

                        <v-select v-model="filtrosForm.filtro_status_aprovacao" :options="statusFiltro"
                            :reduce="option => option.id" label="nome" placeholder="Selecione o status">
                            <template #no-options>
                                Nenhum status encontrado.
                            </template>
                        </v-select>
                    </div>

                    <div class="col-lg-1 d-flex gap-2">
                        <button type="button" class="btn btn-primary" title="Pesquisar" @click="pesquisar">
                            <IconSearch />
                        </button>

                        <button type="button" class="btn btn-outline-danger" title="Limpar filtros"
                            @click="limparFiltros">
                            <IconX />
                        </button>
                    </div>
                </div>
            </div>

            <!-- Listagem-->
            <Table :columns="['#', 'Tema', 'Serviço', 'Especificação', 'Licença', 'Status', 'Ação']" :records="servicos"
                table-class="table-hover">
                <template #body="{ item }">
                    <tr>
                        <td class="text-center">
                            {{ numeroRegistro(item) }}
                        </td>
                        <td>{{ item.tema?.nome_tema }}</td>
                        <td>
                            {{ nomeServicoExibicao(item) }}
                        </td>
                        <td>{{ item.especificacao }}</td>
                        <td>
                            <span v-if="item.condicionantes?.length" class="cursor-pointer text-primary"
                                @click="abrirModalLicenca(item)">
                                {{ item.condicionantes[0]?.licenca?.numero_licenca ?? '' }}
                            </span>
                        </td>
                        <td class="text-center">
                            <span v-if="statusAprovacao(item) === 2" class="badge bg-yellow-lt">
                                Em análise
                            </span>
                            <span v-else-if="statusAprovacao(item) === 3" class="badge bg-blue-lt">
                                Aprovado
                            </span>
                            <span v-else-if="statusAprovacao(item) === 4" class="badge bg-red-lt">
                                Pendente
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="d-flex gap-2 justify-content-center">
                                <button type="button" class="btn btn-icon btn-info dropdown-toggle p-2"
                                    data-bs-boundary="viewport" data-bs-toggle="dropdown" aria-expanded="false">
                                    <IconDots />
                                </button>

                                <div class="dropdown-menu dropdown-menu-end">
                                    <a @click="abrirModalServicoFiscal(item)" class="dropdown-item"
                                        href="javascript:void(0)">
                                        Visualizar serviço
                                    </a>

                                    <a @click="abrirModalParecerFiscal(item)" class="dropdown-item"
                                        href="javascript:void(0)">
                                        Parecer
                                    </a>
                                </div>

                                <Link v-if="temModuloImportador(item)" class="btn btn-icon btn-danger p-2"
                                    title="Visualizar módulo importador" target="_blank" rel="noopener"
                                    :href="rotaImportadorFiscal(item)">
                                    <IconFileImport />
                                </Link>
                            </div>
                        </td>
                    </tr>
                </template>
            </Table>
        </template>
    </Navbar>

    <ModalVisualizarParecerFiscal ref="modalVisualizarParecerFiscal" />
    <ModalVisualizarServicoFiscal ref="modalVisualizarServicoFiscal" />
    <ModalVisualizarLicenca ref="modalVisualizarLicenca" />

</template>

<script setup>
import Table from "@/Components/Table.vue";
import Navbar from "../Navbar.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { IconDots, IconFileImport, IconSearch, IconX } from "@tabler/icons-vue";
import ModalVisualizarParecerFiscal from "./ModalVisualizarParecerFiscal.vue";
import ModalVisualizarServicoFiscal from "./ModalVisualizarServicoFiscal.vue";
import ModalVisualizarLicenca from "@/Pages/Contrato/Contratada/Servicos/ModalVisualizarLicenca.vue";
import { ref } from "vue";

const props = defineProps({
    contrato: Object,
    servicos: Object,
    temasFiltro: {
        type: Array,
        default: () => [],
    },
    statusFiltro: {
        type: Array,
        default: () => [],
    },
    filtros: {
        type: Object,
        default: () => ({}),
    },
});

const toNumberOrNull = (valor) => {
    return valor === null || valor === undefined || valor === ""
        ? null
        : Number(valor);
};

const filtrosForm = ref({
    filtro_tema_id: toNumberOrNull(props.filtros?.filtro_tema_id),
    filtro_servico: props.filtros?.filtro_servico ?? "",
    filtro_especificacao: props.filtros?.filtro_especificacao ?? "",
    filtro_licenca: props.filtros?.filtro_licenca ?? "",
    filtro_status_aprovacao: toNumberOrNull(props.filtros?.filtro_status_aprovacao),
});

const limparParametrosVazios = (params) => {
    return Object.fromEntries(
        Object.entries(params).filter(([_, value]) => {
            return value !== null && value !== undefined && value !== "";
        })
    );
};

const montarParametros = (comFiltros = true) => {
    if (!comFiltros) {
        return {};
    }

    return limparParametrosVazios({
        filtro_tema_id: filtrosForm.value.filtro_tema_id,
        filtro_servico: filtrosForm.value.filtro_servico,
        filtro_especificacao: filtrosForm.value.filtro_especificacao,
        filtro_licenca: filtrosForm.value.filtro_licenca,
        filtro_status_aprovacao: filtrosForm.value.filtro_status_aprovacao,
    });
};

const pesquisar = () => {
    router.get(
        route("fiscal.dados.servicos.index", props.contrato.id),
        montarParametros(true),
        {
            preserveScroll: true,
            preserveState: true,
        }
    );
};

const limparFiltros = () => {
    filtrosForm.value = {
        filtro_tema_id: null,
        filtro_servico: "",
        filtro_especificacao: "",
        filtro_licenca: "",
        filtro_status_aprovacao: null,
    };

    router.get(
        route("fiscal.dados.servicos.index", props.contrato.id),
        montarParametros(false),
        {
            preserveScroll: true,
            preserveState: false,
        }
    );
};

const modalVisualizarParecerFiscal = ref();
const modalVisualizarServicoFiscal = ref();

const abrirModalParecerFiscal = (item) => {
    modalVisualizarParecerFiscal.value.abrirModal(item);
}

const abrirModalServicoFiscal = (item) => {
    modalVisualizarServicoFiscal.value.abrirModal(item);
}

const modalVisualizarLicenca = ref();

const abrirModalLicenca = (item) => {
    modalVisualizarLicenca.value?.abrirModal?.(item);
};

const numeroRegistro = (item) => {
    const lista = props.servicos?.data ?? props.servicos ?? [];

    const index = lista.findIndex((servico) => servico.id === item.id);

    if (index < 0) {
        return "-";
    }

    const inicioPagina = props.servicos?.from ?? 1;

    return inicioPagina + index;
};

const numeroContratoModulo = (modulo) => {
    if (modulo?.contrato?.numero_contrato) {
        return modulo.contrato.numero_contrato;
    }

    if (Number(modulo?.contrato_id) === Number(props.contrato?.id)) {
        return props.contrato?.numero_contrato;
    }

    return null;
};

const nomeModuloFormatado = (modulo) => {
    const nome = modulo?.nome ?? "-";
    const ehPmqa = modulo?.pmqa === true || Number(modulo?.pmqa) === 1;

    if (ehPmqa) {
        return `${nome} | ${numeroContratoModulo(modulo) ?? "Contrato não informado"}`;
    }

    return nome;
};

const nomeServicoExibicao = (item) => {
    const possuiModuloImportado =
        item?.servico_mod_imp_id !== null
        && item?.servico_mod_imp_id !== undefined
        && item?.servico_mod_imp_id !== "";

    if (possuiModuloImportado) {
        return nomeModuloFormatado(item?.modulo_importado);
    }

    if (item?.tipo?.nome) {
        return item.tipo.nome;
    }

    if (
        item?.servico !== null
        && item?.servico !== undefined
        && item?.servico !== ""
    ) {
        return String(item.servico);
    }

    return "-";
};

const temModuloImportador = (item) => {
    return item?.servico_mod_imp_id !== null
        && item?.servico_mod_imp_id !== undefined
        && item?.servico_mod_imp_id !== ""
        && !!item?.modulo_importado;
};

const statusAprovacao = (item) => {
    return Number(item?.status_aprovacao);
};

const rotaImportadorFiscal = (item) => {
    return route("modulos.importador.index", {
        contrato_id: props.contrato.id,
        modulo_id: item.servico_mod_imp_id,
        servico_id: item.id,
        tema_id: item.tema_servico,
        origem_servico: true,
        origem_fiscal: true,
        modo_fiscal: true,
    });
};
</script>
