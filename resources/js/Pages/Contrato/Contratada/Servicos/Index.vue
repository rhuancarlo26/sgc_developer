<template>

    <Head :title="`${contrato.contratada.slice(0, 10)}...`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="w-100 d-flex justify-content-between">
                <Breadcrumb class="align-self-center" :links="[
                    { route: route('contratos.gestao.listagem', contrato.tipo_contrato), label: `Gestão de Contratos` },
                    { route: '#', label: contrato.contratada }
                ]" />

                <div class="container-buttons">
                    <Link class="btn btn-info me-2" :href="route('contratos.contratada.servicos.create', contrato.id)">
                        Cadastrar serviço
                    </Link>
                </div>
            </div>
        </template>

        <Navbar :contrato="contrato">
            <template #body>
                <div class="card card-body mb-3">
                    <div class="row align-items-end g-3">
                        <div class="col-lg-3">
                            <label class="form-label fw-bold">Tema</label>

                            <v-select v-model="filtrosForm.filtro_tema_id" :options="temasFiltro"
                                :reduce="option => option.id" label="nome_tema" placeholder="Selecione o tema">
                                <template #no-options>
                                    Nenhum tema encontrado.
                                </template>
                            </v-select>
                        </div>

                        <div class="col-lg-3">
                            <label class="form-label fw-bold">Serviço</label>

                            <v-select v-model="filtrosForm.filtro_servico_id" :options="servicosFiltro"
                                :reduce="option => option.id" label="nome" placeholder="Selecione o serviço">
                                <template #no-options>
                                    Nenhum serviço encontrado.
                                </template>
                            </v-select>
                        </div>

                        <div class="col-lg-3">
                            <label class="form-label fw-bold">Status</label>

                            <v-select v-model="filtrosForm.filtro_status_aprovacao" :options="statusFiltro"
                                :reduce="option => option.id" label="nome" placeholder="Selecione o status">
                                <template #no-options>
                                    Nenhum status encontrado.
                                </template>
                            </v-select>
                        </div>

                        <div class="col-lg-auto d-flex gap-2">
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

                <Table :columns="['#', 'Tema', 'Serviço', 'Especificação', 'Licença', 'Status', 'Ação']"
                    :records="servicos" table-class="table-hover">
                    <template #body="{ item }">
                        <tr>
                            <td class="text-center">{{ numeroRegistro(item) }}</td>

                            <td class="text-center">{{ item.tema?.nome_tema }}</td>

                            <td>{{ item.tipo?.nome }}</td>

                            <td>{{ item.especificacao }}</td>

                            <td class="text-center">
                                <span @click="abrirModalLicenca(item)" v-if="item.condicionantes.length">
                                    {{ `${item.condicionantes[0]?.licenca?.numero_licenca ?? ''}` }}
                                </span>
                            </td>

                            <td class="text-center">
                                <span v-if="statusAprovacao(item) === 1" class="badge bg-azure-lt">
                                    Em confecção
                                </span>

                                <span v-else-if="statusAprovacao(item) === 2" class="badge bg-yellow-lt">
                                    Em análise
                                </span>

                                <span v-else-if="statusAprovacao(item) === 3" class="badge bg-blue-lt">
                                    Aprovado
                                </span>

                                <span v-else-if="statusAprovacao(item) === 4" class="badge bg-red-lt">
                                    Pendente
                                </span>
                            </td>

                            <td class="text-center w-1">
                                <div class="d-flex gap-2 justify-content-center">
                                    <button type="button" class="btn btn-icon btn-info dropdown-toggle p-2"
                                        data-bs-boundary="viewport" data-bs-toggle="dropdown" aria-expanded="false">
                                        <IconDots />
                                    </button>

                                    <div class="dropdown-menu dropdown-menu-end">
                                        <a @click="abrirModalServico(item)" class="dropdown-item"
                                            href="javascript:void(0)">
                                            Visualizar
                                        </a>

                                        <a v-if="rotaGerenciar(item)" class="dropdown-item" :href="rotaGerenciar(item)">
                                            Gerenciar
                                        </a>

                                        <a class="dropdown-item"
                                            v-if="[1, 4].includes(statusAprovacao(item))"
                                            :href="route('contratos.contratada.servicos.create', { contrato: contrato.id, servico: item.id })">
                                            Editar
                                        </a>

                                        <a @click="deleteServico(item.id)" class="dropdown-item"
                                            href="javascript:void(0)"
                                            v-if="[1, 4].includes(statusAprovacao(item))">
                                            Excluir
                                        </a>

                                        <a @click="enviaFiscal(item.id)" class="dropdown-item" href="javascript:void(0)"
                                            v-if="statusAprovacao(item) === 4">
                                            Parecer
                                        </a>

                                        <a @click="enviaFiscal(item.id)" class="dropdown-item" href="javascript:void(0)"
                                            v-if="[1, 4].includes(statusAprovacao(item))">
                                            Enviar para o fiscal
                                        </a>
                                    </div>

                                    <Link v-if="statusAprovacao(item) === 3"
                                        class="btn btn-icon btn-danger p-2" title="Importador do módulo"
                                        target="_blank" rel="noopener" :href="route('modulos.importador.index', {
                                            contrato_id: contrato.id,
                                            modulo_id: item.servico,
                                            servico_id: item.id,
                                            tema_id: item.tema_servico,
                                            origem_servico: true
                                        })">
                                        <IconFileImport />
                                    </Link>
                                </div>
                            </td>
                        </tr>
                    </template>
                </Table>
            </template>
        </Navbar>

        <ModalVisualizarLicenca ref="modalVisualizarLicenca" />
        <ModalVisualizarServico ref="modalVisualizarServico" />
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import Table from "@/Components/Table.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import Navbar from "../Navbar.vue";
import {
    IconDots,
    IconFileImport,
    IconSearch,
    IconX,
} from "@tabler/icons-vue";
import ModalVisualizarLicenca from "./ModalVisualizarLicenca.vue";
import ModalVisualizarServico from "./ModalVisualizarServico.vue";
import ModalVisualizarParecerFiscal from "../../../Fiscal/Servico/ModalVisualizarParecerFiscal.vue";


