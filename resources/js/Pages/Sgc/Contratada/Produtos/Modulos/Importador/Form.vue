<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import { ref, computed } from "vue";
import { IconDoorExit, IconDeviceFloppy, IconSend, IconCircleX, IconCircleCheck } from "@tabler/icons-vue";
import Swal from "sweetalert2";

import CardInformacoesGerais from "./Components/CardInformacoesGerais.vue";
import CardPareceres from "./Components/CardPareceres.vue";
import CardFotos from "./Components/CardFotos.vue";
import CardAnexos from "./Components/CardAnexos.vue";
import CardDadosPlanilha from "./Components/CardDadosPlanilha.vue";

// import { badgeStatus } from "@/Utils/ImportadorUtils";

const props = defineProps({
    moduloImportador: {
        type: Object,
        default: () => ({}),
    },
    modulos: {
        type: Array,
        default: () => [],
    },
    contratos: {
        type: Array,
        default: () => [],
    },
    contextoImportador: {
        type: Object,
        default: () => ({}),
    },
    campanhasDisponiveis: {
        type: Array,
        default: () => [],
    },
});

const toNumberOrNull = (valor) => {
    return valor === null || valor === undefined || valor === ""
        ? null
        : Number(valor);
};

const labelBreadcrumb = computed(() => props.moduloImportador?.id ? "Importação" : "Nova Importação");

const form = useForm({
    mes_ano_referencia: null,
    campanha: null,
    status: null,
    arquivo: null,
    parecer_tecnico: null,
    parecer_analise: null,
    fotos: [],
    anexos: [],
    enviar_analise: null,
    update_modulo: null,
    continuar_formulario: null,

    ...props.moduloImportador,

    modulo_id: toNumberOrNull(props.moduloImportador?.modulo_id ?? props.contextoImportador?.modulo_id),
    contrato_id: toNumberOrNull(props.moduloImportador?.contrato_id ?? props.contextoImportador?.contrato_id),
    servico_id: toNumberOrNull(props.moduloImportador?.servico_id ?? props.contextoImportador?.servico_id),
});

const CardFotosRef = ref(null);
const CardAnexosRef = ref(null);
const temDadosPlanilha = ref(false);
const CardDadosPlanilhaRef = ref(null);
const CardInformacoesGeraisRef = ref(null);

const importandoPlanilha = ref(false);

const montarContextoIndex = () => {
    const params = {};

    const contratoId = form.contrato_id ?? props.contextoImportador?.contrato_id;
    const moduloId = form.modulo_id ?? props.contextoImportador?.modulo_id;
    const servicoId = form.servico_id ?? props.contextoImportador?.servico_id;
    const temaId = props.contextoImportador?.tema_id;
    const origemServico = props.contextoImportador?.origem_servico;

    if (contratoId) {
        params.contrato_id = contratoId;
    }

    if (moduloId) {
        params.modulo_id = moduloId;
    }

    if (servicoId) {
        params.servico_id = servicoId;
    }

    if (temaId) {
        params.tema_id = temaId;
    }

    if (origemServico) {
        params.origem_servico = origemServico;
    }

    return params;
};

const rotaVoltarImportador = computed(() => {
    return route("modulos.importador.index", montarContextoIndex());
});

const arquivoPendenteEmNovoRegistro = computed(() => {
    return !form.id && !!form.arquivo;
});

const limparArquivoSelecionado = () => {
    form.arquivo = null;
    CardInformacoesGeraisRef.value?.limparArquivo?.();
};

const buscarDadosPlanilhaAposImportar = (tentativa = 1) => {
    setTimeout(() => {
        CardDadosPlanilhaRef.value?.buscarDados?.()
            ?.then((resp) => {
                const total = Number(
                    resp?.total
                    ?? resp?.meta?.total
                    ?? resp?.data?.length
                    ?? 0
                );

                if (total > 0) {
                    temDadosPlanilha.value = true;
                    return;
                }

                if (tentativa < 10) {
                    buscarDadosPlanilhaAposImportar(tentativa + 1);
                }
            });
    }, 1000);
};

const importar = async (enviarAnalise = false) => {
    if (form.fotos.length && CardFotosRef.value?.validarCampos?.()) {
        return;
    }

    if (form.anexos.length && CardAnexosRef.value?.validarCampos?.()) {
        return;
    }

    if (
        form.id &&
        Number(form.modulo_id) !== Number(props.moduloImportador?.modulo_id)
    ) {
        let confirmouAlteracaoModulo = false;

        await Swal.fire({
            title: "Tem certeza?",
            text: "O módulo foi alterado, se prosseguir irá excluir todos os dados das planilhas importadas!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Sim, Continuar",
            cancelButtonText: "Cancelar",
        }).then((result) => {
            if (result.isConfirmed) {
                confirmouAlteracaoModulo = true;
                form.update_modulo = true;
            }
        });

        if (!confirmouAlteracaoModulo) {
            return;
        }
    }

    form.enviar_analise = enviarAnalise;

    const method = form.id ? "update" : "store";
    const params = form.id ? [form.id] : [];

    if (temDadosPlanilha.value) {
        form.arquivo = null;
    }

    form.post(route(`modulos.importador.${method}`, params), {
        preserveScroll: true,
        preserveState: true,
        forceFormData: true,

        onSuccess: () => {
            form.arquivo = null;

            CardInformacoesGeraisRef.value?.limparArquivo?.();

            if (form.id) {
                CardDadosPlanilhaRef.value?.buscarDados?.();
            }
        },

        onError: (errors) => {
            tratarErroImportacao(errors);
        },

        onFinish: () => {
            importandoPlanilha.value = false;
        },
    });
};

