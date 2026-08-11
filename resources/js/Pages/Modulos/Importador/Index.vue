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
    IconDownload,
    IconFileCertificate,
} from "@tabler/icons-vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import Table from "@/Components/Table.vue";
import { dateTimeFormat } from "@/Utils/DateTimeUtils";
import { badgeStatus } from "@/Utils/ImportadorUtils";
import Modal from "@/Components/Modal.vue";

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
    filtro_modulo_id: toNumberOrNull(
        props.filtros?.filtro_modulo_id ?? props.contextoImportador?.modulo_id
    ),
    filtro_tema_id: toNumberOrNull(
        props.filtros?.filtro_tema_id ?? props.contextoImportador?.tema_id
    ),
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

const veioDoServico = computed(() => {
    return !!props.contextoImportador?.origem_servico;
});

const filtroTemaBloqueado = computed(() => {
    return veioDoServico.value && !!props.contextoImportador?.tema_id;
});

const filtroModuloBloqueado = computed(() => {
    return veioDoServico.value && !!props.contextoImportador?.modulo_id;
});

const nomeModuloFormatado = (modulo) => {
    const nome = modulo?.nome ?? "-";

    const ehPmqa = modulo?.pmqa === true || Number(modulo?.pmqa) === 1;

    if (ehPmqa) {
        return `${nome} | ${modulo?.contrato?.numero_contrato ?? "Contrato não informado"}`;
    }

    return nome;
};

const modulosFiltroFormatados = computed(() => {
    return props.modulosFiltro.map((modulo) => {
        return {
            ...modulo,
            nome_formatado: nomeModuloFormatado(modulo),
        };
    });
});

const temaSelecionadoContexto = computed(() => {
    if (props.contextoImportador?.tema) {
        return props.contextoImportador.tema;
    }

    const temaId = Number(props.contextoImportador?.tema_id);

    return props.temasFiltro.find((tema) => Number(tema.id) === temaId) ?? null;
});

const moduloSelecionadoContexto = computed(() => {
    if (props.contextoImportador?.modulo) {
        return {
            ...props.contextoImportador.modulo,
            nome_formatado: nomeModuloFormatado(props.contextoImportador.modulo),
        };
    }

    const moduloId = Number(props.contextoImportador?.modulo_id);

    return modulosFiltroFormatados.value.find((modulo) => Number(modulo.id) === moduloId) ?? null;
});

const mostrarCabecalhoContexto = computed(() => {
    return veioDoServico.value;
});

const nomeTemaContexto = computed(() => {
    return temaSelecionadoContexto.value?.nome_tema ?? "tema não informado";
});

const nomeModuloContexto = computed(() => {
    return moduloSelecionadoContexto.value?.nome_formatado ?? "módulo não informado";
});

const podeBaixarModeloVinculado = computed(() => {
    return veioDoServico.value && !!props.contextoImportador?.modulo_id;
});

const linkModeloPlanilha = computed(() => {
    if (!podeBaixarModeloVinculado.value) {
        return "#";
    }

    return route("modulos.config-modulos.gerar-planilha-modelo", [
        props.contextoImportador.modulo_id,
    ]);
});

const ModalLicencasRef = ref(null);
const importadorLicencasSelecionado = ref(null);

const abrirModalLicencas = (item) => {
    importadorLicencasSelecionado.value = item;
    ModalLicencasRef.value?.getBsModal?.()?.show();
};

const licencasVinculadas = computed(() => {
    return importadorLicencasSelecionado.value?.licencas ?? [];
});

const colunasFiltroLicenca = "tipo_rel.sigla,numero_licenca,empreendimento,data_emissao,status,vencimento,processo_dnit";

const linkLicenca = (licenca) => {
    return `/licenca?columns=${encodeURIComponent(colunasFiltroLicenca)}&value=${encodeURIComponent(licenca.numero_licenca)}`;
};

const formatarData = (data) => {
    if (!data) {
        return "-";
    }

    const partes = String(data).substring(0, 10).split("-");

    if (partes.length !== 3) {
        return data;
    }

    return `${partes[2]}/${partes[1]}/${partes[0]}`;
};

const textoTipoLicenca = (licenca) => {
    return licenca?.tipo_rel?.sigla
        ?? licenca?.tipo_sigla
        ?? licenca?.tipo
        ?? "-";
};

const textoEmpreendimentoLicenca = (licenca) => {
    if (licenca?.empreendimento) {
        return licenca.empreendimento;
    }

    const trecho = [
        licenca?.inicio_subtrecho,
        licenca?.fim_subtrecho,
    ].filter(Boolean).join(" a ");

    return trecho || "-";
};

const textoEmissorLicenca = (licenca) => {
    return licenca?.emissor
        ?? licenca?.emissor_rel?.nome
        ?? licenca?.orgao_emissor?.nome
        ?? "-";
};