import { ref } from "vue";
import { can } from "@/Utils/PermissionUtils";

const props = defineProps({
    contrato: Object,
    servicos: Object,
    temasFiltro: {
        type: Array,
        default: () => [],
    },
    servicosFiltro: {
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
    filtro_servico_id: toNumberOrNull(props.filtros?.filtro_servico_id),
    filtro_status_aprovacao: toNumberOrNull(props.filtros?.filtro_status_aprovacao),
});

const statusFiltro = [
    { id: 1, nome: "Em confecção" },
    { id: 2, nome: "Em análise" },
    { id: 3, nome: "Aprovado" },
    { id: 4, nome: "Pendente" },
];

const numeroRegistro = (item) => {
    const index = props.servicos?.data?.findIndex(servico => servico.id === item.id);

    if (index === undefined || index < 0) {
        return "-";
    }

    const inicioPagina = props.servicos?.from ?? 1;

    return inicioPagina + index;
};

const statusAprovacao = (item) => {
    return Number(item?.status_aprovacao);
};

const servicoTipoId = (item) => {
    return Number(item?.servico);
};

const rotaGerenciar = (item) => {
    if (statusAprovacao(item) !== 3) {
        return null;
    }

    const params = {
        contrato: props.contrato.id,
        servico: item.id,
    };

    const rotas = {
        1: "contratos.contratada.servicos.pmqa.configuracao.ponto.index",
        2: "contratos.contratada.servicos.afugentamento.resgate.fauna.configuracao.vincular.asv.index",
        3: "contratos.contratada.servicos.mon_atp_fauna.configuracoes.vincular_abio.index",
        4: "contratos.contratada.servicos.monitora_fauna.configuracoes.vincular_abio.index",
        5: "contratos.contratada.servicos.passagem_fauna.configuracao.vincular_abio.index",
        6: "contratos.contratada.servicos.supressao-vegetacao.configuracao.vincular-asv.index",
        7: "contratos.contratada.servicos.cont_ocorrencia.configuracao.empreendimento.index",
    };

    const nomeRota = rotas[servicoTipoId(item)];

    return nomeRota ? route(nomeRota, params) : null;
};

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
        filtro_servico_id: filtrosForm.value.filtro_servico_id,
        filtro_status_aprovacao: filtrosForm.value.filtro_status_aprovacao,
    });
};

const pesquisar = () => {
    router.get(
        route("contratos.contratada.servicos.index", props.contrato.id),
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
        filtro_servico_id: null,
        filtro_status_aprovacao: null,
    };

    router.get(
        route("contratos.contratada.servicos.index", props.contrato.id),
        montarParametros(false),
        {
            preserveScroll: true,
            preserveState: false,
        }
    );
};

const modalVisualizarLicenca = ref();
const modalVisualizarServico = ref();
const modalVisualizarParecerFiscal = ref();

const abrirModalLicenca = (servico) => {
    modalVisualizarLicenca.value.abrirModal(servico);
};

const abrirModalServico = (servico) => {
    modalVisualizarServico.value.abrirModal(servico);
};

const deleteServico = (servico_id) => {
    router.delete(route("contratos.contratada.servicos.delete", servico_id));
};

const enviaFiscal = (servico_id) => {
    router.post(route("contratos.contratada.servicos.envia-fiscal", servico_id));
};
</script>
