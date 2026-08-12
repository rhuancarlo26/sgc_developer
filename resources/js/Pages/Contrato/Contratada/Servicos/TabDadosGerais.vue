<script setup>
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import { useForm, usePage } from "@inertiajs/vue3";
import { computed, watch, ref } from "vue";
import { IconDeviceFloppy } from "@tabler/icons-vue";
import Modal from "@/Components/Modal.vue";
import ModalHistoricoRetornoConfeccao from "./Components/ModalHistoricoRetornoConfeccao.vue";
import Swal from "sweetalert2";

const props = defineProps({
    contrato: Object,
    servico: Object,
    tipos: {
        type: Array,
        default: () => [],
    },
    temas: {
        type: Array,
        default: () => [],
    },
    servicosUsados: {
        type: Array,
        default: () => [],
    },
    modoVoltarConfeccao: {
        type: Boolean,
        default: false,
    },
    podeVoltarConfeccao: {
        type: Boolean,
        default: false,
    },
});

const page = usePage();

const acaoTela = computed(() => {
    const query = page.url.split("?")[1] ?? "";
    const params = new URLSearchParams(query);

    return params.get("acao");
});

const nomeModuloFormatado = (modulo) => {
    const nome = modulo?.nome ?? "-";

    if (modulo?.pmqa) {
        return `${nome} | ${modulo?.contrato?.numero_contrato ?? "Contrato não informado"}`;
    }

    return nome;
};

const prepararModuloSelect = (modulo) => {
    if (!modulo) {
        return null;
    }

    return {
        ...modulo,
        nome_formatado: nomeModuloFormatado(modulo),
    };
};

const moduloPertenceAoContratoAtual = (modulo) => {
    if (!modulo?.pmqa) {
        return true;
    }

    return Number(modulo?.contrato_id) === Number(props.contrato?.id);
};

const temaInicial = props.servico?.tema
    ?? props.temas.find((tema) => Number(tema.id) === Number(props.servico?.tema_servico))
    ?? null;

const moduloImportadoInicial = props.servico?.servico_mod_imp_id
    ? prepararModuloSelect(
        props.servico?.modulo_importado
        ?? props.tipos.find((tipo) => Number(tipo.id) === Number(props.servico?.servico_mod_imp_id))
        ?? null
    )
    : null;

const form = useForm({
    id: null,
    id_contrato: props.contrato?.id,
    tema: null,
    tipo: null,
    especificacao: null,
    introducao: null,
    justificativa: null,
    objetivos: null,
    metodologia: null,
    publico_alvo: null,

    ...props.servico,

    tema: temaInicial,
    tipo: moduloImportadoInicial,
    acao: acaoTela.value,
});

const temaSelecionado = computed(() => {
    return !!(form.tema?.id ?? form.tema ?? form.tema_servico);
});

const servicoSelecionado = computed(() => {
    return !!(form.tipo?.id ?? form.tipo ?? form.servico_mod_imp_id);
});

const registroExistente = computed(() => {
    return !!form.id;
});

const possuiModuloImportadoVinculadoOriginal = computed(() => {
    return !!Number(props.servico?.servico_mod_imp_id);
});

const registroLegadoSemModuloImportado = computed(() => {
    return registroExistente.value && !possuiModuloImportadoVinculadoOriginal.value;
});

const modoVincularServico = computed(() => {
    return registroLegadoSemModuloImportado.value
        && acaoTela.value === "vincular-servico";
});

const modoEditarServico = computed(() => {
    return registroExistente.value
        && acaoTela.value === "editar";
});

const podeEditarServico = computed(() => {
    if (telaRetornoConfeccao.value) {
        return false;
    }

    if (!registroExistente.value) {
        return true;
    }

    if (modoVincularServico.value) {
        return true;
    }

    return false;
});

const podeEditarTema = computed(() => {
    if (telaRetornoConfeccao.value) {
        return false;
    }

    return !registroExistente.value || !form.tema_servico;
});

const modoSomenteVinculo = computed(() => {
    return !telaRetornoConfeccao.value
        && modoVincularServico.value;
});

