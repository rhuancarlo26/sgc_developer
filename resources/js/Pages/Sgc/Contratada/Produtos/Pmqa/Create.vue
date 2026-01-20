<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import { Head, useForm } from "@inertiajs/vue3";
import ModalImportarPontos from "./Configuracao/Ponto/ModalPontos.vue";
import { ref, watch } from "vue";
import { router } from "@inertiajs/vue3";
import Index from "./Configuracao/Ponto/Index.vue";
import IndexParametros from "./Configuracao/Parametro/Index.vue";
import IndexVincularParametro from "./Configuracao/VinculacaoPonto/Index.vue";
import NavbarContrato from "../../NavbarContrato.vue";
import InputLabel from "@/Components/InputLabel.vue";
import { usePage } from "@inertiajs/vue3";
import { computed } from "vue";
import InputError from "@/Components/InputError.vue";
import { IconDeviceFloppy } from "@tabler/icons-vue";

const props = defineProps({
    contrato: { type: [Object, String, Number], required: true },
    produto: { type: [Object, String], required: true },
    pontos: { type: [Array, Object], default: () => [] },
    contratos: { type: Object, required: true },
    pmqa: { type: Object, required: true },
    empreendimentos: { type: Object },
    parametros: { type: Array, default: () => [] },
    listas: { type: Object },
});
console.log(props.listas);
const temas = [{ id: 1, nome_tema: "Recursos Hídricos" }];

const form = useForm({
    id: props.pmqa?.id ?? null,

    cod_emp: props.pmqa?.cod_emp ?? "",

    tipo: props.produto === "Eia" ? "Eia" : (props.pmqa?.subproduto ?? ""),

    tema: props.pmqa?.tema_id ?? temas[1],

    especificacao: props.pmqa?.especificacao ?? "",
    introducao: props.pmqa?.introducao ?? "",
    justificativa: props.pmqa?.justificativa ?? "",
    objetivos: props.pmqa?.objetivos ?? "",
    metodologia: props.pmqa?.metodologia ?? "",
    publico_alvo: props.pmqa?.publico_alvo ?? "",
});

const page = usePage();
const subStep = ref(Number(page.props.subStep) || 1);

const modalImportarPonto = ref(null);

const activeTab = ref(
    page.props.tab ??
        (Number(page.props.subStep) >= 2 ? "configuracao" : "apresentacao"),
);

const abrirModalImportar = () => {
    modalImportarPonto.value.abrirModal();
};

const abrirModalVisualizar = (item) => {
    modalVisualizarPonto.value.abrirModal(item);
};

const setActiveTab = (tabId) => {
    activeTab.value = tabId;
};

const salvarApresentacao = () => {
    form.patch(
        route("sgc.contratada.produtos.pmqa.update", [props.contrato, "eia"]),
        {
            preserveScroll: true,
            onSuccess: () => {
                subStep.value = 2;
            },
        },
    );
};

const atualizarListaDePontos = () => {
    console.log(
        "Evento 'importacaoConcluida' recebido! A atualizar a lista de pontos...",
    );
    router.reload({
        only: ["pontos"],
        onSuccess: () => {
            // Opcional: mostrar um feedback ao utilizador.
            // alert("A lista de pontos foi atualizada!");
        },
        onError: () => {
            alert("Ocorreu um erro ao atualizar a lista de pontos.");
        },
    });
};

const irParaConfiguracaoPontos = () => {
    if (!campanhaId.value) {
        alert("Campanha PMQA não encontrada.");
        return;
    }

    router.visit(
        route("contratos.contratada.servicos.pmqa.configuracao.ponto.index", {
            campanha: campanhaId.value,
        }),
    );
};

const nextSubStep = () => {
    if (subStep.value === 3 && moduloRecords.value.length === 0) {
        alert("Adicione ao menos um módulo amostral antes de avançar.");
        return;
    }
    if (
        subStep.value === 4 &&
        !naoSeAplicaQuelonios.value &&
        pontoRecords.value.length === 0
    ) {
        alert(
            'Adicione ao menos um ponto de quelônio ou crocodiliano antes de avançar, ou marque "Não se aplica".',
        );
        return;
    }
    if (
        subStep.value === 5 &&
        !naoSeAplicaCavernicola.value &&
        pontoCavernicolaRecords.value.length === 0
    ) {
        alert(
            'Adicione pelo menos um ponto de fauna cavernícola antes de avançar, ou marque "Não se aplica".',
        );
        return;
    }
    if (subStep.value < 5) {
        subStep.value += 1;
    } else {
        setActiveTab("configuracao");
    }
};