const textoStatusLicenca = (licenca) => {
    return licenca?.status_formatado
        ?? licenca?.status
        ?? "-";
};

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
        filtro_modulo_id: filtroModuloBloqueado.value
            ? toNumberOrNull(props.contextoImportador?.modulo_id)
            : null,
        filtro_tema_id: filtroTemaBloqueado.value
            ? toNumberOrNull(props.contextoImportador?.tema_id)
            : null,
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

            <div v-if="mostrarCabecalhoContexto" class="mb-4">
                <h2 class="card-title mb-2">
                    Módulo Importador
                </h2>

                <p class="text-muted mb-0">
                    <strong>Tema:</strong> {{ nomeTemaContexto }}
                    <span class="mx-2">|</span>
                    <strong>Módulo:</strong> {{ nomeModuloContexto }}
                </p>
            </div>

            <div class="row align-items-end mb-3">
                <div class="col-lg-3">
                    <label class="form-label fw-bold">Tema</label>

                    <v-select v-model="filtrosForm.filtro_tema_id" :options="temasFiltro" :reduce="option => option.id"
                        label="nome_tema" placeholder="Selecione o tema" :disabled="filtroTemaBloqueado">
                        <template #no-options>
                            Nenhum tema encontrado.
                        </template>
                    </v-select>
                </div>

                <div class="col-lg-3">
                    <label class="form-label fw-bold">Módulo</label>

                    <v-select v-model="filtrosForm.filtro_modulo_id" :options="modulosFiltroFormatados"
                        :reduce="option => option.id" label="nome_formatado" placeholder="Selecione o módulo"
                        :disabled="filtroModuloBloqueado">
                        <template #no-options>
                            Nenhum módulo encontrado.
                        </template>
                    </v-select>
                </div>

                <div class="col-lg-3">
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

                <div class="d-flex justify-content-between col-1">
                    <button type="button" class="btn btn-outline-primary" title="Pesquisar" @click="pesquisar">
                        <IconSearch />
                    </button>

                    <button type="button" class="btn btn-outline-danger" title="Limpar filtros" @click="limparFiltros">
                        <IconX />
                    </button>
                </div>

            </div>
            <div class="row mb-3">
                <div class="col-lg-auto ms-auto d-flex justify-content-end gap-2">
                    <a v-if="podeBaixarModeloVinculado" :href="linkModeloPlanilha" class="btn btn-info" target="_blank"
                        title="Baixar modelo da planilha vinculada ao serviço">
                        <IconDownload class="me-2" />
                        Baixar modelo
                    </a>

                    <Link :href="route('modulos.importador.formulario', contextoFormulario)" class="btn btn-success">
                        <IconCirclePlus class="me-2" />
                        {{ veioDoServico ? 'Importar planilha' : 'Nova Importação' }}
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
                            {{ item.servico?.tema?.nome_tema ?? '-' }}
                        </td>
                        <td class="text-center">
                            {{ nomeModuloFormatado(item.modulo) }}
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
                                        contrato_id: contextoImportador.contrato_id ?? item.contrato_id,
                                        modulo_id: contextoImportador.modulo_id ?? item.modulo_id,
                                        servico_id: contextoImportador.servico_id ?? item.servico_id,
                                        tema_id: contextoImportador.tema_id ?? item.servico?.tema_servico,
                                        origem_servico: contextoImportador.origem_servico ?? !!item.servico_id,
                                    })" type="button" class="btn btn-sm btn-info" title="Abrir importação">
                                        <IconEye />
                                    </Link>

                                    <button v-if="item.licencas?.length" type="button" class="btn btn-sm btn-secondary"
                                        title="Licenças vinculadas" @click="abrirModalLicencas(item)">
                                        <IconFileCertificate />
                                    </button>

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

        <Modal ref="ModalLicencasRef" title="Licenças vinculadas à entrega" modal-dialog-class="modal-xl">
            <template #body>
                <div v-if="!licencasVinculadas.length" class="alert alert-info mb-0">
                    Nenhuma licença vinculada a esta entrega.
                </div>

                <div v-else class="table-responsive">
                    <table class="table table-bordered table-hover mb-0">
                        <thead>
                            <tr>
                                <th class="text-center">Tipo</th>
                                <th class="text-center">Nº Licença</th>
                                <th class="text-center">Empreendimento</th>
                                <th class="text-center">Emissor</th>
                                <th class="text-center">Data da emissão</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Vencimento</th>
                                <th class="text-center">Processo DNIT</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr v-for="licenca in licencasVinculadas" :key="licenca.id">
                                <td class="text-center">
                                    {{ textoTipoLicenca(licenca) }}
                                </td>

                                <td class="text-center">
                                    <a :href="linkLicenca(licenca)" target="_blank"
                                        class="text-primary text-decoration-underline fw-bold"
                                        title="Abrir licença em nova aba">
                                        {{ licenca.numero_licenca }}
                                    </a>
                                </td>

                                <td>
                                    {{ textoEmpreendimentoLicenca(licenca) }}
                                </td>

                                <td>
                                    {{ textoEmissorLicenca(licenca) }}
                                </td>

                                <td class="text-center">
                                    {{ formatarData(licenca.data_emissao) }}
                                </td>

                                <td class="text-center">
                                    {{ textoStatusLicenca(licenca) }}
                                </td>

                                <td class="text-center">
                                    {{ formatarData(licenca.vencimento) }}
                                </td>

                                <td>
                                    {{ licenca.processo_dnit ?? '-' }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </template>

            <template #footer>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Fechar
                </button>
            </template>
        </Modal>


    </AuthenticatedLayout>
</template>