const podeSalvar = computed(() => {
    if (!temaSelecionado.value || form.processing) {
        return false;
    }

    if (modoVincularServico.value) {
        return servicoSelecionado.value;
    }

    if (registroLegadoSemModuloImportado.value && modoEditarServico.value) {
        return true;
    }

    return servicoSelecionado.value;
});

const modalHistoricoRetornoRef = ref(null);

const temHistoricoRetorno = computed(() => {
    return historicoRetorno.value.length > 0;
});

const abrirHistoricoRetorno = () => {
    modalHistoricoRetornoRef.value?.abrirModal?.(props.servico);
};

const servicosDisponiveis = computed(() => {
    const temaId = Number(form.tema?.id ?? form.tema ?? form.tema_servico);

    if (!temaId) {
        return [];
    }

    const servicoAtualId = Number(form.id ?? props.servico?.id);

    return props.tipos
        .filter((tipo) => moduloPertenceAoContratoAtual(tipo))
        .filter((tipo) => {
            const tipoId = Number(tipo.id);

            return !props.servicosUsados.some((usado) => {
                return Number(usado.tema_servico) === temaId
                    && Number(usado.servico_mod_imp_id) === tipoId
                    && Number(usado.id) !== servicoAtualId;
            });
        })
        .map((tipo) => prepararModuloSelect(tipo));
});

const telaRetornoConfeccao = computed(() => {
    return props.modoVoltarConfeccao
        && props.podeVoltarConfeccao
        && registroExistente.value
        && [3, 4].includes(Number(form.status_aprovacao));
});

const historicoRetorno = computed(() => {
    return props.servico?.retornos_confeccao ?? [];
});

const nomeTemaRetorno = computed(() => {
    return form.tema?.nome_tema
        ?? props.servico?.tema?.nome_tema
        ?? "-";
});

const nomeServicoRetorno = computed(() => {
    return nomeModuloFormatado(
        form.tipo
        ?? props.servico?.modulo_importado
        ?? null
    );
});

const modalRetornoConfeccao = ref(null);

const retornoForm = useForm({
    motivo: "",
});

const abrirModalRetornoConfeccao = () => {
    retornoForm.clearErrors();
    retornoForm.motivo = "";

    modalRetornoConfeccao.value.getBsModal().show();
};

const limparBackdropsBootstrap = () => {
    document.querySelectorAll(".modal-backdrop").forEach((backdrop) => {
        backdrop.remove();
    });

    document.body.classList.remove("modal-open");
    document.body.style.removeProperty("overflow");
    document.body.style.removeProperty("padding-right");
};

const confirmarRetornoConfeccao = async () => {
    retornoForm.clearErrors();

    if (!retornoForm.motivo || !retornoForm.motivo.trim()) {
        retornoForm.setError("motivo", "Informe o motivo para voltar para em confecção.");
        return;
    }

    const result = await Swal.fire({
        title: "Voltar para em confecção?",
        text: "O serviço voltará para o status Em confecção e o motivo ficará registrado no histórico.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sim, confirmar",
        cancelButtonText: "Cancelar",
        confirmButtonColor: "#d33",
    });

    if (!result.isConfirmed) {
        return;
    }

    const modal = modalRetornoConfeccao.value?.getBsModal?.();

    if (modal) {
        modal.hide();
    }

    setTimeout(() => {
        retornoForm.post(route("contratos.contratada.servicos.voltar-confeccao", form.id), {
            preserveScroll: false,

            onError: () => {
                modalRetornoConfeccao.value?.getBsModal?.()?.show();
            },

            onSuccess: () => {
                limparBackdropsBootstrap();
            },

            onFinish: () => {
                limparBackdropsBootstrap();
            },
        });
    }, 150);
};

const textoStatus = (status) => {
    const statusNumber = Number(status);

    if (statusNumber === 1) {
        return "Em confecção";
    }

    if (statusNumber === 2) {
        return "Em análise";
    }

    if (statusNumber === 3) {
        return "Aprovado";
    }

    if (statusNumber === 4) {
        return "Pendente/Reprovado";
    }

    return "-";
};