const produtoNome = computed(() =>
    typeof props.produto === "string"
        ? props.produto
        : (props.produto?.slug ?? props.produto?.nome),
);

watch(subStep, (val) => {
    if (val >= 2) {
        activeTab.value = "configuracao";
    } else {
        activeTab.value = "apresentacao";
    }
});

const prevSubStep = () => {
    if (subStep.value > 1) {
        subStep.value -= 1;
    }
};

const nextSubStepFromConfiguracao = () => {
    activeTab.value = "configuracao";
    // se está nos pontos (2), vai para parâmetros (3)
    if (subStep.value === 2) subStep.value = 3;
    else if (subStep.value < 5) subStep.value += 1;
};

const prevSubStepFromConfiguracao = () => {
    activeTab.value = "configuracao";
    // se está nos parâmetros (3), volta para pontos (2)
    if (subStep.value === 3) subStep.value = 2;
    else if (subStep.value > 1) subStep.value -= 1;
};

const syncStepToUrl = () => {
    router.replace(
        route(route().current(), {
            ...route().params,
            subStep: subStep.value,
            tab: activeTab.value,
        }),
        { preserveState: true, preserveScroll: true, only: [] },
    );
};

watch(subStep, (val) => {
    // se o usuário escolheu configuracao/resultados/anexos, não força tab
    if (["configuracao", "resultados", "anexos"].includes(activeTab.value))
        return;

    activeTab.value = val >= 2 ? "configuracao" : "apresentacao";
});
</script>