const enviarAnalise = () => {
    importar(true);
};

const aprovReprovImportacao = (status) => {
    form.post(route("modulos.importador.aprovReprov", [form.id, status]), {
        preserveScroll: true,
    });
};

const tratarErroImportacao = (errors = {}) => {
    const mensagem =
        errors.arquivo
        ?? errors.campanha
        ?? errors.modulo_id
        ?? errors.contrato_id
        ?? "Não foi possível importar a planilha. Verifique se o arquivo está no modelo correto.";

    Swal.fire({
        title: "Erro ao importar",
        text: mensagem,
        icon: "error",
        confirmButtonText: "OK",
    });
};

const importarSomentePlanilha = () => {
    if (form.processing || importandoPlanilha.value) {
        return;
    }

    if (!form.arquivo) {
        form.setError("arquivo", "Selecione uma planilha para importar.");

        Swal.fire({
            title: "Atenção",
            text: "Selecione uma planilha para importar.",
            icon: "warning",
            confirmButtonText: "OK",
        });

        return;
    }

    form.clearErrors("arquivo");

    importandoPlanilha.value = true;

    if (!form.id) {
        form.enviar_analise = false;
        form.continuar_formulario = true;

        form.post(route("modulos.importador.store"), {
            preserveScroll: true,
            preserveState: true,
            forceFormData: true,

            onSuccess: () => {
                limparArquivoSelecionado();
                setTimeout(() => {
                    buscarDadosPlanilhaAposImportar();
                }, 1000);
            },

            onError: (errors) => {
                limparArquivoSelecionado();
                tratarErroImportacao(errors);
            },

            onFinish: () => {
                form.continuar_formulario = null;
                importandoPlanilha.value = false;
            },
        });

        return;
    }

    form.post(route("modulos.importador.importarPlanilha", [form.id]), {
        preserveScroll: true,
        preserveState: true,
        forceFormData: true,

        onSuccess: () => {
            limparArquivoSelecionado();

            temDadosPlanilha.value = true;

            buscarDadosPlanilhaAposImportar();
        },

        onError: (errors) => {
            tratarErroImportacao(errors);
        },

        onFinish: () => {
            importandoPlanilha.value = false;
        },
    });
};
</script>

<template>

    <Head :title="form.id ? 'Importação' : 'Nova Importação'" />

    <AuthenticatedLayout>
        <template #header>
            <div class="w-100 d-flex justify-content-between align-items-center">
                <div class="d-flex gap-3 align-items-center">
                    <Breadcrumb :links="[
                        { route: rotaVoltarImportador, label: `Importadores` },
                        { route: '#', label: labelBreadcrumb }
                    ]" />

                    <span v-if="form.id" class="badge" :class="badgeStatus(form.status)">
                        {{ form.status_formatado }}
                    </span>
                </div>

                <Link class="btn btn-info" :href="rotaVoltarImportador">
                    <IconDoorExit class="me-2" />
                    Voltar
                </Link>
            </div>
        </template>

        <form @submit.prevent="importar()" :disabled="form.processing">
            <div class="d-flex flex-column">
                <div class="d-flex flex-column gap-4 flex-grow-1 mb-4">
                    <CardInformacoesGerais ref="CardInformacoesGeraisRef" :form="form" :modulos="modulos"
                        :contratos="contratos" :tem-dados-planilha="temDadosPlanilha"
                        :contexto-importador="contextoImportador" :campanhas-disponiveis="campanhasDisponiveis"
                        @importar-planilha="importarSomentePlanilha" />

                    <CardDadosPlanilha v-if="form.id" ref="CardDadosPlanilhaRef" :form="form"
                        @tem-dados-changed="temDadosPlanilha = $event" />

                    <CardPareceres :form="form" />

                    <CardFotos :form="form" ref="CardFotosRef" />

                    <CardAnexos :form="form" ref="CardAnexosRef" />
                </div>

                <div class="card-body">
                    <div class="d-flex justify-content-end gap-2">
                        <template v-if="[1, 3, null].includes(form.status)">
                            <button type="button" class="btn btn-light"
                                :disabled="form.processing || importandoPlanilha || arquivoPendenteEmNovoRegistro"
                                @click="importar(false)"
                                :title="arquivoPendenteEmNovoRegistro ? 'Use o botão Importar da planilha antes de salvar.' : 'Salvar Rascunho'">
                                <IconDeviceFloppy class="me-2" />
                                Salvar Rascunho
                            </button>

                            <button type="button" class="btn btn-primary"
                                :disabled="form.processing || importandoPlanilha || arquivoPendenteEmNovoRegistro"
                                @click="enviarAnalise"
                                :title="arquivoPendenteEmNovoRegistro ? 'Use o botão Importar da planilha antes de enviar para análise.' : 'Enviar para Análise'">
                                <IconSend class="me-2" />
                                Enviar para Análise
                            </button>
                        </template>

                        <template v-else-if="[2].includes(form.status)">
                            <button type="button" @click="aprovReprovImportacao(3)" class="btn btn-danger">
                                <IconCircleX class="me-2" />
                                Reprovar
                            </button>

                            <button type="button" @click="aprovReprovImportacao(4)" class="btn btn-success">
                                <IconCircleCheck class="me-2" />
                                Aprovar
                            </button>
                        </template>
                    </div>
                </div>
            </div>
        </form>
    </AuthenticatedLayout>
</template>