watch(
    () => form.tema?.id ?? form.tema ?? form.tema_servico,
    (novoTema, temaAnterior) => {
        if (temaAnterior !== undefined && Number(novoTema) !== Number(temaAnterior)) {
            form.tipo = null;
            form.servico_mod_imp_id = null;
        }
    }
);

watch(
    () => form.tipo,
    (novoServico) => {
        form.servico_mod_imp_id = novoServico?.id ?? novoServico ?? null;
    }
);

const scrollParaTopo = () => {
    setTimeout(() => {
        window.scrollTo({
            top: 0,
            behavior: "smooth",
        });
    }, 100);
};

const salvarServico = () => {
    if (!podeSalvar.value) {
        return;
    }

    form.acao = acaoTela.value;

    form.id_contrato = props.contrato?.id;
    form.tema_servico = form.tema?.id ?? form.tema ?? form.tema_servico;
    form.servico_mod_imp_id = form.tipo?.id ?? form.tipo ?? form.servico_mod_imp_id;

    if (form.id) {
        form.patch(route("contratos.contratada.servicos.update"), {
            preserveScroll: false,
            preserveState: true,
            onSuccess: () => {
                scrollParaTopo();
            },
        });
    } else {
        form.post(route("contratos.contratada.servicos.store"), {
            preserveScroll: false,
            preserveState: false,
            onSuccess: () => {
                scrollParaTopo();
            },
        });
    }
};
</script>