<template>
    <Head :title="`${contratos.contratada.slice(0, 10)}...`" />
    <AuthenticatedLayout>
        <template #header>
            <div class="w-100 d-flex justify-content-between">
                <Breadcrumb
                    class="align-self-center"
                    :links="[
                        {
                            route: route(
                                'contratos.gestao.listagem',
                                contratos.tipo_contrato,
                            ),
                            label: `Gestão de Contratos`,
                        },
                        { route: '#', label: contratos.contratada },
                    ]"
                />
                <!-- <Link
                    class="btn btn-primary"
                    :href="
                        route('contratos.contratada.servicos.index', {
                            contrato: props.contratos.id,
                        })
                    "
                >
                    Voltar
                </Link> -->
            </div>
        </template>

        <NavbarContrato :tipo="contratos">
            <template #body>
                <div class="card">
                    <div class="card-body">
                        <h2 class="text-center mb-4">
                            CADASTRAR {{ produtoNome.toUpperCase() }}
                        </h2>
                        <ul class="nav nav-tabs mb-4">
                            <li class="nav-item">
                                <a
                                    class="nav-link"
                                    :class="{
                                        active: activeTab === 'apresentacao',
                                    }"
                                    @click.prevent="
                                        setActiveTab('apresentacao')
                                    "
                                    >Apresentacao</a
                                >
                            </li>
                            <li class="nav-item">
                                <a
                                    class="nav-link"
                                    :class="{
                                        active: activeTab === 'configuracao',
                                    }"
                                    @click.prevent="
                                        setActiveTab('configuracao')
                                    "
                                    >Configuração</a
                                >
                            </li>
                            <li class="nav-item">
                                <a
                                    class="nav-link"
                                    :class="{
                                        active: activeTab === 'resultados',
                                    }"
                                    @click.prevent="setActiveTab('resultados')"
                                    >Resultados</a
                                >
                            </li>
                            <li class="nav-item">
                                <a
                                    class="nav-link"
                                    :class="{ active: activeTab === 'anexos' }"
                                    @click.prevent="setActiveTab('anexos')"
                                    >Anexos</a
                                >
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div
                                v-if="activeTab === 'apresentacao'"
                                class="tab-pane fade"
                                :class="{
                                    'show active': activeTab === 'apresentacao',
                                }"
                            >
                                <!-- Subetapa 1/5 -->
                                <div v-if="subStep === 1">
                                    <h4 class="mb-3" style="text-align: center">
                                        APRESENTAÇÃO
                                    </h4>
                                    <form @submit.prevent="salvarApresentacao">
                                        <div class="row mb-4">
                                            <div class="col form-group">
                                                <InputLabel
                                                    value="Tema"
                                                    for="tema"
                                                />
                                                <v-select
                                                    :options="temas"
                                                    label="nome_tema"
                                                    :reduce="(t) => t.nome_tema"
                                                    v-model="form.tema"
                                                >
                                                    <template #no-options="{}">
                                                        Nenhum registro
                                                        encontrado.
                                                    </template>
                                                </v-select>
                                                <InputError
                                                    :message="form.errors.tema"
                                                />
                                            </div>
                                            <div class="col form-group">
                                                <InputLabel
                                                    value="Código de Empreendimento"
                                                    for="cod_emp"
                                                />
                                                <v-select
                                                    :options="empreendimentos"
                                                    label="nome"
                                                    v-model="form.cod_emp"
                                                >
                                                    <template #no-options="{}">
                                                        Nenhum registro
                                                        encontrado.
                                                    </template>
                                                </v-select>
                                                <InputError
                                                    :message="form.errors.tipo"
                                                />
                                            </div>
                                        </div>
                                        <div class="row mb-4">
                                            <div class="col form-group">
                                                <InputLabel
                                                    value="Especificação"
                                                    for="especificacao"
                                                />
                                                <textarea
                                                    name="especificacao"
                                                    id="especificacao"
                                                    class="form-control"
                                                    v-model="form.especificacao"
                                                    rows="5"
                                                ></textarea>
                                                <InputError
                                                    :message="
                                                        form.errors
                                                            .especificacao
                                                    "
                                                />
                                            </div>
                                        </div>
                                        <div class="row mb-4">
                                            <div class="col form-group">
                                                <InputLabel
                                                    value="Introdução"
                                                    for="introducao"
                                                />
                                                <textarea
                                                    name="introducao"
                                                    id="introducao"
                                                    class="form-control"
                                                    v-model="form.introducao"
                                                    rows="5"
                                                ></textarea>
                                                <InputError
                                                    :message="
                                                        form.errors.introducao
                                                    "
                                                />
                                            </div>
                                        </div>
                                        <div class="row mb-4">
                                            <div class="col form-group">
                                                <InputLabel
                                                    value="Justificativa"
                                                    for="justificativa"
                                                />
                                                <textarea
                                                    name="justificativa"
                                                    id="justificativa"
                                                    class="form-control"
                                                    v-model="form.justificativa"
                                                    rows="5"
                                                ></textarea>
                                                <InputError
                                                    :message="
                                                        form.errors
                                                            .justificativa
                                                    "
                                                />
                                            </div>
                                        </div>
                                        <div class="row mb-4">
                                            <div class="col form-group">
                                                <InputLabel
                                                    value="Objetivos"
                                                    for="objetivo"
                                                />
                                                <textarea
                                                    name="objetivo"
                                                    id="objetivo"
                                                    class="form-control"
                                                    v-model="form.objetivos"
                                                    rows="5"
                                                ></textarea>
                                                <InputError
                                                    :message="
                                                        form.errors.objetivos
                                                    "
                                                />
                                            </div>
                                        </div>
                                        <div class="row mb-4">
                                            <div class="col form-group">
                                                <InputLabel
                                                    value="Metodologia"
                                                    for="metodologia"
                                                />
                                                <textarea
                                                    name="metodologia"
                                                    id="metodologia"
                                                    class="form-control"
                                                    v-model="form.metodologia"
                                                    rows="5"
                                                ></textarea>
                                                <InputError
                                                    :message="
                                                        form.errors.metodologia
                                                    "
                                                />
                                            </div>
                                        </div>
                                        <div class="row mb-4">
                                            <div class="col form-group">
                                                <InputLabel
                                                    value="Público alvo"
                                                    for="publico_alvo"
                                                />
                                                <textarea
                                                    name="publico_alvo"
                                                    id="publico_alvo"
                                                    class="form-control"
                                                    v-model="form.publico_alvo"
                                                    rows="5"
                                                ></textarea>
                                                <InputError
                                                    :message="
                                                        form.errors.publico_alvo
                                                    "
                                                />
                                            </div>
                                        </div>
                                        <div
                                            class="mb-4 d-flex justify-content-end"
                                        >
                                            <button
                                                type="submit"
                                                class="btn btn-success"
                                                :disabled="form.processing"
                                            >
                                                <IconDeviceFloppy
                                                    class="me-2"
                                                />
                                                Salvar
                                            </button>
                                        </div>
                                        <!-- <h4 class="text-center mt-4" style="font-weight: bold; color: #6c757d;">{{ subStep }}/5</h4> -->
                                    </form>
                                </div>
                                <!-- <QueloniosCrocodilian
                                    v-if="subStep === 4"
                                    v-model:naoSeAplica="naoSeAplicaQuelonios"
                                    :form-pontos-amostragem="formPontosAmostragem"
                                    :ponto-records="pontoRecords"
                                    @adicionar-ponto="adicionarPonto"
                                    @excluir-ponto="excluirPonto"
                                    @next="nextSubStep"
                                    @prev="prevSubStep"
                                >
                                    <template #footer>
                                        <h4 class="text-center mt-4" style="font-weight: bold; color: #6c757d;">{{ subStep }}/5</h4>
                                    </template>
                                </QueloniosCrocodilian> -->
                                <!-- <FaunaCavernic
                                    v-if="subStep === 5"
                                    v-model:naoSeAplica="naoSeAplicaCavernicola"
                                    :form-pontos-cavernicola="formPontosCavernicola"
                                    :ponto-cavernicola-records="pontoCavernicolaRecords"
                                    @adicionar-ponto-cavernicola="adicionarPontoCavernicola"
                                    @excluir-ponto-cavernicola="excluirPontoCavernicola"
                                    @next="nextSubStep"
                                    @prev="prevSubStep"
                                >
                                    <template #footer>
                                        <h4 class="text-center mt-4" style="font-weight: bold; color: #6c757d;">{{ subStep }}/5</h4>
                                    </template>
                                </FaunaCavernic> -->
                            </div>
                            <div
                                v-if="activeTab === 'configuracao'"
                                class="tab-pane fade"
                                :class="{
                                    'show active': activeTab === 'configuracao',
                                }"
                            >
                                <!-- Configuração - Passo 2: Pontos -->
                                <Index
                                    v-if="subStep === 2"
                                    :contrato="contratos"
                                    :produto="produto"
                                    :pmqa="pmqa"
                                    :pontos="pontos"
                                    @next="nextSubStepFromConfiguracao"
                                    @prev="prevSubStepFromConfiguracao"
                                />

                                <!-- Configuração - Passo 3: Parâmetros -->
                                <IndexParametros
                                    v-else-if="subStep === 3"
                                    :contrato="contratos"
                                    :produto="produto"
                                    :pmqa="pmqa"
                                    :parametros="parametros"
                                    :listas="listas"
                                    @next="nextSubStepFromConfiguracao"
                                    @prev="prevSubStepFromConfiguracao"
                                />

                                <IndexVincularParametro
                                    v-else-if="subStep === 4"
                                    @next="nextSubStepFromConfiguracao"
                                    @prev="prevSubStepFromConfiguracao"
                                />
                                <!-- fallback -->
                                <div v-else class="alert alert-info mb-0">
                                    Selecione uma etapa de configuração.
                                </div>
                            </div>

                            <div
                                v-if="activeTab === 'resultados'"
                                class="tab-pane fade"
                                :class="{
                                    'show active': activeTab === 'resultados',
                                }"
                            >
                                teste
                            </div>
                            <div
                                v-if="activeTab === 'anexos'"
                                class="tab-pane fade"
                                :class="{
                                    'show active': activeTab === 'anexos',
                                }"
                            >
                                teste
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </NavbarContrato>

        <ModalImportarPontos
            :contrato="contrato"
            :produto="produto"
            ref="modalImportarPonto"
            @importacaoConcluida="atualizarListaDePontos"
        />
    </AuthenticatedLayout>
</template>

<style scoped></style>