<template>
    <form @submit.prevent="salvarServico()">
        <div class="row mb-4">
            <div class="col form-group">
                <InputLabel for="tema">
                    <span>Tema <span class="text-danger">*</span></span>
                </InputLabel>

                <v-select :options="temas" label="nome_tema" v-model="form.tema"
                    :disabled="form.processing || !podeEditarTema">
                    <template #no-options="{ }">
                        Nenhum registro encontrado.
                    </template>
                </v-select>

                <InputError :message="form.errors.tema" />
            </div>

            <div class="col form-group">
                <InputLabel for="servico">
                    <span>Serviço (módulo importado) <span class="text-danger">*</span></span>
                </InputLabel>

                <v-select :options="servicosDisponiveis" label="nome_formatado" v-model="form.tipo"
                    :disabled="!temaSelecionado || form.processing || !podeEditarServico"
                    :placeholder="!temaSelecionado ? 'Selecione um tema para habilitar o campo de serviço' : 'Selecione o serviço'">
                    <template #no-options="{ }">
                        Nenhum módulo encontrado.
                    </template>
                </v-select>

                <small v-if="registroLegadoSemModuloImportado" class="text-warning d-block mt-1">
                    Este registro ainda não possui serviço do módulo importado vinculado.
                </small>

                <InputError :message="form.errors.tipo" />
            </div>
        </div>

        <div class="row mb-4">
            <div class="col form-group">
                <InputLabel value="Especificação" for="especificacao" />
                <textarea :disabled="modoSomenteVinculo || telaRetornoConfeccao" name="especificacao" id="especificacao"
                    class="form-control" v-model="form.especificacao" rows="5"></textarea>
                <InputError :message="form.errors.especificacao" />
            </div>
        </div>

        <div class="row mb-4">
            <div class="col form-group">
                <InputLabel value="Introdução" for="introducao" />
                <textarea :disabled="modoSomenteVinculo || telaRetornoConfeccao" name="introducao" id="introducao"
                    class="form-control" v-model="form.introducao" rows="5"></textarea>
                <InputError :message="form.errors.introducao" />
            </div>
        </div>

        <div class="row mb-4">
            <div class="col form-group">
                <InputLabel value="Justificativa" for="justificativa" />
                <textarea :disabled="modoSomenteVinculo || telaRetornoConfeccao" name="justificativa" id="justificativa"
                    class="form-control" v-model="form.justificativa" rows="5"></textarea>
                <InputError :message="form.errors.justificativa" />
            </div>
        </div>

        <div class="row mb-4">
            <div class="col form-group">
                <InputLabel value="Objetivos" for="objetivo" />
                <textarea :disabled="modoSomenteVinculo || telaRetornoConfeccao" name="objetivo" id="objetivo"
                    class="form-control" v-model="form.objetivos" rows="5"></textarea>
                <InputError :message="form.errors.objetivos" />
            </div>
        </div>

        <div class="row mb-4">
            <div class="col form-group">
                <InputLabel value="Metodologia" for="metodologia" />
                <textarea :disabled="modoSomenteVinculo || telaRetornoConfeccao" name="metodologia" id="metodologia"
                    class="form-control" v-model="form.metodologia" rows="5"></textarea>
                <InputError :message="form.errors.metodologia" />
            </div>
        </div>

        <div class="row mb-4">
            <div class="col form-group">
                <InputLabel value="Público alvo" for="publico_alvo" />
                <textarea :disabled="modoSomenteVinculo || telaRetornoConfeccao" name="publico_alvo" id="publico_alvo"
                    class="form-control" v-model="form.publico_alvo" rows="5"></textarea>
                <InputError :message="form.errors.publico_alvo" />
            </div>
        </div>

        <div class="mb-4 d-flex justify-content-between gap-2">
            <button v-if="temHistoricoRetorno" type="button" class="btn btn-outline-warning"
                @click="abrirHistoricoRetorno">
                Histórico de retorno para em confecção
            </button>

            <div class="d-flex gap-2 ms-auto">
                <button v-if="telaRetornoConfeccao" type="button" class="btn btn-warning"
                    @click="abrirModalRetornoConfeccao">
                    Voltar para em confecção
                </button>

                <button v-else type="submit" class="btn btn-success" :disabled="!podeSalvar" :title="!podeSalvar
                    ? (modoVincularServico
                        ? 'Selecione o serviço para realizar o vínculo'
                        : 'Preencha os campos obrigatórios para salvar')
                    : 'Salvar'
                    ">
                    <IconDeviceFloppy class="me-2" />
                    Salvar
                </button>
            </div>
        </div>
    </form>

    <Modal ref="modalRetornoConfeccao" title="Voltar serviço para em confecção" modal-dialog-class="modal-lg">
        <template #body>
            <div class="row mb-3">
                <div class="col-md-6">
                    <InputLabel value="Tema" />
                    <input type="text" class="form-control" :value="nomeTemaRetorno" disabled />
                </div>

                <div class="col-md-6">
                    <InputLabel value="Serviço (módulo importado)" />
                    <input type="text" class="form-control" :value="nomeServicoRetorno" disabled />
                </div>
            </div>

            <div class="mb-3">
                <InputLabel for="motivo_retorno">
                    <span>Motivo <span class="text-danger">*</span></span>
                </InputLabel>

                <textarea id="motivo_retorno" class="form-control" rows="5" v-model="retornoForm.motivo"
                    placeholder="Informe o motivo pelo qual este serviço voltará para em confecção"></textarea>

                <InputError :message="retornoForm.errors.motivo" />
            </div>

            <hr />

            <h4 class="mb-3">Histórico de retornos</h4>

            <div v-if="!historicoRetorno.length" class="alert alert-info mb-0">
                Nenhum retorno para confecção registrado até o momento.
            </div>

            <div v-else class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Usuário</th>
                            <th>Status anterior</th>
                            <th>Status novo</th>
                            <th>Motivo</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr v-for="historico in historicoRetorno" :key="historico.id">
                            <td>{{ historico.created_at }}</td>
                            <td>{{ historico.usuario?.name ?? '-' }}</td>
                            <td>{{ textoStatus(historico.status_anterior) }}</td>
                            <td>{{ textoStatus(historico.status_novo) }}</td>
                            <td>{{ historico.motivo }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </template>

        <template #footer>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" :disabled="retornoForm.processing">
                Cancelar
            </button>

            <button type="button" class="btn btn-warning" :disabled="retornoForm.processing"
                @click="confirmarRetornoConfeccao">
                Confirmar retorno
            </button>
        </template>
    </Modal>

    <ModalHistoricoRetornoConfeccao ref="modalHistoricoRetornoRef" :contrato-id="contrato.id" />

</template>
